<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function redirectTo()
    {
        // ตรวจสอบว่าผู้ใช้ล็อกอินแล้ว
        if (Auth::check()) {
            if (Auth::user()->userType !== 'customer') {
                return '/outfit/all';  // ถ้าเป็นลูกค้า ไปที่หน้า Outfit
            } elseif (Auth::user()->userType !== 'admin') {
                return '/dashboard';  // ถ้าเป็นแอดมิน ไปที่ Dashboard
            }
        }
    
        return '/home'; // ค่าเริ่มต้น ถ้าไม่มี role หรือเงื่อนไขอื่น
    }
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
