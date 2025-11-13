<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\RoleController;


Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

           // ✅ Add this     
        Route::get('/roles', [RoleController::class, 'index']);
            // User list
        Route::get('/user-list', [UserController::class, 'index']);
        // Create new user
        Route::post('/users', [UserController::class, 'store']);
        // Show single user
        Route::get('/users/{id}', [UserController::class, 'show']);
        // Update user
        Route::put('/users/{id}', [UserController::class, 'update']);
        // Delete user
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
});