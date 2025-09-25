<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWalletController extends Controller
{
    /**
     * Display the admin wallet page
     */
    public function index()
    {
        // Get the current authenticated admin user
        $admin = Auth::user();
        $adminWalletAddress = $admin ? $admin->wallet_address : null;
        
        return view('admin.wallet.index', compact('adminWalletAddress'));
    }

    /**
     * Connect wallet and return wallet address
     */
    public function connectWallet(Request $request)
    {
        try {
            // This will be handled by JavaScript on the frontend
            // The wallet connection happens in the browser
            return response()->json([
                'success' => true,
                'message' => 'Wallet connection initiated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate wallet connection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save admin wallet address
     */
    public function saveWalletAddress(Request $request)
    {
        try {
            // Allow null wallet_address for clearing
            if ($request->wallet_address !== null) {
                $request->validate([
                    'wallet_address' => 'required|string|min:42|max:42'
                ]);
            }

            $admin = Auth::user();
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user not found'
                ], 404);
            }

            $admin->update([
                'wallet_address' => $request->wallet_address
            ]);

            return response()->json([
                'success' => true,
                'message' => $request->wallet_address ? 'Wallet address saved successfully' : 'Wallet address cleared successfully',
                'wallet_address' => $request->wallet_address
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallet balance
     */
    public function getBalance(Request $request)
    {
        try {
            $address = $request->input('address');
            
            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet address is required'
                ], 400);
            }

            // This will be handled by JavaScript on the frontend
            return response()->json([
                'success' => true,
                'message' => 'Balance check initiated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get balance: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Send transaction
     */
    public function sendTransaction(Request $request)
    {
        try {
            $request->validate([
                'to' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'token_address' => 'nullable|string'
            ]);

            // This will be handled by JavaScript on the frontend
            return response()->json([
                'success' => true,
                'message' => 'Transaction initiated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction history for admin
     */
    public function getTransactionHistory(Request $request)
    {
        try {
            $transactions = \App\Models\Transaction::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction statistics for admin dashboard
     */
    public function getTransactionStats()
    {
        try {
            $totalTransactions = \App\Models\Transaction::count();
            $totalAmount = \App\Models\Transaction::where('status', 'confirmed')->sum('amount');
            $pendingTransactions = \App\Models\Transaction::where('status', 'pending')->count();
            $todayTransactions = \App\Models\Transaction::whereDate('created_at', today())->count();
            $todayAmount = \App\Models\Transaction::where('status', 'confirmed')
                ->whereDate('created_at', today())
                ->sum('amount');

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_transactions' => $totalTransactions,
                    'total_amount' => number_format($totalAmount, 2),
                    'pending_transactions' => $pendingTransactions,
                    'today_transactions' => $todayTransactions,
                    'today_amount' => number_format($todayAmount, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
