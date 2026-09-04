<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // ตรวจสอบว่า user ที่ล็อกอินเป็น admin หรือไม่
        if (Auth::check() && Auth::user()->userType !== 'admin') {
            // ถ้าไม่ใช่ admin, ให้ redirect ไปที่หน้า dashboard หรือหน้าอื่นที่กำหนด
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
