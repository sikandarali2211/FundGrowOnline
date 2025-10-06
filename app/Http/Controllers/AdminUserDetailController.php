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
                        ->orWhere('referred_by', 'like', "%{$q}%")
                        ->orWhere('wallet_address', 'like', "%{$q}%")
                        ->orWhere('id', 'like', "%{$q}%")
                        // Search in referrer's name and email
                        ->orWhereHas('referrer', function ($referrerQuery) use ($q) {
                            $referrerQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
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
            $investmentCount = $user->investments()->count();
            if ($investmentCount > 0) {
                return back()->with('error', "Cannot delete {$user->name}. User has {$investmentCount} active investments. Please handle investments first.");
            }

            // Store user name for success message
            $userName = $user->name;
            $userEmail = $user->email;

            // Delete related data
            $user->activationInfo()->delete();
            $user->investments()->delete();
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

    /**
     * Update user's referral
     */
    public function updateReferral(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'new_referrer_id' => 'required|integer|exists:users,id'
            ]);

            $userId = $data['user_id'];
            $newReferrerId = $data['new_referrer_id'];

            // Rule 1: ID order validation (child ID must be greater than parent ID)
            if ($userId <= $newReferrerId) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot update referral: Parent ID {$newReferrerId} must be less than Child ID {$userId}"
                ], 400);
            }

            $user = User::findOrFail($userId);
            $newReferrer = User::findOrFail($newReferrerId);

            // Rule 2: Plan lock validation - check if user has bought any plan
            $hasPlan = $user->planSelections()->where('status', 'approved')->exists() || 
                      $user->investments()->where('status', 'active')->exists();

            if ($hasPlan) {
                return response()->json([
                    'success' => false,
                    'message' => "User's referral cannot be updated. User has already purchased a plan."
                ], 400);
            }

            // Update the referral
            $user->referred_by = $newReferrerId;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$user->name}'s referrer to {$newReferrer->name} (ID: {$newReferrerId})"
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update referral: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search users for referral assignment
     */
    public function searchUsers(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json(['users' => []]);
            }

            $users = User::where('id', '!=', $request->get('exclude_id', 0)) // Exclude current user
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('id', 'like', "%{$query}%");
                })
                ->select('id', 'name', 'email')
                ->limit(10)
                ->get();

            return response()->json(['users' => $users]);

        } catch (\Exception $e) {
            return response()->json(['users' => []]);
        }
    }
}
