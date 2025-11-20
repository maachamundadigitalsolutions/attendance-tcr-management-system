<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\RoleController;
use App\Http\Controllers\Api\v1\AttendanceController;
use App\Http\Controllers\Api\v1\TcrController;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Roles
        Route::get('/roles', [RoleController::class, 'index']);

        // Users
        Route::get('/users/engineers', [UserController::class, 'engineers']);
        Route::get('/user-list', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Attendances
        Route::get('/attendances', [AttendanceController::class, 'index']);
        Route::post('/attendances', [AttendanceController::class, 'store']);
        Route::get('/attendances/{id}', [AttendanceController::class, 'show']);
        Route::delete('/attendances/{id}', [AttendanceController::class, 'destroy']);

        // ✅ TCR Routes
        // TCR Management
        Route::get('/tcrs', [TcrController::class, 'index']);              // Admin view all TCRs
        Route::post('/tcrs/bulk-assign', [TcrController::class, 'bulkAssign']); // Admin assigns range
        Route::get('/tcrs/assigned', [TcrController::class, 'assigned']);  // Employee view assigned TCRs
        Route::post('/tcrs/{id}/use', [TcrController::class, 'useTcr']);   // Employee uses TCR
        Route::post('/tcrs/{id}/verify', [TcrController::class, 'verify']); // Permission-based verify
        Route::delete('/tcrs/{id}', [TcrController::class, 'destroy']);    // Admin delete TCR


    });
});
