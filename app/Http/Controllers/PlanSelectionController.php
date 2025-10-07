<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanSelection;
use App\Services\CommissionService;
use Illuminate\Support\Facades\Auth;

class PlanSelectionController extends Controller
{
    // User ki apni selections
    public function userSelections()
    {
        $plans = PlanSelection::where('user_id', Auth::id())->latest()->get();
        return view('user.plan-selections.index', compact('plans'));
    }

    // Confirm Page
    public function create(Request $request)
    {
        // Query params se data pass
        $plan = [
            'id'               => 'temp_' . uniqid(), // Temporary ID for payment form
            'name'             => $request->get('plan'),
            'amount'           => $request->get('amount'),
            'return_percentage' => $request->get('return'),
            'duration_days'    => $request->get('duration', 30),
        ];

        return view('user.plan-selections.create', compact('plan'));
    }

    // Save Plan Selection
    public function store(Request $request)
    {
        $request->validate([
            'plan_name'        => 'required|string',
            'plan_amount'      => 'required|numeric|min:1',
            'return_percentage' => 'required|numeric|min:0',
            'duration_days'    => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $planAmount = $request->plan_amount;

        // Check if this is user's first plan purchase
        $isFirstPlan = !PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();

        \Log::info('Regular plan purchase attempt', [
            'user_id' => $user->id,
            'plan_amount' => $planAmount,
            'is_first_plan' => $isFirstPlan,
            'has_referrer' => !is_null($user->referred_by)
        ]);

        $planSelection = PlanSelection::create([
            'user_id'          => $user->id,
            'plan_name'        => $request->plan_name,
            'plan_amount'      => $planAmount,
            'return_percentage' => $request->return_percentage,
            'duration_days'    => $request->duration_days,
            'expected_return'  => $planAmount * (1 + $request->return_percentage / 100),
            'status'           => 'pending',
        ]);

        // Process commission system when plan is approved by admin
        // This will be triggered from the admin approval process

        return redirect()->route('user.plan-selections.success')
            ->with('success', 'Plan submitted successfully. Awaiting admin approval.');
    }

    // Buy Plan with Pool Wallet
    public function buyWithPoolWallet(Request $request)
    {
        $request->validate([
            'plan_name'        => 'required|string',
            'plan_amount'      => 'required|numeric|min:1',
            'return_percentage' => 'required|numeric|min:0',
            'duration_days'    => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $planAmount = $request->plan_amount;
        $poolBalance = $user->pool_wallet_amount ?? 0;

        \Log::info('Pool wallet plan purchase attempt', [
            'user_id' => $user->id,
            'plan_amount' => $planAmount,
            'pool_balance' => $poolBalance,
            'request_data' => $request->all()
        ]);

        // Check if user has sufficient pool wallet balance
        if ($poolBalance < $planAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient pool wallet balance. Available: $' . number_format($poolBalance, 2)
            ], 400);
        }

        try {
            // Use database transaction to ensure data consistency
            \DB::transaction(function () use ($user, $request, $planAmount, $poolBalance) {
                // Check if this is user's first plan purchase
                $isFirstPlan = !PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->exists();
                
                \Log::info('First plan check', [
                    'user_id' => $user->id,
                    'is_first_plan' => $isFirstPlan,
                    'has_referrer' => !is_null($user->referred_by)
                ]);
                // Create the plan selection
                $planSelection = PlanSelection::create([
                    'user_id'          => $user->id,
                    'plan_name'        => $request->plan_name,
                    'plan_amount'      => $planAmount,
                    'return_percentage' => $request->return_percentage,
                    'duration_days'    => $request->duration_days,
                    'expected_return'  => $planAmount * (1 + $request->return_percentage / 100),
                    'status'           => 'approved', // Auto-approve for pool wallet purchases
                    'admin_notes'      => 'Purchased using pool wallet',
                    'processed_by'     => $user->id,
                    'processed_at'     => now(),
                ]);

                // Deduct amount from pool wallet
                $user->pool_wallet_amount = $poolBalance - $planAmount;
                
                // Also reduce sent amount to prevent balance wallet from showing amount after plan purchase
                $sentTransaction = \App\Models\Transaction::where('user_id', $user->id)
                    ->where('status', 'confirmed')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($sentTransaction && $sentTransaction->amount >= $planAmount) {
                    $sentTransaction->amount = $sentTransaction->amount - $planAmount;
                    $sentTransaction->save();
                }
                
                $user->save();

                // Get or create a default investment plan
                $defaultPlan = \App\Models\InvestmentPlan::first();
                if (!$defaultPlan) {
                    $defaultPlan = \App\Models\InvestmentPlan::create([
                        'name' => 'Pool Wallet Plan',
                        'description' => 'Default plan for pool wallet purchases',
                        'entry_amount' => 1,
                        'min_amount' => 1,
                        'max_amount' => 10000,
                        'return_percentage' => 0,
                        'total_return' => 0,
                        'duration_days' => 30,
                        'is_active' => true
                    ]);
                }

                // Create user investment record
                \App\Models\UserInvestment::create([
                    'user_id' => $user->id,
                    'investment_plan_id' => $defaultPlan->id,
                    'plan_id' => $defaultPlan->id, // Use investment_plan_id instead of plan_selection_id
                    'amount' => $planAmount,
                    'status' => 'active',
                    'invested_at' => now(),
                    'maturity_date' => now()->addDays($request->duration_days),
                    'return_amount' => $planAmount * ($request->return_percentage / 100),
                ]);

                // Distribute Level 1 commission: $10 fixed commission (60% pool commission, 40% pool wallet)
                if ($isFirstPlan && $user->referred_by) {
                    $referrer = \App\Models\User::find($user->referred_by);
                    if ($referrer) {
                        $level1Commission = 10.00; // Fixed $10 commission
                        $poolCommission = $level1Commission * 0.60; // 60% to pool commission
                        $poolWallet = $level1Commission * 0.40; // 40% to pool wallet
                        
                        // Update referrer's balances
                        $referrer->referral_commission_balance = ($referrer->referral_commission_balance ?? 0) + $poolCommission;
                        $referrer->referral_commission_pool = ($referrer->referral_commission_pool ?? 0) + $poolCommission;
                        $referrer->pool_wallet_amount = ($referrer->pool_wallet_amount ?? 0) + $poolWallet;
                        $referrer->total_commission_earned = ($referrer->total_commission_earned ?? 0) + $level1Commission;
                        $referrer->save();
                        
                        // Record commission transaction
                        \App\Models\CommissionTransaction::create([
                            'user_id' => $referrer->id,
                            'plan_selection_id' => $planSelection->id,
                            'total_commission' => $level1Commission,
                            'pool_commission' => $poolCommission,
                            'profit_commission' => 0,
                            'global_pool_commission' => 0,
                            'commission_type' => \App\Models\CommissionTransaction::TYPE_REFERRAL_CHAIN,
                            'description' => "Level 1 commission from {$user->name}'s first plan"
                        ]);
                        
                        \Log::info("Level 1 commission distributed", [
                            'referrer_id' => $referrer->id,
                            'referrer_name' => $referrer->name,
                            'referred_user_id' => $user->id,
                            'referred_user_name' => $user->name,
                            'plan_amount' => $planAmount,
                            'level1_commission' => $level1Commission,
                            'pool_commission' => $poolCommission,
                            'pool_wallet' => $poolWallet
                        ]);
                    }
                }

                // Process new commission system for second plan and referral chain
                $commissionService = app(CommissionService::class);
                $commissionResults = $commissionService->processAllCommissions($planSelection);
                
                \Log::info("New commission system results", [
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'commission_results' => $commissionResults
                ]);

                \Log::info("Plan purchased with pool wallet", [
                    'user_id' => $user->id,
                    'plan_amount' => $planAmount,
                    'pool_balance_before' => $poolBalance,
                    'pool_balance_after' => $user->pool_wallet_amount,
                    'plan_selection_id' => $planSelection->id,
                    'is_first_plan' => $isFirstPlan,
                    'has_referrer' => !is_null($user->referred_by)
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Plan purchased successfully using pool wallet!',
                'data' => [
                    'plan_name' => $request->plan_name,
                    'plan_amount' => $planAmount,
                    'remaining_pool_balance' => $user->fresh()->pool_wallet_amount
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Failed to purchase plan with pool wallet: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase plan. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Success Page
    public function success()
    {
        return view('user.plan-selections.success');
    }

    public function adminIndex()
    {
        $selections = PlanSelection::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString(); // Preserve query parameters

        // Debug information
        \Log::info('Plan Selections Pagination Debug', [
            'total_selections' => $selections->total(),
            'current_page' => $selections->currentPage(),
            'last_page' => $selections->lastPage(),
            'has_pages' => $selections->hasPages(),
            'per_page' => $selections->perPage(),
            'first_item' => $selections->firstItem(),
            'last_item' => $selections->lastItem()
        ]);

        return view('admin.plan-selections.index', compact('selections'));
    }
    /**
     * Show plan selection details for admin
     */
    public function adminShow(PlanSelection $planSelection)
    {
        $planSelection->load(['user', 'processedBy']);
        return view('admin.plan-selections.show', compact('planSelection'));
    }
    /**
     * Update plan selection status (Admin only)
     */
    public function updateStatus(Request $request, PlanSelection $planSelection)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        if ($request->status === 'approved') {
            // Check if this is user's first approved plan
            $isFirstPlan = !PlanSelection::where('user_id', $planSelection->user_id)
                ->where('status', 'approved')
                ->where('id', '!=', $planSelection->id)
                ->exists();
            
            \Log::info('Admin plan approval', [
                'plan_selection_id' => $planSelection->id,
                'user_id' => $planSelection->user_id,
                'plan_amount' => $planSelection->plan_amount,
                'is_first_plan' => $isFirstPlan,
                'has_referrer' => !is_null($planSelection->user->referred_by)
            ]);
            
            $planSelection->approve(Auth::id(), $request->admin_notes);
            
            // Distribute commission if this is first plan and user has referrer
            if ($isFirstPlan && $planSelection->user->referred_by) {
                $referrer = \App\Models\User::find($planSelection->user->referred_by);
                if ($referrer) {
                    $commissionResult = $referrer->distributeCommission($planSelection->plan_amount, 100); // 100% commission
                    
                    \Log::info("Commission distributed on admin approval", [
                        'referrer_id' => $referrer->id,
                        'referred_user_id' => $planSelection->user_id,
                        'plan_amount' => $planSelection->plan_amount,
                        'commission_result' => $commissionResult
                    ]);
                }
            }

            // Process new commission system for second plan and referral chain
            $commissionService = app(CommissionService::class);
            $commissionResults = $commissionService->processAllCommissions($planSelection);
            
            \Log::info("New commission system results on admin approval", [
                'user_id' => $planSelection->user_id,
                'plan_selection_id' => $planSelection->id,
                'commission_results' => $commissionResults
            ]);
            
            $message = 'Plan selection approved successfully!';
        } else {
            $planSelection->reject(Auth::id(), $request->admin_notes);
            $message = 'Plan selection rejected.';
        }
        return back()->with('success', $message);
    }
}
