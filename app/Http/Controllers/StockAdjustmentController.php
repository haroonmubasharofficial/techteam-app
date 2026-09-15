<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class StockAdjustmentController extends Controller {
 public function index(){ $adjustments=StockAdjustment::with('warehouse')->latest('adjustment_date')->latest('id')->paginate(20); return view('stock_adjustments.index',compact('adjustments')); }
 public function create(){ return view('stock_adjustments.create',['warehouses'=>Warehouse::orderBy('name')->get(),'products'=>Product::where('is_active',true)->orderBy('name')->get()]); }
 public function store(Request $request){
  $data=$request->validate(['warehouse_id'=>['required','exists:warehouses,id'],'adjustment_date'=>['required','date'],'reason'=>['required','string','max:255'],'notes'=>['nullable','string'],'items'=>['required','array','min:1','max:15'],'items.*.product_id'=>['required','exists:products,id'],'items.*.qty'=>['required','numeric','gt:0'],'items.*.unit'=>['required','string','max:30'],'items.*.unit_cost'=>['required','numeric','gte:0'],'items.*.direction'=>['required','in:in,out'],'items.*.notes'=>['nullable','string','max:255']]);
  $adjustment=DB::transaction(function() use($data){
   $year=now()->format('Y'); $last=StockAdjustment::where('adjustment_number','like',"ADJ-$year-%")->lockForUpdate()->latest('id')->first(); $next=$last?((int)substr($last->adjustment_number,-5))+1:1; $number="ADJ-$year-".str_pad($next,5,'0',STR_PAD_LEFT);
   $adjustment=StockAdjustment::create(['adjustment_number'=>$number,'warehouse_id'=>$data['warehouse_id'],'adjustment_date'=>$data['adjustment_date'],'reason'=>$data['reason'],'status'=>'posted','notes'=>$data['notes']??null]);
   foreach($data['items'] as $line){
    if($line['direction']==='out'){ $available=(float)DB::table('stock_transactions')->where('warehouse_id',$data['warehouse_id'])->where('product_id',$line['product_id'])->selectRaw('COALESCE(SUM(quantity_in-quantity_out),0) AS qty')->value('qty'); if($line['qty']>$available) abort(422,'Insufficient stock for the selected product.'); }
    $adjustment->items()->create(['product_id'=>$line['product_id'],'quantity'=>$line['qty'],'unit'=>$line['unit'],'unit_cost'=>$line['unit_cost'],'direction'=>$line['direction'],'notes'=>$line['notes']??null]);
    DB::table('stock_transactions')->insert(['warehouse_id'=>$data['warehouse_id'],'product_id'=>$line['product_id'],'transaction_type'=>'adjustment','transaction_date'=>$data['adjustment_date'],'quantity_in'=>$line['direction']==='in'?$line['qty']:0,'quantity_out'=>$line['direction']==='out'?$line['qty']:0,'unit_cost'=>$line['unit_cost'],'reference'=>$number,'notes'=>$line['notes']??$data['reason'],'created_at'=>now(),'updated_at'=>now()]);
   } return $adjustment;
  });
  return redirect()->route('stock_adjustments.show',$adjustment)->with('success','Stock adjustment posted.');
 }
 public function show(StockAdjustment $stockAdjustment){ $stockAdjustment->load(['warehouse','items.product']); return view('stock_adjustments.show',compact('stockAdjustment')); }
}
