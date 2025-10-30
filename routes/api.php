<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserController;


Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Role-based route
        Route::get('/admin', function () {
            return response()->json(['message' => 'Welcome Admin']);
        })->middleware('role:admin');

        // Permission-based route
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Dashboard Access']);
        })->middleware('permission:view dashboard');
    });
});