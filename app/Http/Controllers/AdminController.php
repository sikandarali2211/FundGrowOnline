<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PlanPayment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalUsers      = User::count();
        $newUsersToday   = User::whereDate('created_at', today())->count();
        $newUsers7Days   = User::where('created_at', '>=', now()->subDays(7))->count();

        return view('admin.index', compact('totalUsers', 'newUsersToday', 'newUsers7Days'));
    }
    public function showUserPlan($userId)
    {
        // Fetch the user and their selected plan
        $user = User::findOrFail($userId);
        $plan = PlanPayment::where('user_id', $userId)->latest()->first(); // Assumes the latest plan is the active one

        return view('admin.user_plan', compact('user', 'plan'));
    }

    public function updatePlanStatus(Request $request, $planId)
    {
        // Find the plan and update its status
        $plan = PlanPayment::findOrFail($planId);
        $plan->status = $request->status; // status can be 'active', 'blocked', or 'rejected'
        $plan->save();

        return redirect()->back()->with('success', 'Plan status updated successfully');
    }
}
