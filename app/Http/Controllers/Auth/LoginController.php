<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // default fallback
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Role-based redirect after login
     */
    protected function authenticated($request, $user)
    {
        if ($user->role === 'admin' || $user->utype === 'ADM') {
            return redirect()->route('admin.index'); // Admin dashboard
        }

        return redirect()->route('user.index'); // User dashboard
    }
}
