<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;


    Route::get('/',[DashboardController::class ,'index'])->name('dashboard.index');
    Route::post('/dashboard/new', [DashboardController::class, 'newCustomer'])->name('dashboard.new');
    Route::post('/dashboard/old', [DashboardController::class, 'oldCustomer'])->name('dashboard.old');
    

