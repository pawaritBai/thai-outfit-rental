<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SelectService;
use App\Models\SelectStaffDetail;

class SelectStaffDetailController extends Controller
{
    public function index()
{
    $user_id = Auth::id();
    $works = SelectStaffDetail::with('selectService.address')
        ->where('staff_id', $user_id)
        ->get();

    $works = $works->sortBy('selectService.reservation_date');
    // dd($works);
    return view('work.schedule', compact('works'));
}

public function finishJob(Request $request)
{
    $staff_detail = SelectStaffDetail::findOrFail(decrypt($request->id));

    if ($staff_detail->finished_time == null) {
        $staff_detail->finished_time = now();
        $staff_detail->save();
    }

    return redirect()->route('Auth::user->userType.dashboard')->with('success', 'งานถูกทำเสร็จเรียบร้อย!');
}


    public function show($id) {
        $decryptedId = decrypt($id); // ถอดรหัสค่า ID ที่เข้ารหัสมา
        $work = SelectStaffDetail::with('selectService.address')->findOrFail($decryptedId);
        return view('work.work-details', compact('work'));
    }
    
}
