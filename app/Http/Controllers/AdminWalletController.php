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
        $admin = \App\Models\User::where('utype', 'ADM')->first();
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
            $request->validate([
                'wallet_address' => 'required|string|min:42|max:42'
            ]);

            $admin = \App\Models\User::where('utype', 'ADM')->first();
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
                'message' => 'Wallet address saved successfully',
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
     * Get transaction history
     */
    public function getTransactionHistory(Request $request)
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
                'message' => 'Transaction history check initiated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get transaction history: ' . $e->getMessage()
            ], 500);
        }
    }
}
