<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display the role management page
     */
    public function index()
    {
        try {
            // Get all users with their roles
            $users = User::select('id', 'name', 'email', 'role', 'role_updated_at', 'email_verified_at')
                        ->orderBy('created_at', 'desc')
                        ->get();

            // Get users who have assigned roles
            $usersWithRoles = User::whereNotNull('role')
                                ->select('id', 'name', 'email', 'role', 'role_updated_at', 'email_verified_at')
                                ->orderBy('role_updated_at', 'desc')
                                ->get();

            return view('admin.role.index', compact('users', 'usersWithRoles'));
        } catch (\Exception $e) {
            Log::error('Error loading role management page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load role management page');
        }
    }

    /**
     * Assign a role to a user
     */
    public function assignRole(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|in:admin,manager,moderator,user'
            ]);

            $user = User::findOrFail($request->user_id);
            $oldRole = $user->role;
            
            // Update user role and utype
            $user->role = $request->role;
            $user->role_updated_at = now();
            
            // Set utype based on role
            if (in_array($request->role, ['admin', 'manager', 'moderator'])) {
                $user->utype = 'ADM';
            } else {
                $user->utype = 'USR';
            }
            
            $user->save();

            // Log the role assignment
            Log::info("Role assigned to user {$user->name} ({$user->email})", [
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $request->role,
                'assigned_at' => now()
            ]);

            return redirect()->back()->with('success', 
                "Role '{$request->role}' has been successfully assigned to {$user->name}"
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            Log::error('Error assigning role: ' . $e->getMessage(), [
                'user_id' => $request->user_id ?? 'unknown',
                'role' => $request->role ?? 'unknown',
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Failed to assign role. Please try again.');
        }
    }

    /**
     * Update a user's role
     */
    public function updateRole(Request $request, $userId)
    {
        try {
            $request->validate([
                'role' => 'required|in:admin,manager,moderator,user'
            ]);

            $user = User::findOrFail($userId);
            $oldRole = $user->role;
            
            // Update user role and utype
            $user->role = $request->role;
            $user->role_updated_at = now();
            
            // Set utype based on role
            if (in_array($request->role, ['admin', 'manager', 'moderator'])) {
                $user->utype = 'ADM';
            } else {
                $user->utype = 'USR';
            }
            
            $user->save();

            // Log the role update
            Log::info("Role updated for user {$user->name} ({$user->email})", [
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $request->role,
                'updated_at' => now()
            ]);

            return redirect()->back()->with('success', 
                "Role for {$user->name} has been successfully updated to '{$request->role}'"
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator);
        } catch (\Exception $e) {
            Log::error('Error updating role: ' . $e->getMessage(), [
                'user_id' => $userId,
                'role' => $request->role ?? 'unknown',
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Failed to update role. Please try again.');
        }
    }

    /**
     * Remove role from a user
     */
    public function removeRole($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $oldRole = $user->role;
            
            // Remove user role and set utype to USR
            $user->role = null;
            $user->role_updated_at = now();
            $user->utype = 'USR';
            $user->save();

            // Log the role removal
            Log::info("Role removed from user {$user->name} ({$user->email})", [
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'removed_role' => $oldRole,
                'removed_at' => now()
            ]);

            return redirect()->back()->with('success', 
                "Role has been successfully removed from {$user->name}"
            );

        } catch (\Exception $e) {
            Log::error('Error removing role: ' . $e->getMessage(), [
                'user_id' => $userId,
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Failed to remove role. Please try again.');
        }
    }

    /**
     * Get users by role (AJAX endpoint)
     */
    public function getUsersByRole(Request $request)
    {
        try {
            $role = $request->get('role');
            
            if (!$role) {
                return response()->json(['error' => 'Role parameter is required'], 400);
            }

            $users = User::where('role', $role)
                        ->select('id', 'name', 'email', 'role_updated_at', 'status')
                        ->orderBy('role_updated_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'users' => $users,
                'count' => $users->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting users by role: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch users'
            ], 500);
        }
    }

    /**
     * Get role statistics
     */
    public function getRoleStats()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'admin_count' => User::where('role', 'admin')->count(),
                'manager_count' => User::where('role', 'manager')->count(),
                'moderator_count' => User::where('role', 'moderator')->count(),
                'user_count' => User::where('role', 'user')->count(),
                'no_role_count' => User::whereNull('role')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting role statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch role statistics'
            ], 500);
        }
    }
}