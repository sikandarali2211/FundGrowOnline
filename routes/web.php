<?php

use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserDetailController;
use App\Http\Controllers\TeamController;   // ← NEW

Route::view('/', 'index');

// 
Auth::routes();

/* 
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');
*/

Route::middleware(['auth'])->prefix('User-dashboard')->name('user.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');

    // Referral link page 
    Route::get('/referral-link', [UserController::class, 'referralLink'])
        ->name('referral.index');

    // NEW: Team tree page (YOU -> Level 1 -> Level 2)
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
});

Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/Admin-dashboard', [AdminController::class, 'index'])->name('admin.index');

    // Admin User Details
    Route::get('/Admin-dashboard/userdetails', [AdminUserDetailController::class, 'index'])
        ->name('admin.userdetails.index');
});
