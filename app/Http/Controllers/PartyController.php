<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartyController extends Controller
{
    public function __construct(private AuditLogService $audit) {}
    public function customers(): View { return view('parties.customers', ['customers'=>Customer::latest('id')->paginate(20)]); }
    public function createCustomer(): View { return view('parties.customer_create'); }
    public function storeCustomer(Request $request): RedirectResponse { $data=$this->customerData($request); $data['is_active']=true; $customer=Customer::create($data); $this->audit->record('customer.created',$customer,[], $customer->only(['company_name','contact_person','city','phone','mobile','email','ntn','strn','payment_terms','credit_limit','tax_treatment','is_active'])); return redirect()->route('parties.customers')->with('success','Customer created.'); }
    public function editCustomer(Customer $customer): View { return view('parties.customer_edit',compact('customer')); }
    public function updateCustomer(Request $request, Customer $customer): RedirectResponse { $data=$this->customerData($request); $data['is_active']=$request->boolean('is_active'); $old=$customer->only(array_keys($data)); $customer->update($data); $this->audit->record('customer.updated',$customer,$old,$customer->only(array_keys($data))); return redirect()->route('parties.customers')->with('success','Customer updated.'); }
    public function suppliers(): View { return view('parties.suppliers', ['suppliers'=>Supplier::latest('id')->paginate(20)]); }
    public function createSupplier(): View { return view('parties.supplier_create'); }
    public function storeSupplier(Request $request): RedirectResponse { $data=$this->supplierData($request); $data['is_active']=true; $supplier=Supplier::create($data); $this->audit->record('supplier.created',$supplier,[], $supplier->only(['company_name','contact_person','phone','email','ntn','strn','payment_terms','is_active'])); return redirect()->route('parties.suppliers')->with('success','Supplier created.'); }
    public function editSupplier(Supplier $supplier): View { return view('parties.supplier_edit',compact('supplier')); }
    public function updateSupplier(Request $request, Supplier $supplier): RedirectResponse { $data=$this->supplierData($request); $data['is_active']=$request->boolean('is_active'); $old=$supplier->only(array_keys($data)); $supplier->update($data); $this->audit->record('supplier.updated',$supplier,$old,$supplier->only(array_keys($data))); return redirect()->route('parties.suppliers')->with('success','Supplier updated.'); }
    private function customerData(Request $request): array { return $request->validate(['company_name'=>['required','string','max:255'],'contact_person'=>['nullable','string','max:255'],'address'=>['nullable','string'],'city'=>['nullable','string','max:100'],'phone'=>['nullable','string','max:50'],'mobile'=>['nullable','string','max:50'],'email'=>['nullable','email','max:255'],'ntn'=>['nullable','string','max:50'],'strn'=>['nullable','string','max:50'],'payment_terms'=>['nullable','string','max:100'],'credit_limit'=>['nullable','numeric','gte:0'],'tax_treatment'=>['nullable','string','max:100'],'notes'=>['nullable','string']]); }
    private function supplierData(Request $request): array { return $request->validate(['company_name'=>['required','string','max:255'],'contact_person'=>['nullable','string','max:255'],'address'=>['nullable','string'],'phone'=>['nullable','string','max:50'],'email'=>['nullable','email','max:255'],'ntn'=>['nullable','string','max:50'],'strn'=>['nullable','string','max:50'],'payment_terms'=>['nullable','string','max:100'],'notes'=>['nullable','string']]); }
}
