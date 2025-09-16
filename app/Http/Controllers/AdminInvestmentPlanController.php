<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPlan;
use App\Models\UserInvestment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminInvestmentPlanController extends Controller
{
    public function index()
    {
        $plans = InvestmentPlan::with(['userInvestments.user'])
            ->withCount('userInvestments')
            ->get();

        // Get user investments with user details
        $userInvestments = UserInvestment::with(['user', 'investmentPlan'])
            ->orderBy('created_at', 'desc')
            ->get();

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








