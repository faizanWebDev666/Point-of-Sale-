<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\POSController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/products', [ProductController::class, 'products'])->name('products');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// POS Routes
Route::get('/pos', [POSController::class, 'index'])->name('pos');
Route::post('/api/pos/save-sale', [POSController::class, 'saveSale']);
Route::get('/api/pos/transaction-history', [POSController::class, 'getTransactionHistory']);
Route::get('/api/pos/transaction/{id}', [POSController::class, 'getTransaction']);
Route::post('/api/pos/refund/{id}', [POSController::class, 'refundTransaction']);
Route::post('/api/pos/void/{id}', [POSController::class, 'voidTransaction']);
Route::get('/api/pos/daily-summary', [POSController::class, 'getDailySummary']);
Route::get('/api/pos/stock-alerts', [POSController::class, 'getStockAlerts']);
Route::get('/api/pos/search-products', [POSController::class, 'searchProducts']);