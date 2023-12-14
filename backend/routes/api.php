<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;




// Public routes of authtication
Route::controller(LoginRegisterController::class)->group(function() {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

// Protected routes of product and logout
Route::middleware('auth:sanctum')->group( function () {
    Route::post('/logout', [LoginRegisterController::class, 'logout']);

    Route::controller(ProductController::class)->group(function() {
        Route::post('/products', 'store');
        Route::patch('/products/{id}', 'update');
        Route::delete('/products/{id}', 'destroy');
        Route::get('/products', 'index');
        Route::get('/products/{id}', 'show');
        Route::get('/products/search/{name}', 'search');
    });

    Route::controller(TransactionController::class)->group(function() {
        Route::post('/transactions', 'store');
        Route::get('/transactions', 'index');
        Route::get('/transactions/search/{keyword}', 'search');
    });

});
