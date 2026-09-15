<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Unit;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
class ProductController extends Controller {
 public function __construct(private AuditLogService $audit) {}
 public function index(Request $request): View { $products=Product::with('unit')->when($request->filled('q'),function($query)use($request){$term=trim($request->string('q')->toString());$query->where(fn($q)=>$q->where('name','like',"%{$term}%")->orWhere('sku','like',"%{$term}%"));})->orderBy('name')->paginate(25)->withQueryString(); $stock=DB::table('stock_transactions')->select('product_id')->selectRaw('SUM(quantity_in-quantity_out) as quantity')->groupBy('product_id')->pluck('quantity','product_id'); return view('products.index',compact('products','stock')); }
 public function create(): View { return view('products.create',['units'=>Unit::orderBy('name')->get(),'categories'=>DB::table('product_categories')->orderBy('name')->get()]); }
 public function store(Request $request): RedirectResponse { $data=$this->validated($request); $data['is_active']=true; $product=Product::create($data); $this->audit->record('product.created',$product,[], $product->only(['sku','name','item_type','brand','model','warranty','purchase_cost','default_selling_price','tax_rate','is_active','track_serial','product_category_id','unit_id'])); return redirect()->route('products.index')->with('success','Product created.'); }
 public function edit(Product $product): View { return view('products.edit',['product'=>$product,'units'=>Unit::orderBy('name')->get(),'categories'=>DB::table('product_categories')->orderBy('name')->get()]); }
 public function update(Request $request, Product $product): RedirectResponse { $data=$this->validated($request,$product); $data['is_active']=$request->boolean('is_active'); $old=$product->only(array_keys($data)); $product->update($data); $this->audit->record('product.updated',$product,$old,$product->only(array_keys($data))); return redirect()->route('products.index')->with('success','Product updated.'); }
 private function validated(Request $request, ?Product $product=null): array { $skuRule=['nullable','string','max:100']; if($product){$skuRule[]='unique:products,sku,'.$product->id;}else{$skuRule[]='unique:products,sku';} $data=$request->validate(['product_category_id'=>['nullable','exists:product_categories,id'],'unit_id'=>['nullable','exists:units,id'],'sku'=>$skuRule,'name'=>['required','string','max:255'],'description'=>['nullable','string'],'item_type'=>['required','in:product,service'],'brand'=>['nullable','string','max:100'],'model'=>['nullable','string','max:100'],'warranty'=>['nullable','string','max:100'],'purchase_cost'=>['nullable','numeric','gte:0'],'default_selling_price'=>['nullable','numeric','gte:0'],'tax_rate'=>['nullable','numeric','gte:0'],'track_serial'=>['nullable','boolean']]); $data['track_serial']=$request->boolean('track_serial'); return $data; }
}
