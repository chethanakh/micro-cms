<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PublicOrderTrackingController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::redirect('/app/login', '/login')->name('login');
Route::get('/track/{slug}', PublicOrderTrackingController::class)->name('tracking.public');

Route::middleware(Authenticate::class)->group(function (): void {
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/parcel-label', [InvoiceController::class, 'parcelLabel'])->name('invoices.parcel-label');
});
