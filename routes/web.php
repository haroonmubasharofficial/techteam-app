<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryChallanController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));
Route::get('/dashboard', DashboardController::class)->name('dashboard');
Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
Route::get('/quotations/{quotation}/invoice', [InvoiceController::class, 'createFromQuotation'])->name('invoices.createFromQuotation');
Route::post('/quotations/{quotation}/invoice', [InvoiceController::class, 'storeFromQuotation'])->name('invoices.storeFromQuotation');
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::get('/invoices/{invoice}/delivery-challan', [DeliveryChallanController::class, 'createFromInvoice'])->name('delivery_challans.createFromInvoice');
Route::post('/invoices/{invoice}/delivery-challan', [DeliveryChallanController::class, 'storeFromInvoice'])->name('delivery_challans.storeFromInvoice');
Route::get('/delivery-challans', [DeliveryChallanController::class, 'index'])->name('delivery_challans.index');
Route::get('/delivery-challans/{deliveryChallan}', [DeliveryChallanController::class, 'show'])->name('delivery_challans.show');
Route::get('/delivery-challans/{deliveryChallan}/print', [DeliveryChallanController::class, 'print'])->name('delivery_challans.print');
Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
