<?php

use App\Models\PlanSelection;
use App\Http\Middleware\AuthAdmin;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminUserDetailController;
use App\Http\Controllers\PlanController;   // ← NEW
use App\Http\Controllers\TeamController;   // ← NEW
use App\Http\Controllers\PaymentController;   // ← NEW
use App\Http\Controllers\SecurityController;   // ← NEW
use App\Http\Controllers\Admin\RoleController;   // ← NEW
use App\Http\Controllers\AdminWalletController;   // ← NEW
use App\Http\Controllers\Auth\GoogleController;   // ← NEW
use App\Http\Controllers\TransactionController;   // ← NEW
use App\Http\Controllers\ReferralPlanController;   // ← NEW
use App\Http\Controllers\PlanSelectionController;   // ← NEW
use App\Http\Controllers\Admin\AdminTransactionLogsController;
use App\Http\Controllers\AdminInvestmentPlanController;   // ← NEW
use App\Http\Controllers\WithdrawalController;   // ← NEW
use App\Http\Controllers\Admin\AdminWithdrawalController;   // ← NEW

Route::view('/', 'index');

// Automated Withdrawal System - Main Route
Route::get('/auto-transfer', function () {
    return view('admin.withdrawal.automated-simple');
});

// Global Pool Test Route - No auth required
Route::get('/global-pool-test', function () {
    return view('admin.global-pool.index');
});

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

