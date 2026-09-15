<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = Invoice::with('customer')->when($request->filled('q'), function ($query) use ($request) {
            $term = trim($request->string('q')->toString());
            $query->where(fn ($q) => $q->where('invoice_number', 'like', "%{$term}%")->orWhereHas('customer', fn ($cq) => $cq->where('company_name', 'like', "%{$term}%")));
        })->latest('invoice_date')->latest('id')->paginate(20)->withQueryString();
        return view('invoices.index', compact('invoices'));
    }

    public function createFromQuotation(Quotation $quotation): View
    {
        $quotation->load(['customer', 'items.product', 'invoice']);
        abort_if($quotation->invoice, 409, 'This quotation has already been converted to an invoice.');
        abort_unless($quotation->items->isNotEmpty(), 422, 'The quotation has no items to invoice.');
        return view('invoices.create', compact('quotation'));
    }

    public function storeFromQuotation(Request $request, Quotation $quotation): RedirectResponse
    {
        $quotation->load('items');
        abort_if($quotation->invoice()->exists(), 409, 'This quotation has already been converted to an invoice.');
        $data = $request->validate([
            'invoice_date' => ['required', 'date'], 'reference' => ['nullable', 'string', 'max:100'], 'summary' => ['nullable', 'string', 'max:255'], 'terms' => ['nullable', 'string'],
            'items' => ['required', 'array', 'size:' . $quotation->items->count(), 'max:15'], 'items.*.actual_cost_unit' => ['required', 'numeric', 'gte:0'],
        ]);

        $invoice = DB::transaction(function () use ($quotation, $data) {
            $invoice = new Invoice();
            $invoice->invoice_number = app(DocumentNumberService::class)->next('invoice', $data['invoice_date']);
            $invoice->fill(['customer_id' => $quotation->customer_id, 'quotation_id' => $quotation->id, 'invoice_date' => $data['invoice_date'], 'reference' => $data['reference'] ?? $quotation->reference, 'summary' => $data['summary'] ?? $quotation->summary, 'terms' => $data['terms'] ?? $quotation->terms, 'status' => 'draft', 'currency' => $quotation->currency])->save();
            $subtotal = $discountTotal = $taxTotal = $costTotal = $profitTotal = 0.0;
            foreach ($quotation->items as $index => $qItem) {
                $actualCost = (float)$data['items'][$index]['actual_cost_unit']; $qty = (float)$qItem->quantity; $selling = (float)$qItem->selling_price; $discount = (float)$qItem->discount;
                $net = max(0, ($selling * $qty) - $discount); $tax = $net * (float)$qItem->tax_rate / 100; $cost = $actualCost * $qty; $profit = $net - $cost;
                $invoice->items()->create(['quotation_item_id' => $qItem->id, 'product_id' => $qItem->product_id, 'line_no' => $index + 1, 'description' => $qItem->description, 'quantity' => $qty, 'unit' => $qItem->unit, 'actual_cost_unit' => $actualCost, 'selling_price_unit' => $selling, 'discount' => $discount, 'tax_rate' => $qItem->tax_rate, 'tax_amount' => $tax, 'cost_total' => $cost, 'actual_profit' => $profit, 'actual_margin_percent' => $net > 0 ? ($profit / $net) * 100 : 0]);
                $subtotal += $selling * $qty; $discountTotal += $discount; $taxTotal += $tax; $costTotal += $cost; $profitTotal += $profit;
            }
            $netSubtotal = $subtotal - $discountTotal;
            $total = $netSubtotal + $taxTotal;
            $invoice->update(['subtotal' => $netSubtotal, 'discount_total' => $discountTotal, 'tax_total' => $taxTotal, 'total_amount' => $total, 'actual_cost_total' => $costTotal, 'actual_profit' => $profitTotal, 'actual_margin_percent' => $netSubtotal > 0 ? ($profitTotal / $netSubtotal) * 100 : 0]);
            DB::table('party_ledger_entries')->insert(['customer_id'=>$invoice->customer_id,'supplier_id'=>null,'entry_date'=>$invoice->invoice_date,'entry_type'=>'invoice','reference_type'=>'invoice','reference_id'=>$invoice->id,'debit'=>$total,'credit'=>0,'description'=>'Invoice '.$invoice->invoice_number,'created_at'=>now(),'updated_at'=>now()]);
            $quotation->update(['status' => 'invoiced']);
            return $invoice;
        });
        return redirect()->route('invoices.show', $invoice)->with('success', "Invoice {$invoice->invoice_number} created from quotation.");
    }

    public function show(Invoice $invoice): View { $invoice->load(['customer', 'quotation', 'items.product']); return view('invoices.show', compact('invoice')); }
    public function print(Invoice $invoice): View { $invoice->load(['customer', 'quotation', 'items.product']); return view('invoices.print', compact('invoice')); }
}
