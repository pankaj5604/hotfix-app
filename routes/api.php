<?php

use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EmployeeWorkController;
use App\Http\Controllers\EmployeeAdvanceController;


Route::post('login',[UserAuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout',[UserAuthController::class,'logout']);
    Route::apiResource('employees', EmployeeController::class);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']); // bulk add
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{product}', [ProductController::class, 'update']);

    // Employee Work
    Route::get('/employee-work', [EmployeeWorkController::class, 'index']);
    Route::post('/employee-work', [EmployeeWorkController::class, 'store']);
    Route::get('/employee-work/{id}', [EmployeeWorkController::class, 'show']);
    Route::put('/employee-work/{employeeWork}', [EmployeeWorkController::class, 'update']);


    Route::get('/employee-advance', [EmployeeAdvanceController::class, 'index']);
    Route::post('/employee-advance', [EmployeeAdvanceController::class, 'store']);
    Route::get('/employee-advance/{id}', [EmployeeAdvanceController::class, 'show']);
    Route::put('/employee-advance/{id}', [EmployeeAdvanceController::class, 'update']);
});

Route::get('employee/details/{id}', [EmployeeController::class, 'showPublic']);