<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SelectService;
use App\Models\SelectStaffDetail;

class SelectServiceController extends Controller
{
    //

    public function getAvailableJobs()
{
    $userType = Auth::user()->userType;

    $services = SelectService::with('address')
        ->leftJoin('SelectStaffDetails as ss', 'SelectServices.select_service_id', '=', 'ss.select_service_id')
        ->select(
            'SelectServices.*',
            DB::raw('COUNT(ss.select_staff_detail_id) as staff_count'),
            DB::raw('CEIL(SelectServices.customer_count / 3) as required_staff')
        )
        ->where('SelectServices.service_type', $userType)
        ->groupBy('SelectServices.select_service_id', 'SelectServices.customer_count')
        ->havingRaw('staff_count < required_staff')
        ->whereNull('ss.select_staff_detail_id') // กรองงานที่ยังไม่มีช่างรับ
        ->get();

    // คำนวณการแบ่งลูกค้าให้พนักงาน
    foreach ($services as $service) {
        $service->work_distribution = $this->calculateWorkDistribution($service->customer_count);
    }

    return view('work.work_list', compact('services'));
}

// ฟังก์ชันแบ่งงานให้พนักงาน
private function calculateWorkDistribution($customer_count)
{
    $max_per_worker = 3;
    $min_per_worker = ($customer_count > 1) ? 2 : 1;

    $workers_needed = ceil($customer_count / $max_per_worker);
    $distribution = array_fill(0, $workers_needed, $min_per_worker);

    $remaining = $customer_count - array_sum($distribution);
    for ($i = 0; $i < $remaining; $i++) {
        $distribution[$i]++;
    }

    return $distribution;
}




    public function acceptJob(Request $request)
    {
        // Validate ค่าที่รับเข้ามา
        $request->validate([
            'select_service_id' => 'required|exists:SelectServices,select_service_id',
        ]);

        // ดึงข้อมูล service
        $service = SelectService::findOrFail($request->input('select_service_id'));

        // ใช้อัลกอริธึมแบ่งจำนวนลูกค้า
        $work_distribution = $this->calculateWorkDistribution($service->customer_count);

        // ดึงจำนวนพนักงานที่รับงานไปแล้ว
        $currentStaffCount = SelectStaffDetail::where('select_service_id', $service->select_service_id)->count();

        // ตรวจสอบว่ายังมีที่ให้รับงานอยู่หรือไม่
        if ($currentStaffCount >= count($work_distribution)) {
            return response()->json([
                'success' => false,
                'message' => 'งานนี้ถูกจองเต็มแล้ว'
            ]);
        }

        // กำหนดจำนวนลูกค้าที่พนักงานนี้ต้องทำ
        $customer_assigned = $work_distribution[$currentStaffCount];
        $earning = $customer_assigned * 2000;

        // บันทึกลงฐานข้อมูล
        $staffDetail = new SelectStaffDetail();
        $staffDetail->select_service_id = $service->select_service_id;
        $staffDetail->staff_id = Auth::user()->user_id;
        $staffDetail->customer_count = $customer_assigned;
        $staffDetail->earning = $earning;
        $staffDetail->created_at = now();
        $staffDetail->save();

        // เตรียมข้อมูล Response
        $response = [
            'success' => true,
            'message' => 'รับงานสำเร็จ!',
            'staff_count' => $currentStaffCount + 1,
            'required_staff' => count($work_distribution),
            'customer_assigned' => $customer_assigned,
            'earning' => $earning
        ];

        return response()->json($response);
    }

}
