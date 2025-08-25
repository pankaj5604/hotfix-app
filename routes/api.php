<?php

use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\EmployeeController;

Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);
Route::post('logout',[UserAuthController::class,'logout'])->middleware('auth:sanctum');


Route::apiResource('employees', EmployeeController::class)->middleware('auth:sanctum');
  