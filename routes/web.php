<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');


// Route::middleware('api')->group(function () {
//     Route::get('/category', [CategoryController::class, 'index']);
// });

