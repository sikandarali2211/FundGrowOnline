<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // ✅ Only logged-in users can access
    }

    /**
     * Show the profile edit page
     */
    public function edit()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You need to log in first!');
        }

        return view('user.profile.index', compact('user'));
    }

    /**
     * Handle profile update / photo save
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // ✅ If Save (Profile Picture)
        if ($request->action === 'save') {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
            $user->save();

            return redirect()->back()->with('success', 'Profile picture saved successfully!');
        }

        // ✅ If Update (Other fields)
        if ($request->action === 'update') {
            $validated = $request->validate([
                'fullName' => 'required|string|max:255',
                'phone'    => 'nullable|string|max:15',
                'country'  => 'nullable|string|max:255',
            ]);

            $user->full_name = $validated['fullName'];
            $user->phone     = $validated['phone'];
            $user->country   = $validated['country'];
            $user->save();

            return redirect()->back()->with('success', 'Profile updated successfully!');
        }

        return redirect()->back();
    }
}
