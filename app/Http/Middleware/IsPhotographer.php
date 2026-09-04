<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class IsPhotographer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    
    {
        // ตรวจสอบว่า user ที่ล็อกอินเป็น admin หรือไม่
        if (Auth::check() && Auth::user()->userType !== 'photographer') {
            // ถ้าไม่ใช่ admin, ให้ redirect ไปที่หน้า dashboard หรือหน้าอื่นที่กำหนด
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
