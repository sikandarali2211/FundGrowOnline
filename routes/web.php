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
use App\Http\Controllers\PaymentController;   // ← NEW
use App\Http\Controllers\SecurityController;   // ← NEW
use App\Http\Controllers\Admin\RoleController;   // ← NEW

Route::view('/', 'index');

// Test route for plan selections (without admin middleware)
Route::get('/test-plan-selections', function () {
    $selections = \App\Models\PlanSelection::with(['user'])->get();
    return view('admin.plan-selections.index', compact('selections'));
});

// Test route for email OTP (remove in production)
Route::get('/test-email-otp', function () {
    $user = \App\Models\User::first();
    if ($user) {
        $results = $user->sendOTP(['email']);
        return response()->json([
            'user' => $user->name . ' (' . $user->email . ')',
            'email_result' => $results['email'] ? 'Success' : 'Failed',
            'message' => $results['email'] ? 'OTP sent to email successfully!' : 'Failed to send email OTP'
        ]);
    }
    return response()->json(['error' => 'No users found']);
});


// 
Auth::routes();

Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');
/* 
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');

*/

// Security PIN routes
Route::middleware(['auth'])->prefix('security')->name('security.')->group(function () {
    Route::get('/pin/setup', [SecurityController::class, 'showPINSetup'])->name('pin.setup');
    Route::post('/pin/setup', [SecurityController::class, 'setupPIN'])->name('pin.setup.store');
    Route::post('/pin/send-otp', [SecurityController::class, 'sendOTPForPINSetup'])->name('pin.send-otp');
    
    Route::get('/pin/verify', [SecurityController::class, 'showPINVerification'])->name('pin.verify');
    Route::post('/pin/verify', [SecurityController::class, 'verifyPIN'])->name('pin.verify.store');
    
    Route::get('/pin/change', [SecurityController::class, 'showPINChange'])->name('pin.change');
    Route::post('/pin/change', [SecurityController::class, 'changePIN'])->name('pin.change.store');
    
    Route::post('/pin/clear-verification', [SecurityController::class, 'clearPINVerification'])->name('pin.clear-verification');
});

// Wallet save route (only auth required, no PIN setup required)
Route::middleware(['auth'])->prefix('User-dashboard')->group(function () {
    Route::post('/wallet/save-address', [TransactionController::class, 'saveWalletAddress'])->name('wallet.save.address');
    
    // Test route for wallet address saving (remove in production)
    Route::get('/test-wallet-save', function() {
        $user = Auth::user();
        if ($user) {
            return response()->json([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'current_wallet_address' => $user->wallet_address,
                'message' => 'User authenticated, ready to save wallet address'
            ]);
        }
        return response()->json(['error' => 'User not authenticated']);
    });
});


// Fallback wallet save route (without prefix for DApp browser compatibility)
Route::middleware(['auth'])->group(function () {
    Route::post('/wallet/save-address', [TransactionController::class, 'saveWalletAddress'])->name('wallet.save.address.fallback');
    
    // Test route without prefix
    Route::get('/test-wallet-save', function() {
        $user = Auth::user();
        if ($user) {
            return response()->json([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'current_wallet_address' => $user->wallet_address,
                'message' => 'User authenticated, ready to save wallet address (fallback route)'
            ]);
        }
        return response()->json(['error' => 'User not authenticated']);
    });
});

