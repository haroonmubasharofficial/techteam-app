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
        $totalValue = $rows->sum('stock_value');
        return view('stock.index',['rows'=>$rows,'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get(),'warehouseId'=>$warehouseId,'search'=>$search,'totalValue'=>$totalValue]);
    }

    public function ledger(Request $request, Product $product)
    {
        $warehouseId = $request->integer('warehouse_id') ?: Warehouse::where('is_active', true)->value('id');
        $transactions = DB::table('stock_transactions as st')
            ->where('st.product_id',$product->id)
            ->where('st.warehouse_id',$warehouseId)
            ->orderBy('st.transaction_date')->orderBy('st.id')
            ->select('st.*')->get();
        $runningQty = 0;
        $rows = $transactions->map(function ($tx) use (&$runningQty) {
            $runningQty += (float)$tx->quantity_in - (float)$tx->quantity_out;
            return (object)['transaction_date'=>$tx->transaction_date,'transaction_type'=>$tx->transaction_type,'reference'=>$tx->reference,'quantity_in'=>(float)$tx->quantity_in,'quantity_out'=>(float)$tx->quantity_out,'unit_cost'=>(float)$tx->unit_cost,'running_qty'=>$runningQty,'notes'=>$tx->notes];
        });
        return view('stock.ledger', compact('product','rows','warehouseId'));
    }
}
