<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\Unit;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    public function __construct(private AuditLogService $audit) {}

    public function index(): View
    {
        return view('master_data.index', [
            'categories' => ProductCategory::withCount('products')->orderBy('name')->get(),
            'units' => Unit::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required','string','max:255','unique:product_categories,name']]);
        $category = ProductCategory::create($data);
        $this->audit->record('product_category.created', $category, [], $category->only(['name']));
        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, ProductCategory $category): RedirectResponse
    {
        $data = $request->validate(['name' => ['required','string','max:255','unique:product_categories,name,'.$category->id]]);
        $old = $category->only(['name']);
        $category->update($data);
        $this->audit->record('product_category.updated', $category, $old, $category->only(['name']));
        return back()->with('success', 'Category updated.');
    }

    public function storeUnit(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required','string','max:255','unique:units,name'],'symbol' => ['nullable','string','max:20']]);
        $unit = Unit::create($data);
        $this->audit->record('unit.created', $unit, [], $unit->only(['name','symbol']));
        return back()->with('success', 'Unit created.');
    }

    public function updateUnit(Request $request, Unit $unit): RedirectResponse
    {
        $data = $request->validate(['name' => ['required','string','max:255','unique:units,name,'.$unit->id],'symbol' => ['nullable','string','max:20']]);
        $old = $unit->only(['name','symbol']);
        $unit->update($data);
        $this->audit->record('unit.updated', $unit, $old, $unit->only(['name','symbol']));
        return back()->with('success', 'Unit updated.');
    }
}
