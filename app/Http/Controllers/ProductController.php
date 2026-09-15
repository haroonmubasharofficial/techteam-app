<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products=Product::with('unit')->when($request->filled('q'),function($query)use($request){$term=trim($request->string('q')->toString());$query->where('name','like',"%{$term}%")->orWhere('sku','like',"%{$term}%");})->orderBy('name')->paginate(25)->withQueryString();
        $stock=DB::table('stock_transactions')->select('product_id',DB::raw('SUM(quantity_in-quantity_out) as quantity'))->groupBy('product_id')->pluck('quantity','product_id');
        return view('products.index',compact('products','stock'));
    }
    public function create(): View { return view('products.create',['units'=>Unit::orderBy('name')->get(),'categories'=>DB::table('product_categories')->orderBy('name')->get()]); }
    public function store(Request $request): RedirectResponse
    {
        $data=$request->validate(['product_category_id'=>['nullable','exists:product_categories,id'],'unit_id'=>['nullable','exists:units,id'],'sku'=>['nullable','string','max:100','unique:products,sku'],'name'=>['required','string','max:255'],'description'=>['nullable','string'],'item_type'=>['required','in:product,service'],'brand'=>['nullable','string','max:100'],'model'=>['nullable','string','max:100'],'warranty'=>['nullable','string','max:100'],'purchase_cost'=>['nullable','numeric','gte:0'],'default_selling_price'=>['nullable','numeric','gte:0'],'tax_rate'=>['nullable','numeric','gte:0'],'track_serial'=>['nullable','boolean']]);
        $data['is_active']=true;$data['track_serial']=(bool)($data['track_serial']??false);Product::create($data);return redirect()->route('products.index')->with('success','Product created.');
    }
}
