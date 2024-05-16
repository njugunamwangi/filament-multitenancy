<?php

use App\Http\Controllers\InvoicePDFController;
use App\Http\Controllers\QuotePDFController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('signed')
    ->get('quotes/{quote}/pdf', QuotePDFController::class)
    ->name('quote.view');

Route::middleware('signed')
    ->get('invoices/{invoice}/pdf', InvoicePDFController::class)
    ->name('invoice.view');

require __DIR__.'/auth.php';