Route::middleware(['auth', 'require.pin.setup'])->prefix('User-dashboard')->name('user.')->group(function () {
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


    // Wallet and Transaction Routes (require PIN verification)
    Route::middleware(['require.pin.verification'])->group(function () {
    Route::post('/wallet/verify-transaction', [TransactionController::class, 'verifyTransaction'])->name('wallet.verify');
        Route::post('/wallet/transactions', [TransactionController::class, 'storeTransaction'])->name('wallet.store');
        Route::patch('/wallet/transactions/{txHash}', [TransactionController::class, 'updateTransactionStatus'])->name('wallet.update');
    });
    
    
    Route::get('/wallet/transaction-status/{txHash}', [TransactionController::class, 'getTransactionStatus'])->name('wallet.status');
    Route::get('/wallet/balance/{address}', [TransactionController::class, 'getBSCBalance'])->name('wallet.balance');
    Route::get('/wallet/transactions', [TransactionController::class, 'getTransactionHistory'])->name('wallet.transactions');

    // Payment Routes (require PIN verification for sensitive operations)
    Route::middleware(['require.pin.verification'])->group(function () {
    Route::post('/payment/verify', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
    });
    
    Route::get('/payment/history', [PaymentController::class, 'getPaymentHistory'])->name('payment.history');
});

// Public Payment Routes (accessible without authentication)
Route::get('/payment/{planId}', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');


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

    // User action routes - Admin Only
    Route::middleware(['role:admin'])->group(function () {
    // Status update (POST ya PATCH dono chalenge)
    Route::match(['post', 'patch'], '/admin/user-details/{user}/status', [AdminUserDetailController::class, 'updateStatus']);
    
    // User login as admin functionality
    Route::post('/admin/user-details/{user}/login', [AdminUserDetailController::class, 'loginAsUser'])->name('admin.user.login');
    Route::get('/admin/restore-login', [AdminUserDetailController::class, 'restoreAdminLogin'])->name('admin.restore.login');
    
    // User delete functionality
    Route::delete('/admin/user-details/{user}/delete', [AdminUserDetailController::class, 'deleteUser'])->name('admin.user.delete');
    });

    // User Plan Details
    Route::get('/admin/user-plan/{userId}', [AdminController::class, 'showUserPlan'])->name('admin.user-plan.show');

    // Admin Wallet Routes - Admin Only
    Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/wallet', [AdminWalletController::class, 'index'])->name('admin.wallet.index');
    Route::post('/admin/wallet/connect', [AdminWalletController::class, 'connectWallet'])->name('admin.wallet.connect');
    Route::post('/admin/wallet/save-address', [AdminWalletController::class, 'saveWalletAddress'])->name('admin.wallet.save-address');
    Route::post('/admin/wallet/balance', [AdminWalletController::class, 'getBalance'])->name('admin.wallet.balance');
    Route::post('/admin/wallet/send', [AdminWalletController::class, 'sendTransaction'])->name('admin.wallet.send');
    Route::get('/admin/wallet/transactions', [AdminWalletController::class, 'getTransactionHistory'])->name('admin.wallet.transactions');
    });

    // Admin Payment Routes - Admin Only
    Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/payments', [PaymentController::class, 'getPendingPayments'])->name('admin.payments.index');
    Route::post('/admin/payments/{transaction_id}/confirm', [PaymentController::class, 'confirmPayment'])->name('admin.payments.confirm');
    Route::post('/admin/payments/{transaction_id}/reject', [PaymentController::class, 'rejectPayment'])->name('admin.payments.reject');
    Route::patch('/admin/user-plan/{planId}/status', [AdminController::class, 'updatePlanStatus'])->name('admin.user-plan.update-status');
    });

    // Investment Plans Management - Admin Only
    Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/investment-plans', [AdminInvestmentPlanController::class, 'index'])->name('admin.investmentplans.index');
    Route::post('/admin/investment-plans/{userInvestment}/status', [AdminInvestmentPlanController::class, 'updateUserInvestmentStatus'])->name('admin.investmentplans.updateStatus');
    Route::post('/admin/investment-plans/{userInvestment}/plan', [AdminInvestmentPlanController::class, 'updateUserPlan'])->name('admin.investmentplans.updatePlan');
    });

    // Plan Selection Management - Admin Only
    Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/plan-selections', [PlanSelectionController::class, 'adminIndex'])->name('admin.plan-selections.index');
    Route::get('/admin/plan-selections/{planSelection}', [PlanSelectionController::class, 'adminShow'])->name('admin.plan-selections.show');
    Route::patch('/admin/plan-selections/{planSelection}/status', [PlanSelectionController::class, 'updateStatus'])->name('admin.plan-selections.update-status');
    });

    // Admin Setting - Admin Only
    Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/setting', [AdminProfileController::class, 'edit'])->name('admin.setting.index');
    Route::post('/admin/setting', [AdminProfileController::class, 'update'])->name('admin.setting.update');
    });

    // Role Management Routes - Admin Only
    Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/role', [RoleController::class, 'index'])->name('admin.role.index');
    Route::post('/admin/role/assign', [RoleController::class, 'assignRole'])->name('admin.role.assign');
    Route::put('/admin/role/{user}', [RoleController::class, 'updateRole'])->name('admin.role.update');
    Route::delete('/admin/role/{user}', [RoleController::class, 'removeRole'])->name('admin.role.remove');
    Route::get('/admin/role/users-by-role', [RoleController::class, 'getUsersByRole'])->name('admin.role.users-by-role');
    Route::get('/admin/role/stats', [RoleController::class, 'getRoleStats'])->name('admin.role.stats');
    });


    // Debug route for plan selections
    Route::get('/admin/debug-plan-selections', function () {
        $selections = \App\Models\PlanSelection::with(['user'])->get();
        return response()->json([
            'count' => $selections->count(),
            'selections' => $selections->toArray()
        ]);
    });
});
