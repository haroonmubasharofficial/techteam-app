<?php

use App\Http\Controllers\DashboardController;
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
