<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Users;

// Homepage (optional)
Route::get('/', function () {
    return redirect()->route('login');
});

// Login page
Route::get('/login', Login::class)->name('login');
Route::get('/dashboard', Dashboard::class)->name('dashboard');

Route::get('/users', Users::class)->name('users');
