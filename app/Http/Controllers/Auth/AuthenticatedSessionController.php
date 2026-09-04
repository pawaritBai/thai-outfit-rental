<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {

        $credentials = $request->only('username', 'password');

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            if (Auth::user()->userType === 'customer') {
                return redirect('/outfit/all'); // ถ้าเป็นลูกค้าไปที่ Outfit
            } elseif (Auth::user()->userType === 'admin') {
                return redirect()->route(str_replace(' ', '', Auth::user()->userType) . '.dashboard'); // ถ้าเป็นแอดมินไปที่ Dashboard
            } else {
                return redirect()->route(str_replace(' ', '', Auth::user()->userType) . '.dashboard'); // ค่าเริ่มต้น ถ้าไม่มี role หรือเงื่อนไขอื่น
            }
            
        }

        return back()->withErrors([
            'username' => 'Sorry, your password was incorrect. Please double-check your password.',
        ]);

        // $request->authenticate();

        // $request->session()->regenerate();

        // return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
