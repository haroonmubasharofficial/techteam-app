<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierPaymentController extends Controller
{
    public function __construct(private AuditLogService $audit) {}
    public function index(Request $request): View { $payments=SupplierPayment::with('supplier')->when($request->filled('q'),function($query)use($request){$term=trim($request->string('q')->toString());$query->where(function($q)use($term){$q->where('payment_number','like',"%{$term}%")->orWhereHas('supplier',fn($sq)=>$sq->where('company_name','like',"%{$term}%"));});})->latest('payment_date')->latest('id')->paginate(20)->withQueryString();return view('supplier_payments.index',compact('payments')); }
    public function create(): View { return view('supplier_payments.create',['suppliers'=>Supplier::where('is_active',true)->orderBy('company_name')->get()]); }
    public function purchases(Supplier $supplier): JsonResponse { $purchases=Purchase::where('supplier_id',$supplier->id)->whereIn('status',['received','partially_paid','paid'])->orderBy('purchase_date')->orderBy('id')->get()->map(function($purchase){$paid=(float)$purchase->paymentAllocations()->sum('amount');return ['id'=>$purchase->id,'purchase_number'=>$purchase->purchase_number,'purchase_date'=>$purchase->purchase_date?->format('Y-m-d'),'total'=>(float)$purchase->total_amount,'paid'=>$paid,'outstanding'=>max(0,(float)$purchase->total_amount-$paid)];})->filter(fn($p)=>$p['outstanding']>0)->values();return response()->json($purchases); }
    public function store(Request $request): RedirectResponse {
        $data=$request->validate(['supplier_id'=>['required','exists:suppliers,id'],'payment_date'=>['required','date'],'payment_method'=>['required','in:Cash,Bank Transfer,Cheque,Card,Online,Other'],'reference'=>['nullable','string','max:100'],'amount'=>['required','numeric','gt:0'],'notes'=>['nullable','string'],'allocations'=>['nullable','array','max:15'],'allocations.*.purchase_id'=>['required','integer','distinct'],'allocations.*.amount'=>['required','numeric','gt:0']]);
        $payment=DB::transaction(function()use($data){$payment=new SupplierPayment();$payment->payment_number=app(DocumentNumberService::class)->next('supplier_payment',$data['payment_date']);$payment->fill(['supplier_id'=>$data['supplier_id'],'payment_date'=>$data['payment_date'],'payment_method'=>$data['payment_method'],'reference'=>$data['reference']??null,'amount'=>$data['amount'],'status'=>'posted','notes'=>$data['notes']??null]);$payment->save();$allocated=0.0;foreach($data['allocations']??[] as $allocation){$purchase=Purchase::where('id',$allocation['purchase_id'])->where('supplier_id',$payment->supplier_id)->whereIn('status',['received','partially_paid','paid'])->lockForUpdate()->firstOrFail();$alreadyPaid=(float)$purchase->paymentAllocations()->sum('amount');$outstanding=max(0,(float)$purchase->total_amount-$alreadyPaid);abort_if((float)$allocation['amount']>$outstanding+0.0001,422,"Allocation exceeds outstanding balance for {$purchase->purchase_number}.");$allocated+=(float)$allocation['amount'];abort_if($allocated>(float)$payment->amount+0.0001,422,'Allocated amount cannot exceed the payment amount.');$payment->allocations()->create(['purchase_id'=>$purchase->id,'amount'=>$allocation['amount']]);$purchase->update(['status'=>$outstanding-(float)$allocation['amount']<=0.0001?'paid':'partially_paid']);}DB::table('party_ledger_entries')->insert(['customer_id'=>null,'supplier_id'=>$payment->supplier_id,'entry_date'=>$payment->payment_date,'entry_type'=>'payment','reference_type'=>'supplier_payment','reference_id'=>$payment->id,'debit'=>$payment->amount,'credit'=>0,'description'=>'Supplier payment '.$payment->payment_number,'created_at'=>now(),'updated_at'=>now()]);return $payment;});
        $this->audit->record('supplier_payment.posted',$payment,[],['payment_number'=>$payment->payment_number,'supplier_id'=>$payment->supplier_id,'amount'=>(float)$payment->amount]);
        return redirect()->route('supplier_payments.show',$payment)->with('success',"Payment {$payment->payment_number} posted.");
    }
    public function show(SupplierPayment $payment): View {$payment->load(['supplier','allocations.purchase']);return view('supplier_payments.show',compact('payment'));}
    public function print(SupplierPayment $payment): View {$payment->load(['supplier','allocations.purchase']);return view('supplier_payments.print',compact('payment'));}
    public function payables(): View {$suppliers=Supplier::where('is_active',true)->orderBy('company_name')->get()->map(function($supplier){$purchases=Purchase::where('supplier_id',$supplier->id)->whereIn('status',['received','partially_paid','paid'])->get(['id','total_amount']);$billed=(float)$purchases->sum('total_amount');$paid=(float)DB::table('supplier_payment_allocations')->whereIn('purchase_id',$purchases->pluck('id'))->sum('amount');return ['supplier'=>$supplier,'billed'=>$billed,'paid'=>$paid,'payable'=>max(0,$billed-$paid)];})->filter(fn($row)=>$row['payable']>0)->values();return view('supplier_payments.payables',compact('suppliers'));}
    public function statement(Supplier $supplier): View {$entries=DB::table('party_ledger_entries')->where('supplier_id',$supplier->id)->orderBy('entry_date')->orderBy('id')->get();$balance=0;foreach($entries as $entry){$balance+=(float)$entry->credit-(float)$entry->debit;$entry->balance=$balance;}return view('supplier_payments.statement',compact('supplier','entries','balance'));}
}
