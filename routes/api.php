<?php

use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MerchantProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:manager'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);

    Route::post('users/role', [UserController::class, 'assignRole']);
});

Route::get('/warehouse', [WarehouseController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:manager'])->group(function (){
    Route::get('/product', [ProductController::class, 'index']);
});

// Route::get('/category', [CategoryController::class, 'index']);
