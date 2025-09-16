<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanSelection;
use Illuminate\Support\Facades\Auth;

class PlanSelectionController extends Controller
{
    /**
     * Store a newly created plan selection
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_amount' => 'required|numeric|min:0.01',
            'return_percentage' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ]);

        $expectedReturn = $request->plan_amount * (1 + $request->return_percentage / 100);

        $planSelection = PlanSelection::create([
            'user_id' => Auth::id(),
            'plan_name' => $request->plan_name,
            'plan_amount' => $request->plan_amount,
            'return_percentage' => $request->return_percentage,
            'duration_days' => $request->duration_days,
            'expected_return' => $expectedReturn,
            'status' => PlanSelection::STATUS_PENDING,
        ]);

        return redirect()->route('user.plan-selections.success')
            ->with('success', 'Plan selection submitted successfully! Admin will review your request.');
    }

    /**
     * Display user's plan selections
     */
    public function userSelections()
    {
        $selections = PlanSelection::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.plan-selections.index', compact('selections'));
    }

    /**
     * Display success page
     */
    public function success()
    {
        return view('user.plan-selections.success');
    }

    /**
     * Display admin panel for plan selections
     */
    public function adminIndex()
    {
        $selections = PlanSelection::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

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
            $planSelection->approve(Auth::id(), $request->admin_notes);
            $message = 'Plan selection approved successfully!';
        } else {
            $planSelection->reject(Auth::id(), $request->admin_notes);
            $message = 'Plan selection rejected.';
        }

        return back()->with('success', $message);
    }
}

