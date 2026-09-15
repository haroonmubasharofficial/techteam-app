<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
class PurchaseController extends Controller {
    public function index(): View { $purchases=Purchase::with(['supplier','warehouse'])->latest('purchase_date')->latest('id')->paginate(20); return view('purchases.index',compact('purchases')); }
    public function create(): View { return view('purchases.create',['suppliers'=>Supplier::where('is_active',true)->orderBy('company_name')->get(),'products'=>Product::where('is_active',true)->with('unit')->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get()]); }
    public function store(Request $request): RedirectResponse {
        $data=$request->validate(['supplier_id'=>['required','exists:suppliers,id'],'warehouse_id'=>['required','exists:warehouses,id'],'purchase_date'=>['required','date'],'supplier_invoice_number'=>['nullable','string','max:100'],'reference'=>['nullable','string','max:100'],'notes'=>['nullable','string'],'items'=>['required','array','min:1','max:15'],'items.*.product_id'=>['required','exists:products,id'],'items.*.description'=>['required','string','max:500'],'items.*.quantity'=>['required','numeric','gt:0'],'items.*.unit'=>['required','string','max:30'],'items.*.unit_cost'=>['required','numeric','gte:0'],'items.*.tax_rate'=>['nullable','numeric','gte:0'],'items.*.serial_number'=>['nullable','string','max:150']]);
        $purchase=DB::transaction(function()use($data){$p=new Purchase();$p->purchase_number=app(DocumentNumberService::class)->next('purchase',$data['purchase_date']);$p->fill(['supplier_id'=>$data['supplier_id'],'warehouse_id'=>$data['warehouse_id'],'purchase_date'=>$data['purchase_date'],'supplier_invoice_number'=>$data['supplier_invoice_number']??null,'reference'=>$data['reference']??null,'notes'=>$data['notes']??null,'status'=>'received','currency'=>'PKR'])->save();$sub=$tax=$total=0.0;foreach($data['items'] as $item){$qty=(float)$item['quantity'];$cost=(float)$item['unit_cost'];$rate=(float)($item['tax_rate']??0);$base=$qty*$cost;$t=$base*$rate/100;$pi=$p->items()->create(['product_id'=>$item['product_id'],'description'=>$item['description'],'quantity'=>$qty,'unit'=>$item['unit'],'unit_cost'=>$cost,'tax_rate'=>$rate,'tax_amount'=>$t,'total_cost'=>$base+$t,'serial_number'=>$item['serial_number']??null]);StockTransaction::create(['warehouse_id'=>$p->warehouse_id,'product_id'=>$pi->product_id,'purchase_item_id'=>$pi->id,'transaction_type'=>'purchase','transaction_date'=>$p->purchase_date->startOfDay(),'quantity_in'=>$qty,'quantity_out'=>0,'unit_cost'=>$cost,'reference'=>$p->purchase_number]);$sub+=$base;$tax+=$t;$total+=$base+$t;}$p->update(['subtotal'=>$sub,'tax_total'=>$tax,'total_amount'=>$total]);DB::table('party_ledger_entries')->insert(['customer_id'=>null,'supplier_id'=>$p->supplier_id,'entry_date'=>$p->purchase_date,'entry_type'=>'purchase','reference_type'=>'purchase','reference_id'=>$p->id,'debit'=>0,'credit'=>$total,'description'=>'Purchase '.$p->purchase_number,'created_at'=>now(),'updated_at'=>now()]);return $p;});
        return redirect()->route('purchases.show',$purchase)->with('success',"Purchase {$purchase->purchase_number} received and stock updated.");
    }
    public function show(Purchase $purchase): View { $purchase->load(['supplier','warehouse','items.product']); return view('purchases.show',compact('purchase')); }
}
