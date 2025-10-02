<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPlan;
use App\Models\UserInvestment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminInvestmentPlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = InvestmentPlan::with(['userInvestments.user'])
            ->withCount('userInvestments')
            ->get();

        // Get user investments with user details - with pagination
        $userInvestments = UserInvestment::with(['user', 'investmentPlan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10) // Show 10 investments per page
            ->withQueryString(); // Preserve query parameters

        // Debug information
        \Log::info('Investment Plans Pagination Debug', [
            'total_investments' => $userInvestments->total(),
            'current_page' => $userInvestments->currentPage(),
            'last_page' => $userInvestments->lastPage(),
            'has_pages' => $userInvestments->hasPages(),
            'per_page' => $userInvestments->perPage(),
            'first_item' => $userInvestments->firstItem(),
            'last_item' => $userInvestments->lastItem()
        ]);

        return view('admin.investmentplans.index', compact('plans', 'userInvestments'));
    }

    public function updateUserInvestmentStatus(Request $request, UserInvestment $userInvestment)
    {
        $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled'
        ]);

        $userInvestment->update([
            'status' => $request->status,
            'invested_at' => $request->status === 'active' ? now() : $userInvestment->invested_at,
            'maturity_date' => $request->status === 'active' ? now()->addDays(30) : $userInvestment->maturity_date
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Investment status updated successfully!',
            'status' => $userInvestment->status
        ]);
    }

    public function updateUserPlan(Request $request, UserInvestment $userInvestment)
    {
        $request->validate([
            'investment_plan_id' => 'required|exists:investment_plans,id'
        ]);

        $newPlan = InvestmentPlan::findOrFail($request->investment_plan_id);
        
        $userInvestment->update([
            'investment_plan_id' => $request->investment_plan_id,
            'amount' => $newPlan->entry_amount,
            'return_amount' => $newPlan->total_return
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User plan updated successfully!',
            'plan_name' => $newPlan->name
        ]);
    }
}








