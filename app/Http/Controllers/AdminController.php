<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
