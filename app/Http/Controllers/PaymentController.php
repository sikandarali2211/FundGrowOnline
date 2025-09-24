<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\UserInvestment;
use App\Models\InvestmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display payment form for plan selection
     */
    public function showPaymentForm($planId)
    {
        try {
            // Handle temporary plan data from query parameters
            if (strpos($planId, 'temp') === 0) {
                $plan = [
                    'id' => $planId,
                    'name' => request('plan', 'Grower Plan'),
                    'amount' => request('amount', 10),
                    'return_percentage' => request('return', 0),
                    'duration_days' => request('duration', 30),
                ];
            } else {
                $plan = InvestmentPlan::findOrFail($planId);
            }
            
        $user = Auth::user();
        
        // Get admin wallet address
        $admin = \App\Models\User::where('utype', 'ADM')->first();
        $adminWalletAddress = $admin ? $admin->wallet_address : '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6';
        
        return view('user.payment.form', compact('plan', 'user', 'adminWalletAddress'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'planId' => $planId,
                'request_data' => request()->all()
            ], 500);
        }
    }

    /**
     * Process payment transaction
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|string',
            'transaction_hash' => 'required|string|unique:payment_transactions,transaction_hash',
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|in:BNB,USDT',
        ]);

        try {
            DB::beginTransaction();
            
            // Debug: Log the incoming request
            \Log::info('Payment Request Data:', [
                'plan_id' => $request->plan_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'from_address' => $request->from_address,
                'to_address' => $request->to_address,
            ]);

            // Handle temporary plan data
            if (strpos($request->plan_id, 'temp') === 0) {
                // For temporary plans, find or create a default plan
                $defaultPlan = InvestmentPlan::where('name', 'Grower Plan')->first();
                if (!$defaultPlan) {
                    // Create a default plan if it doesn't exist
                    $defaultPlan = InvestmentPlan::create([
                        'name' => 'Grower Plan',
                        'amount' => $request->amount,
                        'return_percentage' => 0,
                        'duration_days' => 30,
                        'is_active' => true,
                    ]);
                }
                $planData = [
                    'id' => $defaultPlan->id,
                    'name' => $defaultPlan->name,
                    'amount' => $request->amount,
                    'return_percentage' => $defaultPlan->return_percentage,
                    'duration_days' => $defaultPlan->duration_days,
                ];
            } else {
                $plan = InvestmentPlan::findOrFail($request->plan_id);
                $planData = [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'amount' => $plan->amount,
                    'return_percentage' => $plan->return_percentage,
                    'duration_days' => $plan->duration_days,
                ];
            }

            $user = Auth::user();

            // Create payment transaction
            $transaction = PaymentTransaction::create([
                'user_id' => $user->id,
                'plan_id' => $planData['id'],
                'transaction_hash' => $request->transaction_hash,
                'from_address' => $request->from_address,
                'to_address' => $request->to_address,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'status' => PaymentTransaction::STATUS_PENDING,
                'expires_at' => now()->addHours(24), // 24 hours expiry
            ]);

            // Create user investment record
            $userInvestment = UserInvestment::create([
                'user_id' => $user->id,
                'plan_id' => $planData['id'] ?? 1, // Fallback to plan ID 1 if null
                'amount' => $planData['amount'],
                'status' => 'pending',
                'payment_transaction_id' => $transaction->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment submitted successfully! Waiting for confirmation.',
                'transaction_id' => $transaction->id,
                'transaction_hash' => $transaction->transaction_hash,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify payment transaction
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'transaction_hash' => 'required|string',
        ]);

        try {
            $transaction = PaymentTransaction::where('transaction_hash', $request->transaction_hash)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Here you would typically verify the transaction on blockchain
            // For now, we'll simulate verification
            $isVerified = $this->verifyTransactionOnBlockchain($transaction);

            if ($isVerified) {
                $transaction->markAsConfirmed();
                
                // Update user investment status
                $userInvestment = UserInvestment::where('payment_transaction_id', $transaction->id)->first();
                if ($userInvestment) {
                    $userInvestment->update(['status' => 'active']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully!',
                    'status' => 'confirmed',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed',
                    'status' => 'pending',
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user payment history
     */
    public function getPaymentHistory()
    {
        $user = Auth::user();
        $transactions = PaymentTransaction::where('user_id', $user->id)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Admin: Get all pending payments
     */
    public function getPendingPayments()
    {
        $transactions = PaymentTransaction::with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payments.index', compact('transactions'));
    }

    /**
     * Admin: Confirm payment
     */
    public function confirmPayment(Request $request, $transaction_id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $transaction = PaymentTransaction::findOrFail($transaction_id);
            $transaction->markAsConfirmed();
            $transaction->update(['admin_notes' => $request->admin_notes]);

            // Update user investment status
            $userInvestment = UserInvestment::where('payment_transaction_id', $transaction->id)->first();
            if ($userInvestment) {
                $userInvestment->update(['status' => 'active']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Confirmation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin: Reject payment
     */
    public function rejectPayment(Request $request, $transaction_id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        try {
            $transaction = PaymentTransaction::findOrFail($transaction_id);
            $transaction->markAsFailed();
            $transaction->update(['admin_notes' => $request->admin_notes]);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get admin wallet address (you can configure this)
     */
    private function getAdminWalletAddress()
    {
        // This should be configured in your environment or database
        return config('app.admin_wallet_address', '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6');
    }

    /**
     * Verify transaction on blockchain (placeholder)
     */
    private function verifyTransactionOnBlockchain(PaymentTransaction $transaction)
    {
        // Here you would implement actual blockchain verification
        // For now, we'll return true for demonstration
        return true;
    }
}