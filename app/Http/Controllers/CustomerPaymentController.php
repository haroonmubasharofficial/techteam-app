<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = CustomerPayment::with('customer')->when($request->filled('q'), function ($query) use ($request) {
            $term = trim($request->string('q')->toString());
            $query->where('receipt_number', 'like', "%{$term}%")->orWhereHas('customer', fn ($q) => $q->where('company_name', 'like', "%{$term}%"));
        })->latest('payment_date')->latest('id')->paginate(20)->withQueryString();
        return view('customer_payments.index', compact('payments'));
    }

    public function create(): View { return view('customer_payments.create', ['customers'=>Customer::where('is_active',true)->orderBy('company_name')->get()]); }

    public function invoices(Customer $customer): \Illuminate\Http\JsonResponse
    {
        $invoices = Invoice::where('customer_id',$customer->id)->whereIn('status',['draft','delivered','partially_delivered','partially_paid','posted'])
            ->orderBy('invoice_date')->orderBy('id')->get()->map(function ($invoice) {
                $allocated=(float)$invoice->paymentAllocations()->sum('amount');
                return ['id'=>$invoice->id,'invoice_number'=>$invoice->invoice_number,'invoice_date'=>$invoice->invoice_date?->format('Y-m-d'),'total'=>(float)$invoice->total_amount,'paid'=>$allocated,'outstanding'=>max(0,(float)$invoice->total_amount-$allocated)];
            })->filter(fn($i)=>$i['outstanding']>0)->values();
        return response()->json($invoices);
    }

    public function store(Request $request): RedirectResponse
    {
        $data=$request->validate(['customer_id'=>['required','exists:customers,id'],'payment_date'=>['required','date'],'payment_method'=>['required','in:Cash,Bank Transfer,Cheque,Card,Online,Other'],'reference'=>['nullable','string','max:100'],'amount'=>['required','numeric','gt:0'],'notes'=>['nullable','string'],'allocations'=>['nullable','array','max:15'],'allocations.*.invoice_id'=>['required','integer','distinct'],'allocations.*.amount'=>['required','numeric','gt:0']]);
        $payment=DB::transaction(function()use($data){$payment=new CustomerPayment();$payment->receipt_number=app(DocumentNumberService::class)->next('customer_payment',$data['payment_date']);$payment->fill(['customer_id'=>$data['customer_id'],'payment_date'=>$data['payment_date'],'payment_method'=>$data['payment_method'],'reference'=>$data['reference']??null,'amount'=>$data['amount'],'status'=>'posted','notes'=>$data['notes']??null]);$payment->save();$allocated=0.0;foreach($data['allocations']??[] as $allocation){$invoice=Invoice::where('id',$allocation['invoice_id'])->where('customer_id',$payment->customer_id)->lockForUpdate()->firstOrFail();$alreadyPaid=(float)$invoice->paymentAllocations()->sum('amount');$outstanding=max(0,(float)$invoice->total_amount-$alreadyPaid);abort_if((float)$allocation['amount']>$outstanding+0.0001,422,"Allocation exceeds outstanding balance for {$invoice->invoice_number}.");$allocated+=(float)$allocation['amount'];abort_if($allocated>(float)$payment->amount+0.0001,422,'Allocated amount cannot exceed the receipt amount.');$payment->allocations()->create(['invoice_id'=>$invoice->id,'amount'=>$allocation['amount']]);$invoice->update(['status'=>$outstanding-(float)$allocation['amount']<=0.0001?'paid':'partially_paid']);}DB::table('party_ledger_entries')->insert(['customer_id'=>$payment->customer_id,'supplier_id'=>null,'entry_date'=>$payment->payment_date,'entry_type'=>'receipt','reference_type'=>'customer_payment','reference_id'=>$payment->id,'debit'=>0,'credit'=>$payment->amount,'description'=>'Customer payment '.$payment->receipt_number,'created_at'=>now(),'updated_at'=>now()]);return $payment;});
        return redirect()->route('customer_payments.show',$payment)->with('success',"Receipt {$payment->receipt_number} posted.");
    }
    public function show(CustomerPayment $payment): View {$payment->load(['customer','allocations.invoice']);return view('customer_payments.show',compact('payment'));}
    public function print(CustomerPayment $payment): View {$payment->load(['customer','allocations.invoice']);return view('customer_payments.print',compact('payment'));}
    public function receivables(): View {$customers=Customer::where('is_active',true)->orderBy('company_name')->get()->map(function($customer){$invoices=Invoice::where('customer_id',$customer->id)->get(['id','total_amount']);$invoiced=(float)$invoices->sum('total_amount');$paid=(float)DB::table('customer_payment_allocations')->whereIn('invoice_id',$invoices->pluck('id'))->sum('amount');return ['customer'=>$customer,'invoiced'=>$invoiced,'paid'=>$paid,'receivable'=>max(0,$invoiced-$paid)];})->filter(fn($row)=>$row['receivable']>0)->values();return view('customer_payments.receivables',compact('customers'));}
    public function statement(Customer $customer): View {$entries=DB::table('party_ledger_entries')->where('customer_id',$customer->id)->orderBy('entry_date')->orderBy('id')->get();$balance=0;foreach($entries as $entry){$balance+=(float)$entry->debit-(float)$entry->credit;$entry->balance=$balance;}return view('customer_payments.statement',compact('customer','entries','balance'));}
}
