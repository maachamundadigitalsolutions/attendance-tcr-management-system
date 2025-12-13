<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\TcrController;
use App\Http\Controllers\Api\V1\NotificationController;


Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/login', [AuthController::class, 'login']);
    // Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Roles management (only admin role can manage roles)
        Route::middleware('role:admin')->group(function () {
            Route::get('/roles', [RoleController::class, 'index']);
            Route::get('/roles/{id}', [RoleController::class, 'show']);
            Route::post('/roles', [RoleController::class, 'store']);
            Route::put('/roles/{id}', [RoleController::class, 'update']);
            Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
        });

        // Users management (admin only)
        Route::middleware('role:admin')->group(function () {
            Route::get('/users/engineers', [UserController::class, 'engineers']);
            Route::get('/user-list', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::get('/users/{id}', [UserController::class, 'show']);
            Route::put('/users/{id}', [UserController::class, 'update']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
        });

        // Attendances
        Route::get('/attendances', [AttendanceController::class, 'index']);
        Route::get('/attendances/{id}', [AttendanceController::class, 'show']);
        Route::post('/attendances/punch-in', [AttendanceController::class, 'punchIn'])
            ->middleware('permission:attendance-mark');
        Route::post('/attendances/{id}/punch-out', [AttendanceController::class, 'punchOut'])
            ->middleware('permission:attendance-mark');
        Route::put('/attendances/{id}', [AttendanceController::class, 'update']);
        Route::delete('/attendances/{id}', [AttendanceController::class, 'destroy'])
            ->middleware('permission:attendance-view-all'); 

        // TCR Routes
        Route::get('/tcrs', [TcrController::class, 'index']);
        Route::post('/tcrs/bulk-assign', [TcrController::class, 'bulkAssign'])
            ->middleware('permission:tcr-assign');
        Route::get('/tcrs/assigned', [TcrController::class, 'assigned'])
            ->middleware('permission:tcr-use');
        Route::post('/tcrs/{id}/use', [TcrController::class, 'useTcr'])
            ->middleware('permission:tcr-use');
        Route::post('/tcrs/{id}/verify', [TcrController::class, 'verify'])
            ->middleware('permission:tcr-verify');
        Route::delete('/tcrs/{id}', [TcrController::class, 'destroy'])
            ->middleware('permission:tcr-delete');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
            //  ->middleware('permission:view notifications');
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
            // ->middleware('permission:mark notifications');
    });
});





