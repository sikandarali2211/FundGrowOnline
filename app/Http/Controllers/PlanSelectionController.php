<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanSelection;
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

        PlanSelection::create([
            'user_id'          => auth()->id(),
            'plan_name'        => $request->plan_name,
            'plan_amount'      => $request->plan_amount,
            'return_percentage' => $request->return_percentage,
            'duration_days'    => $request->duration_days,
            'expected_return'  => $request->plan_amount * (1 + $request->return_percentage / 100),
            'status'           => 'pending',
        ]);

        return redirect()->route('user.plan-selections.success')
            ->with('success', 'Plan submitted successfully. Awaiting admin approval.');
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
