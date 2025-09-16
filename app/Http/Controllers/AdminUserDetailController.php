<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // 👈 add this

class AdminUserDetailController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->with([
                'activationInfo:id,user_id,status',
                'referrer:id,name,email'
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('referral_code', 'like', "%{$q}%")
                        ->orWhere('referred_by', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.userdetail.index', compact('users', 'q'));
    }

    // 👇 NEW: update status handler
    public function updateStatus(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => ['required', \Illuminate\Validation\Rule::in(['Active', 'Rejected', 'Blocked'])],
        ]);

        $activationInfo = $user->activationInfo()->first() ?? $user->activationInfo()->make();
        $activationInfo->status = $data['status'];
        $activationInfo->save();

        return back()->with('success', "Status updated to {$data['status']} for {$user->name}.");
    }
}
