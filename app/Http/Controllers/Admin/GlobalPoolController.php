<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalPool;
use App\Models\CommissionTransaction;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GlobalPoolController extends Controller
{
    protected CommissionService $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Show global pool dashboard
     */
    public function index()
    {
        return view('admin.global-pool.index');
    }

    /**
     * Get global pool statistics
     */
    public function statistics()
    {
        try {
            $stats = $this->commissionService->getGlobalPoolStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get global pool statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent global pool contributions
     */
    public function recentContributions()
    {
        try {
            $contributions = CommissionTransaction::with(['user'])
                ->where('global_pool_commission', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Add commission type text to each contribution
            $contributions->each(function ($contribution) {
                $contribution->commission_type_text = $contribution->commission_type_text;
            });

            return response()->json([
                'success' => true,
                'data' => $contributions
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get recent contributions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get commission history
     */
    public function commissionHistory(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);

            $transactions = CommissionTransaction::with(['user', 'planSelection'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get commission history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user commission statistics
     */
    public function getUserCommissionStats(Request $request, $userId)
    {
        try {
            $user = \App\Models\User::findOrFail($userId);
            $stats = $this->commissionService->getUserCommissionStats($user);
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get user commission stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export global pool data
     */
    public function exportData(Request $request)
    {
        try {
            $format = $request->get('format', 'json');
            
            $data = [
                'global_pool_stats' => $this->commissionService->getGlobalPoolStats(),
                'recent_contributions' => CommissionTransaction::with(['user'])
                    ->where('global_pool_commission', '>', 0)
                    ->orderBy('created_at', 'desc')
                    ->limit(100)
                    ->get(),
                'export_date' => now()->toISOString()
            ];

            if ($format === 'csv') {
                // Generate CSV
                $csv = "Date,User,Amount,Type,Description\n";
                foreach ($data['recent_contributions'] as $contribution) {
                    $csv .= sprintf(
                        "%s,%s,%.2f,%s,%s\n",
                        $contribution->created_at->format('Y-m-d H:i:s'),
                        $contribution->user->name,
                        $contribution->global_pool_commission,
                        $contribution->commission_type,
                        $contribution->description
                    );
                }

                return response($csv)
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="global_pool_data.csv"');
            } else {
                // Return JSON
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to export global pool data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get commission distribution summary
     */
    public function distributionSummary()
    {
        try {
            $summary = [
                'total_pool_commissions' => CommissionTransaction::sum('pool_commission'),
                'total_profit_commissions' => CommissionTransaction::sum('profit_commission'),
                'total_global_pool_commissions' => CommissionTransaction::sum('global_pool_commission'),
                'second_plan_commissions' => CommissionTransaction::where('commission_type', 'second_plan')->sum('total_commission'),
                'referral_chain_commissions' => CommissionTransaction::where('commission_type', 'referral_chain')->sum('total_commission'),
                'total_transactions' => CommissionTransaction::count(),
                'unique_users' => CommissionTransaction::distinct('user_id')->count('user_id')
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get distribution summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
