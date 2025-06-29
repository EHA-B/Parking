<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\MonthlyPaymentController;
use Illuminate\Support\Facades\Route;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::post('/dashboard/new', [DashboardController::class, 'newCustomer'])->name('dashboard.new');
Route::post('/dashboard/old', [DashboardController::class, 'oldCustomer'])->name('dashboard.old');
Route::get('/get-customer-vehicles/{customerId}', [DashboardController::class, 'getCustomerVehicles']);

Route::get('/dashboard/checkout/{parcode}', [DashboardController::class, 'checkout'])->name('dashboard.checkout');
Route::post('/dashboard/confirm-checkout', [DashboardController::class, 'confirmCheckout'])->name('dashboard.confirm-checkout');
Route::get('/dashboard/check-parcode', [DashboardController::class, 'checkParcode'])->name('dashboard.check-parcode');
Route::post('/dashboard/add_service/{vic_id}/{parking_slot_id}', [DashboardController::class, 'add_service'])->name('dashboard.add_service');
Route::post('/dashboard/toggle-status/{parking_slot_id}', [DashboardController::class, 'toggleStatus'])->name('dashboard.toggle-status');
Route::post('/dashboard/update-status-time', [DashboardController::class, 'updateStatusTime'])->name('dashboard.update-status-time');

// Replace the existing resource route with these explicit routes
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

// Vehicle management routes
Route::post('/customers/{customer}/vehicles', [CustomerController::class, 'addVehicle'])->name('customers.vehicles.store');
Route::put('/customers/vehicles/{vic}', [CustomerController::class, 'updateVehicle'])->name('customers.vehicles.update');
Route::delete('/customers/vehicles/{vic}', [CustomerController::class, 'deleteVehicle'])->name('customers.vehicles.destroy');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
Route::post('/pricing/update', [PricingController::class, 'update'])->name('pricing.update');

Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

Route::resource('items', ItemController::class)->except(['show', 'create', 'edit']);
Route::resource('services', ServiceController::class)->except(['show', 'create', 'edit']);
Route::get('items-services', [ItemController::class, 'index'])->name('items-services.index');

Route::get('/history/report', [HistoryController::class, 'generateReport'])->name('history.report');

Route::get('/dashboard/status-history/{customer_id}', [DashboardController::class, 'viewStatusHistory'])->name('dashboard.status-history');

// Monthly Payment Routes
Route::post('/monthly-payments/{parkingSlot}', [MonthlyPaymentController::class, 'store'])->name('monthly-payments.store');
Route::get('/monthly-payments/{parkingSlot}/history', [MonthlyPaymentController::class, 'getPaymentHistory'])->name('monthly-payments.history');