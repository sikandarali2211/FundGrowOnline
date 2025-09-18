<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function edit()
    {
        $admin = Auth::user();
        return view('admin.setting.index', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::user();

        // ✅ Agar Save button dabaya (profile picture)
        if ($request->action === 'save') {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($admin->profile_picture) {
                Storage::disk('public')->delete($admin->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $admin->profile_picture = $path;
            $admin->save();

            return redirect()->back()->with('success', 'Profile picture updated successfully!');
        }

        // ✅ Agar Update button dabaya (other details)
        if ($request->action === 'update') {
            $validated = $request->validate([
                'fullName' => 'required|string|max:255',
                'phone'    => 'nullable|string|max:15',
                'country'  => 'nullable|string|max:255',
            ]);

            $admin->full_name = $validated['fullName'];
            $admin->phone     = $validated['phone'];
            $admin->country   = $validated['country'];
            $admin->save();

            return redirect()->back()->with('success', 'Profile updated successfully!');
        }

        return redirect()->back();
    }
}
