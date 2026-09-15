<?php

namespace App\Http\Controllers;

use App\Models\DeliveryChallan;
use App\Models\Invoice;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DeliveryChallanController extends Controller
{
    public function __construct(private AuditLogService $audit) {}

    public function index(Request $request): View
    {
        $challans = DeliveryChallan::with('customer')->when($request->filled('q'), function ($query) use ($request) {
            $term = trim($request->string('q')->toString());
            $query->where(fn ($q) => $q->where('challan_number','like',"%{$term}%")->orWhereHas('customer', fn ($cq) => $cq->where('company_name','like',"%{$term}%")));
        })->latest('challan_date')->latest('id')->paginate(20)->withQueryString();
        return view('delivery_challans.index', compact('challans'));
    }

    public function createFromInvoice(Invoice $invoice): View
    {
        $invoice->load(['customer','items.product']);
        abort_unless($invoice->items->isNotEmpty(), 422, 'The invoice has no items to deliver.');
        return view('delivery_challans.create', compact('invoice'));
    }

    public function storeFromInvoice(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->load('items.product');
        $data = $request->validate([
            'challan_date'=>['required','date'], 'delivery_date'=>['nullable','date'], 'shipped_date'=>['nullable','date'],
            'delivery_challan_for'=>['nullable','string','max:255'], 'shipping_to'=>['nullable','string','max:255'], 'terms'=>['nullable','string'],
            'items'=>['required','array','min:1','max:15'], 'items.*.quantity'=>['required','numeric','gte:0'], 'items.*.serial_number'=>['nullable','string','max:255'],
        ]);

        $challan = DB::transaction(function () use ($invoice,$data) {
            $warehouseId = DB::table('warehouses')->where('name','Main Warehouse')->value('id');
            abort_unless($warehouseId, 422, 'Main Warehouse is not configured.');
            $challan = new DeliveryChallan();
            $challan->challan_number = app(DocumentNumberService::class)->next('delivery_challan', $data['challan_date']);
            $challan->fill(['customer_id'=>$invoice->customer_id,'invoice_id'=>$invoice->id,'challan_date'=>$data['challan_date'],'delivery_date'=>$data['delivery_date']??null,'shipped_date'=>$data['shipped_date']??null,'delivery_challan_for'=>$data['delivery_challan_for']??$invoice->summary,'shipping_to'=>$data['shipping_to']??$invoice->customer->address,'status'=>'issued','terms'=>$data['terms']??null])->save();
            $created = 0;
            foreach ($invoice->items as $index=>$item) {
                $qty=(float)($data['items'][$index]['quantity']??0);
                if ($qty <= 0) continue;
                $previouslyDelivered=(float)DB::table('delivery_challan_items')->where('invoice_item_id',$item->id)->whereExists(function($q)use($invoice){$q->select(DB::raw(1))->from('delivery_challans')->whereColumn('delivery_challans.id','delivery_challan_items.delivery_challan_id')->where('delivery_challans.invoice_id',$invoice->id);})->sum('quantity');
                $remaining=max(0,(float)$item->quantity-$previouslyDelivered);
                abort_if($qty > $remaining + 0.0001, 422, "Delivery quantity exceeds remaining quantity for {$item->description}. Remaining: {$remaining}.");
                if ($item->product_id) {
                    $available=(float)DB::table('stock_transactions')->where('warehouse_id',$warehouseId)->where('product_id',$item->product_id)->sum(DB::raw('quantity_in - quantity_out'));
                    abort_if($qty > $available + 0.0001, 422, "Insufficient stock for {$item->description}. Available: {$available}.");
                }
                $challan->items()->create(['invoice_item_id'=>$item->id,'product_id'=>$item->product_id,'line_no'=>$index+1,'item_name'=>$item->description,'serial_number'=>$data['items'][$index]['serial_number']??null,'quantity'=>$qty,'unit'=>$item->unit]);
                if ($item->product_id) DB::table('stock_transactions')->insert(['warehouse_id'=>$warehouseId,'product_id'=>$item->product_id,'invoice_item_id'=>$item->id,'transaction_type'=>'delivery','transaction_date'=>now(),'quantity_in'=>0,'quantity_out'=>$qty,'unit_cost'=>$item->actual_cost_unit,'reference'=>$challan->challan_number,'notes'=>'Delivery against invoice '.$invoice->invoice_number,'created_at'=>now(),'updated_at'=>now()]);
                $created++;
            }
            abort_if($created === 0, 422, 'Enter at least one delivery quantity.');
            $totalRemaining=(float)DB::table('invoice_items')->where('invoice_id',$invoice->id)->sum('quantity');
            $totalDelivered=(float)DB::table('delivery_challan_items')->join('delivery_challans','delivery_challans.id','=','delivery_challan_items.delivery_challan_id')->where('delivery_challans.invoice_id',$invoice->id)->sum('delivery_challan_items.quantity');
            $invoice->update(['status'=>$totalDelivered >= $totalRemaining - 0.0001 ? 'delivered' : 'partially_delivered']);
            $this->audit->record('delivery_challan.created', $challan, [], $challan->fresh()->load('items')->toArray());
            return $challan;
        });
        return redirect()->route('delivery_challans.show',$challan)->with('success',"Delivery Challan {$challan->challan_number} created.");
    }

    public function show(DeliveryChallan $deliveryChallan): View { $deliveryChallan->load(['customer','invoice','items.product']); return view('delivery_challans.show',['challan'=>$deliveryChallan]); }
    public function print(DeliveryChallan $deliveryChallan): View { $deliveryChallan->load(['customer','invoice','items.product']); return view('delivery_challans.print',['challan'=>$deliveryChallan]); }
}
