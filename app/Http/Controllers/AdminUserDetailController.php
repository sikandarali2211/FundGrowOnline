<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserDetailController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->with([
                'activationInfo:id,user_id,status',
                'referrer:id,name,email' // 👈 referrerUser ki jagah referrer
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
}
