<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // ตรวจสอบว่า user ที่ล็อกอินเป็น admin หรือไม่
        if (Auth::check() && Auth::user()->status !== 'active') {
            // ถ้าไม่ใช่ admin, ให้ redirect ไปที่หน้า dashboard หรือหน้าอื่นที่กำหนด
            return redirect()->route('outfits.index');
        }

        return $next($request);
    }
}