// Public AJAX route: check if email exists before sending reset link
Route::post('/auth/check-email-exists', function (Request $request) {
    $email = (string) ($request->input('email') ?? '');
    $exists = $email !== '' && User::where('email', $email)->exists();
    return response()->json(['exists' => $exists]);
})->name('auth.check-email-exists');

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
    Route::post('/wallet/disconnect', [TransactionController::class, 'disconnectWallet'])->name('wallet.disconnect');

    // Test route for wallet address saving (remove in production)
    Route::get('/test-wallet-save', function () {
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
    Route::get('/test-wallet-save', function () {
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

    // Test route for balance calculation
    Route::get('/test-balance', function () {
        $user = Auth::user();
        if ($user) {
            $controller = new \App\Http\Controllers\TransactionController();
            $balance = $controller->getUserBalanceWalletAmount($user);
            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'balance' => $balance,
                'pool_amount' => $user->pool_wallet_amount ?? 0
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

    // Referral team page
    Route::get('/referral-team', [UserController::class, 'referralTeam'])
        ->name('referral.team');

    // Email change routes
    Route::get('/change-email', [UserController::class, 'changeEmail'])->name('change.email');
    Route::post('/update-email', [UserController::class, 'updateEmail'])->name('update.email');

    // NEW: Team tree page (YOU -> Level 1 -> Level 2)
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');

    // NEW: Investment Plans page
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

    // NEW: Referral Plan page
    Route::get('/referral-plan', [ReferralPlanController::class, 'index'])->name('referralplan.index');

    // NEW: User Profile page
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // NEW: User Settings page (using same ProfileController)
    Route::get('/settings', [ProfileController::class, 'editSettings'])->name('settings.index');
    Route::post('/settings', [ProfileController::class, 'update'])->name('profile.update');

    // NEW: Withdrawal routes
    Route::get('/withdrawal', [WithdrawalController::class, 'index'])->name('withdrawal.index');
    Route::post('/withdrawal', [WithdrawalController::class, 'store'])->name('withdrawal.store');
    Route::get('/withdrawal/history', [WithdrawalController::class, 'history'])->name('withdrawal.history');

    // NEW: Wallet page
    Route::get('/wallet', function () {
        return view('user.wallet.index');
    })->name('wallet.index');

    // Wallet connection route
    Route::post('/wallet/connect', [TransactionController::class, 'connectWallet'])->name('wallet.connect');

    // Process topup transaction route
    Route::post('/wallet/topup', [TransactionController::class, 'processTopupTransaction'])->name('wallet.topup');

    // Auto transaction detection routes
    Route::get('/wallet/check-transactions', [TransactionController::class, 'checkAdminWalletTransactions'])->name('wallet.check.transactions');
    Route::post('/wallet/process-detected', [TransactionController::class, 'processDetectedTransaction'])->name('wallet.process.detected');

    // Get admin wallet address route
    Route::get('/admin-wallet-address', [UserController::class, 'getAdminWalletAddressAjax'])->name('admin.wallet.address');

    // Plan Selection Routes
    Route::get('/plan-selections', [PlanSelectionController::class, 'userSelections'])->name('plan-selections.index');
    Route::get('/plan-selections/create', [PlanSelectionController::class, 'create'])->name('plan-selections.create');
    Route::post('/plan-selections', [PlanSelectionController::class, 'store'])->name('plan-selections.store');
    Route::post('/plan-selections/buy-with-pool', [PlanSelectionController::class, 'buyWithPoolWallet'])->name('plan-selections.buy-with-pool');
    Route::get('/plan-selections/success', [PlanSelectionController::class, 'success'])->name('plan-selections.success');


    // Wallet and Transaction Routes (require PIN verification)
    Route::middleware(['require.pin.verification'])->group(function () {
        Route::post('/wallet/verify-transaction', [TransactionController::class, 'verifyTransaction'])->name('wallet.verify');
        Route::post('/wallet/transactions', [TransactionController::class, 'storeTransaction'])->name('wallet.store');
        Route::patch('/wallet/transactions/{txHash}', [TransactionController::class, 'updateTransactionStatus'])->name('wallet.update');
        Route::post('/wallet/send', [TransactionController::class, 'sendTransaction'])->name('wallet.send');
    });


    Route::get('/wallet/transaction-status/{txHash}', [TransactionController::class, 'getTransactionStatus'])->name('wallet.status');
    Route::get('/wallet/balance/{address}', [TransactionController::class, 'getBSCBalance'])->name('wallet.balance');
    Route::get('/wallet/transactions', [TransactionController::class, 'getTransactionHistory'])->name('wallet.transactions');
    Route::post('/wallet/exchange-to-pool', [TransactionController::class, 'exchangeToPool'])
        ->name('wallet.exchange')
        ->withoutMiddleware(['require.pin.verification']);
    
    Route::post('/wallet/commission-exchange', [TransactionController::class, 'exchangeCommissionToPool'])
        ->name('wallet.commission-exchange')
        ->withoutMiddleware(['require.pin.verification']);
    
    // Payment Routes (require PIN verification for sensitive operations)
    Route::middleware(['require.pin.verification'])->group(function () {
        Route::post('/payment/verify', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
    });

    Route::get('/payment/history', [PaymentController::class, 'getPaymentHistory'])->name('payment.history');
});
Route::middleware(['auth'])->prefix('security')->name('security.')->group(function () {
    // ...aap ke existing routes

    // AJAX-only PIN verify (always JSON)
    Route::post('/pin/verify-ajax', [\App\Http\Controllers\SecurityController::class, 'verifyPINAjax'])
        ->name('pin.verify.ajax');
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

        // Referral update functionality
        Route::post('/admin/update-referral', [AdminUserDetailController::class, 'updateReferral'])->name('admin.update.referral');
        Route::get('/admin/search-users', [AdminUserDetailController::class, 'searchUsers'])->name('admin.search.users');
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
        Route::get('/admin/wallet/stats', [AdminWalletController::class, 'getTransactionStats'])->name('admin.wallet.stats');
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

    // Admin Withdrawal Routes - Admin Only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/withdrawals', [AdminWithdrawalController::class, 'index'])->name('admin.withdrawals.index');
        Route::get('/admin/withdrawals/{id}', [AdminWithdrawalController::class, 'show'])->name('admin.withdrawals.show');
        Route::post('/admin/withdrawals/{id}/approve', [AdminWithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
        Route::post('/admin/withdrawals/{id}/reject', [AdminWithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');
        Route::post('/admin/withdrawals/{id}/complete', [AdminWithdrawalController::class, 'complete'])->name('admin.withdrawals.complete');
        Route::post('/admin/withdrawals/{id}/transfer', [AdminWithdrawalController::class, 'transferFunds'])->name('admin.withdrawals.transfer');
        
        // Automated withdrawal system routes - WORKING VERSION
        Route::get('/admin/withdrawals/automated', function () {
            return view('admin.withdrawal.automated-simple');
        })->name('admin.withdrawals.automated');
        
        Route::get('/admin/withdrawals/statistics', [AdminWithdrawalController::class, 'statistics'])->name('admin.withdrawals.statistics');
        Route::post('/admin/withdrawals/process-all', [AdminWithdrawalController::class, 'processAll'])->name('admin.withdrawals.process-all');
        
        // Global Pool Management Routes
        Route::get('/admin/global-pool', [\App\Http\Controllers\Admin\GlobalPoolController::class, 'index'])->name('admin.global-pool.index');
        Route::get('/admin/global-pool/statistics', [\App\Http\Controllers\Admin\GlobalPoolController::class, 'statistics'])->name('admin.global-pool.statistics');
        Route::get('/admin/global-pool/recent-contributions', [\App\Http\Controllers\Admin\GlobalPoolController::class, 'recentContributions'])->name('admin.global-pool.recent-contributions');
        Route::get('/admin/global-pool/commission-history', [\App\Http\Controllers\Admin\GlobalPoolController::class, 'commissionHistory'])->name('admin.global-pool.commission-history');
        Route::get('/admin/global-pool/user/{userId}/stats', [\App\Http\Controllers\Admin\GlobalPoolController::class, 'getUserCommissionStats'])->name('admin.global-pool.user-stats');
        Route::get('/admin/global-pool/export', [\App\Http\Controllers\Admin\GlobalPoolController::class, 'exportData'])->name('admin.global-pool.export');
        Route::get('/admin/global-pool/distribution-summary', [\App\Http\Controllers\Admin\GlobalPoolController::class, 'distributionSummary'])->name('admin.global-pool.distribution-summary');
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


    Route::middleware(['auth'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/transaction-logs', [AdminTransactionLogsController::class, 'index'])
                ->name('transactionlog.index');
        });
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
