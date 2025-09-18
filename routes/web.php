<?php

use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminUserDetailController;
use App\Http\Controllers\PlanController;   // ← NEW
use App\Http\Controllers\TeamController;   // ← NEW
use App\Http\Controllers\Auth\GoogleController;   // ← NEW
use App\Http\Controllers\TransactionController;   // ← NEW
use App\Http\Controllers\ReferralPlanController;   // ← NEW
use App\Http\Controllers\PlanSelectionController;   // ← NEW
use App\Http\Controllers\AdminInvestmentPlanController;   // ← NEW
use App\Http\Controllers\AdminWalletController;   // ← NEW

Route::view('/', 'index');

// Test route for plan selections (without admin middleware)
Route::get('/test-plan-selections', function () {
    $selections = \App\Models\PlanSelection::with(['user'])->get();
    return view('admin.plan-selections.index', compact('selections'));
});


// 
Auth::routes();

Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');
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

    // NEW: Investment Plans page
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

    // NEW: Referral Plan page
    Route::get('/referral-plan', [ReferralPlanController::class, 'index'])->name('referralplan.index');

    // NEW: User Profile page
    Route::get('/profile', function () {
        return view('user.profile.index');
    })->name('profile.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');


    // NEW: User Settings page
    Route::get('/settings', function () {
        return view('user.settings.index');
    })->name('settings.index');

    // NEW: Wallet page
    Route::get('/wallet', function () {
        return view('user.wallet.index');
    })->name('wallet.index');

    // Plan Selection Routes
    Route::get('/plan-selections', [PlanSelectionController::class, 'userSelections'])->name('plan-selections.index');
    Route::get('/plan-selections/create', [PlanSelectionController::class, 'create'])->name('plan-selections.create');
    Route::post('/plan-selections', [PlanSelectionController::class, 'store'])->name('plan-selections.store');
    Route::get('/plan-selections/success', [PlanSelectionController::class, 'success'])->name('plan-selections.success');

    // Wallet and Transaction Routes
    Route::post('/wallet/verify-transaction', [TransactionController::class, 'verifyTransaction'])->name('wallet.verify');
    Route::get('/wallet/transaction-status/{txHash}', [TransactionController::class, 'getTransactionStatus'])->name('wallet.status');
    Route::get('/wallet/balance/{address}', [TransactionController::class, 'getBSCBalance'])->name('wallet.balance');
    Route::get('/wallet/transactions', [TransactionController::class, 'getTransactionHistory'])->name('wallet.transactions');
    Route::post('/wallet/transactions', [TransactionController::class, 'storeTransaction'])->name('wallet.store');
    Route::patch('/wallet/transactions/{txHash}', [TransactionController::class, 'updateTransactionStatus'])->name('wallet.update');
});


// Main User Dashboard Route
Route::get('/user', function () {
    return view('user');
})->name('user.dashboard');


Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/Admin-dashboard', [AdminController::class, 'index'])->name('admin.index');

    // Admin User Details
    Route::get('/Admin-dashboard/userdetails', [AdminUserDetailController::class, 'index'])
        ->name('admin.userdetails.index');
    Route::get('/user-details', [AdminUserDetailController::class, 'index']);

    // Status update (POST ya PATCH dono chalenge)
    Route::match(['post', 'patch'], '/admin/user-details/{user}/status', [AdminUserDetailController::class, 'updateStatus']);

    // User Plan Details
    Route::get('/admin/user-plan/{userId}', [AdminController::class, 'showUserPlan'])->name('admin.user-plan.show');

    // Admin Wallet Routes
    Route::get('/admin/wallet', [AdminWalletController::class, 'index'])->name('admin.wallet.index');
    Route::post('/admin/wallet/connect', [AdminWalletController::class, 'connectWallet'])->name('admin.wallet.connect');
    Route::post('/admin/wallet/balance', [AdminWalletController::class, 'getBalance'])->name('admin.wallet.balance');
    Route::post('/admin/wallet/send', [AdminWalletController::class, 'sendTransaction'])->name('admin.wallet.send');
    Route::get('/admin/wallet/transactions', [AdminWalletController::class, 'getTransactionHistory'])->name('admin.wallet.transactions');
    Route::patch('/admin/user-plan/{planId}/status', [AdminController::class, 'updatePlanStatus'])->name('admin.user-plan.update-status');

    // Investment Plans Management
    Route::get('/admin/investment-plans', [AdminInvestmentPlanController::class, 'index'])->name('admin.investmentplans.index');
    Route::post('/admin/investment-plans/{userInvestment}/status', [AdminInvestmentPlanController::class, 'updateUserInvestmentStatus'])->name('admin.investmentplans.updateStatus');
    Route::post('/admin/investment-plans/{userInvestment}/plan', [AdminInvestmentPlanController::class, 'updateUserPlan'])->name('admin.investmentplans.updatePlan');

    // Plan Selection Management
    Route::get('/admin/plan-selections', [PlanSelectionController::class, 'adminIndex'])->name('admin.plan-selections.index');
    Route::get('/admin/plan-selections/{planSelection}', [PlanSelectionController::class, 'adminShow'])->name('admin.plan-selections.show');
    Route::patch('/admin/plan-selections/{planSelection}/status', [PlanSelectionController::class, 'updateStatus'])->name('admin.plan-selections.update-status');


    // Admin Setting
    Route::get('/admin/setting', [AdminProfileController::class, 'edit'])->name('admin.setting.index');
    Route::post('/admin/setting', [AdminProfileController::class, 'update'])->name('admin.setting.update');


    // Debug route for plan selections
    Route::get('/admin/debug-plan-selections', function () {
        $selections = \App\Models\PlanSelection::with(['user'])->get();
        return response()->json([
            'count' => $selections->count(),
            'selections' => $selections->toArray()
        ]);
    });
});
