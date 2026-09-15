<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartyController extends Controller
{
    public function customers(): View { return view('parties.customers', ['customers'=>Customer::latest('id')->paginate(20)]); }
    public function createCustomer(): View { return view('parties.customer_create'); }
    public function storeCustomer(Request $request): RedirectResponse
    {
        $data=$request->validate(['company_name'=>['required','string','max:255'],'contact_person'=>['nullable','string','max:255'],'address'=>['nullable','string'],'city'=>['nullable','string','max:100'],'phone'=>['nullable','string','max:50'],'mobile'=>['nullable','string','max:50'],'email'=>['nullable','email','max:255'],'ntn'=>['nullable','string','max:50'],'strn'=>['nullable','string','max:50'],'payment_terms'=>['nullable','string','max:100'],'credit_limit'=>['nullable','numeric','gte:0'],'tax_treatment'=>['nullable','string','max:100'],'notes'=>['nullable','string']]);
        $data['is_active']=true; Customer::create($data); return redirect()->route('parties.customers')->with('success','Customer created.');
    }
    public function suppliers(): View { return view('parties.suppliers', ['suppliers'=>Supplier::latest('id')->paginate(20)]); }
    public function createSupplier(): View { return view('parties.supplier_create'); }
    public function storeSupplier(Request $request): RedirectResponse
    {
        $data=$request->validate(['company_name'=>['required','string','max:255'],'contact_person'=>['nullable','string','max:255'],'address'=>['nullable','string'],'phone'=>['nullable','string','max:50'],'email'=>['nullable','email','max:255'],'ntn'=>['nullable','string','max:50'],'strn'=>['nullable','string','max:50'],'payment_terms'=>['nullable','string','max:100'],'notes'=>['nullable','string']]);
        $data['is_active']=true; Supplier::create($data); return redirect()->route('parties.suppliers')->with('success','Supplier created.');
    }
}
