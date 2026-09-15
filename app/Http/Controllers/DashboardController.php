<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Purchase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $from = Carbon::now()->startOfMonth();
        $to = Carbon::now()->endOfMonth();
        $sales = (float) Invoice::whereBetween('invoice_date', [$from, $to])->sum('total_amount');
        $purchases = (float) Purchase::whereBetween('purchase_date', [$from, $to])->sum('total_amount');
        $customerInvoices = Invoice::get(['id','total_amount']);
        $receivables = max(0, (float)$customerInvoices->sum('total_amount') - (float)DB::table('customer_payment_allocations')->whereIn('invoice_id',$customerInvoices->pluck('id'))->sum('amount'));
        $supplierPurchases = Purchase::get(['id','total_amount']);
        $payables = max(0, (float)$supplierPurchases->sum('total_amount') - (float)DB::table('supplier_payment_allocations')->whereIn('purchase_id',$supplierPurchases->pluck('id'))->sum('amount'));
        $stockProducts = DB::table('stock_transactions')->select('product_id', DB::raw('SUM(quantity_in - quantity_out) as quantity'))->groupBy('product_id')->havingRaw('SUM(quantity_in - quantity_out) > 0')->count();
        return view('dashboard.index', compact('sales','purchases','receivables','payables','stockProducts'));
    }
}
