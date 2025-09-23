<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'status' => ['required', Rule::in(['Active', 'Rejected', 'Blocked', 'Pending'])],
        ]);

        $activationInfo = $user->activationInfo()->first() ?? $user->activationInfo()->make();
        $activationInfo->status = $data['status'];
        $activationInfo->save();

        return back()->with('success', "Status updated to {$data['status']} for {$user->name}.");
    }

    // 👇 NEW: login as user functionality
    public function loginAsUser(User $user)
    {
        try {
            // Store admin user ID for later restoration
            session(['admin_user_id' => Auth::id()]);
            
            // Login as the selected user
            Auth::login($user);
            
            return redirect('/User-dashboard')->with('success', "Logged in as {$user->name}");
            
        } catch (\Exception $e) {
            return back()->with('error', "Failed to login as {$user->name}: " . $e->getMessage());
        }
    }

    // 👇 NEW: restore admin login
    public function restoreAdminLogin()
    {
        try {
            $adminUserId = session('admin_user_id');
            
            if ($adminUserId) {
                $adminUser = User::find($adminUserId);
                if ($adminUser) {
                    Auth::login($adminUser);
                    session()->forget('admin_user_id');
                    return redirect('/admin/user-details')->with('success', 'Admin login restored');
                }
            }
            
            return redirect('/admin/login')->with('error', 'Unable to restore admin login');
            
        } catch (\Exception $e) {
            return redirect('/admin/login')->with('error', 'Failed to restore admin login: ' . $e->getMessage());
        }
    }

    // 👇 NEW: delete user functionality
    public function deleteUser(User $user)
    {
        try {
            DB::beginTransaction();

            // Check if user has referrals
            $referralCount = $user->referrals()->count();
            if ($referralCount > 0) {
                return back()->with('error', "Cannot delete {$user->name}. User has {$referralCount} referrals. Please reassign referrals first.");
            }

            // Check if user has investments
            $investmentCount = $user->userInvestments()->count();
            if ($investmentCount > 0) {
                return back()->with('error', "Cannot delete {$user->name}. User has {$investmentCount} active investments. Please handle investments first.");
            }

            // Store user name for success message
            $userName = $user->name;
            $userEmail = $user->email;

            // Delete related data
            $user->activationInfo()->delete();
            $user->userInvestments()->delete();
            $user->transactions()->delete();
            $user->planSelections()->delete();

            // Delete the user
            $user->delete();

            DB::commit();

            return back()->with('success', "User {$userName} ({$userEmail}) has been permanently deleted.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Failed to delete {$user->name}: " . $e->getMessage());
        }
    }
}
