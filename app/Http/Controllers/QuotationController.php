<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(Request $request): View
    {
        $quotations = Quotation::with('customer')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim($request->string('q')->toString());
                $query->where(fn ($q) => $q->where('quotation_number', 'like', "%{$term}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('company_name', 'like', "%{$term}%")));
            })->latest('quote_date')->latest('id')->paginate(20)->withQueryString();
        return view('quotations.index', compact('quotations'));
    }

    public function create(): View
    {
        return view('quotations.create', ['customers' => Customer::where('is_active', true)->orderBy('company_name')->get(), 'products' => Product::where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $quotation = DB::transaction(fn () => $this->saveQuotation(new Quotation(), $data));
        return redirect()->route('quotations.show', $quotation)->with('success', "Quotation {$quotation->quotation_number} saved.");
    }

    public function show(Quotation $quotation): View { $quotation->load(['customer', 'items.product']); return view('quotations.show', compact('quotation')); }

    public function edit(Quotation $quotation): View
    {
        $quotation->load('items.product');
        return view('quotations.edit', ['quotation' => $quotation, 'customers' => Customer::where('is_active', true)->orderBy('company_name')->get(), 'products' => Product::where('is_active', true)->orderBy('name')->get()]);
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $data = $this->validated($request);
        DB::transaction(fn () => $this->saveQuotation($quotation, $data));
        return redirect()->route('quotations.show', $quotation)->with('success', "Quotation {$quotation->quotation_number} updated.");
    }

    public function print(Quotation $quotation): View { $quotation->load(['customer', 'items.product']); return view('quotations.print', compact('quotation')); }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'], 'quote_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quote_date'], 'reference' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:255'], 'terms' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1', 'max:15'], 'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:500'], 'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit' => ['nullable', 'string', 'max:30'], 'items.*.purchase_cost' => ['required', 'numeric', 'gte:0'],
            'items.*.delivery_cost' => ['nullable', 'numeric', 'gte:0'], 'items.*.other_cost' => ['nullable', 'numeric', 'gte:0'],
            'items.*.selling_price' => ['required', 'numeric', 'gte:0'], 'items.*.discount' => ['nullable', 'numeric', 'gte:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'gte:0'],
        ]);
    }

    private function saveQuotation(Quotation $quotation, array $data): Quotation
    {
        if (!$quotation->exists) $quotation->quotation_number = app(DocumentNumberService::class)->next('quotation', $data['quote_date']);
        $quotation->fill(['customer_id' => $data['customer_id'], 'quote_date' => $data['quote_date'], 'valid_until' => $data['valid_until'] ?? null, 'reference' => $data['reference'] ?? null, 'summary' => $data['summary'] ?? null, 'terms' => $data['terms'] ?? null, 'status' => $quotation->status ?: 'draft', 'currency' => 'PKR'])->save();
        $quotation->items()->delete();
        $subtotal = $discountTotal = $taxTotal = $costTotal = $profitTotal = 0.0;
        foreach ($data['items'] as $index => $item) {
            $qty=(float)$item['quantity']; $purchase=(float)$item['purchase_cost']; $delivery=(float)($item['delivery_cost']??0); $other=(float)($item['other_cost']??0); $selling=(float)$item['selling_price']; $discount=min((float)($item['discount']??0),$selling*$qty); $taxRate=(float)($item['tax_rate']??0);
            $gross=$selling*$qty; $net=$gross-$discount; $tax=$net*$taxRate/100; $lineCost=($purchase+$delivery+$other)*$qty; $profit=$net-$lineCost;
            $quotation->items()->create(['product_id'=>$item['product_id']??null,'line_no'=>$index+1,'description'=>$item['description'],'quantity'=>$qty,'unit'=>$item['unit']??'Unit','purchase_cost'=>$purchase,'delivery_cost'=>$delivery,'other_cost'=>$other,'selling_price'=>$selling,'discount'=>$discount,'tax_rate'=>$taxRate,'tax_amount'=>$tax,'estimated_cost_total'=>$lineCost,'estimated_profit'=>$profit,'estimated_margin_percent'=>$net>0?($profit/$net)*100:0]);
            $subtotal+=$gross; $discountTotal+=$discount; $taxTotal+=$tax; $costTotal+=$lineCost; $profitTotal+=$profit;
        }
        $netSubtotal=$subtotal-$discountTotal;
        $quotation->update(['subtotal'=>$netSubtotal,'discount_total'=>$discountTotal,'tax_total'=>$taxTotal,'total_amount'=>$netSubtotal+$taxTotal,'estimated_cost_total'=>$costTotal,'estimated_profit'=>$profitTotal,'estimated_margin_percent'=>$netSubtotal>0?($profitTotal/$netSubtotal)*100:0]);
        return $quotation;
    }
}
