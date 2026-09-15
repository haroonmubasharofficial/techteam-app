<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $warehouseId = $request->integer('warehouse_id') ?: Warehouse::where('is_active', true)->value('id');
        $search = trim((string) $request->input('search'));
        $products = Product::query()->where('is_active', true)
            ->when($search, fn($q) => $q->where(fn($x) => $x->where('name','like',"%$search%")->orWhere('sku','like',"%$search%")))
            ->orderBy('name')->get();
        $balances = DB::table('stock_transactions')->where('warehouse_id',$warehouseId)
            ->select('product_id')
            ->selectRaw('COALESCE(SUM(quantity_in),0) AS qty_in')
            ->selectRaw('COALESCE(SUM(quantity_out),0) AS qty_out')
            ->selectRaw('COALESCE(SUM(quantity_in * unit_cost),0) AS inbound_cost')
            ->groupBy('product_id')->get()->keyBy('product_id');
        $rows = $products->map(function($product) use ($balances){
            $b=$balances->get($product->id);
            $qty=(float)(($b->qty_in ?? 0)-($b->qty_out ?? 0));
            $inQty=(float)($b->qty_in ?? 0);
            $cost=$inQty>0?(float)$b->inbound_cost/$inQty:(float)($product->purchase_cost ?? 0);
            return (object)['product'=>$product,'qty'=>$qty,'unit_cost'=>$cost,'stock_value'=>max(0,$qty)*$cost];
        });
        return view('stock.index',['rows'=>$rows,'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),'warehouseId'=>$warehouseId,'search'=>$search]);
    }
}
