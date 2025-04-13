<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PricingController;
use Illuminate\Support\Facades\Route;


    Route::get('/',[DashboardController::class ,'index'])->name('dashboard.index');
    Route::post('/dashboard/new', [DashboardController::class, 'newCustomer'])->name('dashboard.new');
    Route::post('/dashboard/old', [DashboardController::class, 'oldCustomer'])->name('dashboard.old');
    Route::get('/get-customer-vehicles/{customerId}', [DashboardController::class, 'getCustomerVehicles']);

    Route::get('/dashboard/checkout/{vic_id}/{parking_slot_id}',[DashboardController::class,'checkout'])->name('dashboard.checkout');
    Route::get('/dashboard/add_service/{vic_id}/{parking_slot_id}',[DashboardController::class,'add_service'])->name('dashboard.add_service');
    // Replace the existing resource route with these explicit routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
    Route::post('/pricing/update', [PricingController::class, 'update'])->name('pricing.update');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    