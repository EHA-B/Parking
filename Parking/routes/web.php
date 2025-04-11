<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;


    Route::get('/',[DashboardController::class ,'index'])->name('dashboard.index');
    Route::post('/dashboard/new', [DashboardController::class, 'newCustomer'])->name('dashboard.new');
    Route::post('/dashboard/old', [DashboardController::class, 'oldCustomer'])->name('dashboard.old');
    Route::get('/get-customer-vehicles/{customerId}', [DashboardController::class, 'getCustomerVehicles']);

    Route::get('/dashboard/checkout/{vic_id}/{parking_slot_id}',[DashboardController::class,'checkout'])->name('dashboard.checkout');
    Route::get('/dashboard/add_service/{vic_id}/{parking_slot_id}',[DashboardController::class,'add_service'])->name('dashboard.add_service');
    