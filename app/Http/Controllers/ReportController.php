<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\SupplierPayment;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $sales = Invoice::whereBetween('invoice_date', [$from, $to])->selectRaw('COALESCE(SUM(total_amount),0) total')->selectRaw('COALESCE(SUM(actual_cost_total),0) cost')->selectRaw('COALESCE(SUM(actual_profit),0) profit')->first();
        $purchases = Purchase::whereBetween('purchase_date', [$from, $to])->selectRaw('COALESCE(SUM(total_amount),0) total')->first();
        $receipts = CustomerPayment::whereBetween('payment_date', [$from, $to])->sum('amount');
        $supplierPayments = SupplierPayment::whereBetween('payment_date', [$from, $to])->sum('amount');

        $receivable = Invoice::query()->selectRaw('COALESCE(SUM(total_amount),0) total')->value('total');
        $paidAgainstInvoices = DB::table('customer_payment_allocations')->sum('amount');
        $receivable = max(0, (float)$receivable - (float)$paidAgainstInvoices);
        $payable = Purchase::query()->selectRaw('COALESCE(SUM(total_amount),0) total')->value('total');
        $paidAgainstPurchases = DB::table('supplier_payment_allocations')->sum('amount');
        $payable = max(0, (float)$payable - (float)$paidAgainstPurchases);

        $warehouseId = Warehouse::where('is_active', true)->value('id');
        $stockValue = 0.0;
        if ($warehouseId) {
            $rows = DB::table('stock_transactions')->where('warehouse_id',$warehouseId)->select('product_id')->selectRaw('SUM(quantity_in-quantity_out) qty')->selectRaw('SUM(quantity_in*unit_cost) inbound_cost')->selectRaw('SUM(quantity_in) inbound_qty')->groupBy('product_id')->get();
            foreach ($rows as $row) {
                $qty=(float)$row->qty; $avg=(float)$row->inbound_qty>0?(float)$row->inbound_cost/(float)$row->inbound_qty:0; $stockValue += max(0,$qty)*$avg;
            }
        }

        return view('reports.index', compact('from','to','sales','purchases','receipts','supplierPayments','receivable','payable','stockValue'));
    }
}
