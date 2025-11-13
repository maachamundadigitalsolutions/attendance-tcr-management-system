<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Users;
use App\Livewire\Roles;
use App\Livewire\Attendances;

// Homepage (optional)
Route::get('/', function () {
    return redirect()->route('login');
});

// Login page
Route::get('/login', Login::class)->name('login');
Route::get('/dashboard', Dashboard::class)->name('dashboard');

Route::get('/user-management', Users::class)->name('users');
Route::get('/roles-permissions', Roles::class)->name('roles');
Route::get('/attendances-management', Attendances::class)->name('attendances');



// Route::middleware('guest')->group(function () {
//     Route::get('/login', Login::class)->name('login');
// });

// Route::middleware('auth.api')->group(function () {
//     Route::get('/dashboard', Dashboard::class)->name('dashboard');
//     Route::get('/users', Users::class)->name('users');
// });

