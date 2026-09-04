<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SelectService;
use App\Models\SelectStaffDetail;
use Carbon\Carbon;

class StaffController extends Controller
{
    //
    public function schedule(Request $request)
    {
        $user_id = Auth::id();
        $status = $request->query('status', 'all'); // ค่า default เป็น 'all'
    
        $query = SelectStaffDetail::with(['selectService.address'])
            ->where('staff_id', $user_id);
    
        // กรองตามสถานะ
        if ($status === 'completed') {
            $query->whereNotNull('finished_time');
        } elseif ($status === 'pending') {
            $query->whereNull('finished_time');
        }
    
        $works = $query->get();
    
        // เรียงตาม reservation_date
        $works = $works->sortBy('selectService.reservation_date');
    
        return view('work.schedule', ['works' => $works]);
    }

    public function finishJob(Request $request)
    {
        $staff_detail = SelectStaffDetail::findOrFail(decrypt($request->id));

        if ($staff_detail->finished_time == null) {
            $staff_detail->finished_time = now();
            $staff_detail->service_info = $request->service_info;
            $staff_detail->save();
        }

        // ส่งข้อมูลกลับในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'message' => 'งานถูกทำเสร็จเรียบร้อย!',
            'staff_detail' => $staff_detail // ส่งข้อมูล staff_detail กลับ
        ]);
    }


    public function workDetails($id)
    {
        $decryptedId = decrypt($id); // ถอดรหัสค่า ID ที่เข้ารหัสมา
        $work = SelectStaffDetail::with(['selectService.address','selectService.booking.user'])->findOrFail($decryptedId);
        // dd($work);
        return view('work.work-details', compact('work'));
    }

    public function getAvailableJobs()
    {
        $userType = Auth::user()->userType;

        $services = SelectService::with(['address','booking.user'])
            ->leftJoin('SelectStaffDetails as ss', 'SelectServices.select_service_id', '=', 'ss.select_service_id')
            ->select(
                'SelectServices.*',
                DB::raw('COUNT(ss.select_staff_detail_id) as staff_count'),
                DB::raw('CEIL(SelectServices.customer_count / 3) as required_staff')
            )
            ->where('SelectServices.service_type', $userType)
            ->where('SelectServices.reservation_date', '>', now())
            ->groupBy('SelectServices.select_service_id', 'SelectServices.customer_count')
            ->havingRaw('staff_count < required_staff')
            ->whereNull('ss.select_staff_detail_id') // กรองงานที่ยังไม่มีช่างรับ
            ->get();
            // dd($services,now());


        // dd($services);

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

    public function earning(Request $request)
    {
        // กำหนดค่าเริ่มต้นเป็น Daily
        $period = $request->input('period', 'daily');
        // สร้างตัวแปรไว้เก็บค่า id ของ user
        $user_id = Auth::id();

        // กำหนดช่วงเวลาเริ่มต้นและสิ้นสุดสำหรับแต่ละช่วงเวลา
        $date = Carbon::now('Asia/Bangkok');

        if ($period == 'daily') {
            // Earning ของแต่ละชั่วโมงในวันนั้นๆ
            $earningsPerDay = DB::table('SelectStaffDetails')
                ->select(DB::raw('HOUR(finished_time) as hour'), DB::raw('SUM(earning) as earnings'))
                ->whereDate('finished_time', $date->toDateString())
                ->where('staff_id', $user_id)
                ->groupBy(DB::raw('HOUR(finished_time)'))
                ->get();

            for ($hour = 0; $hour <= 23; $hour++) {
                $earningsForHour = $earningsPerDay->where('hour', $hour)->first();
                if ($earningsForHour) {
                    $earningsPerPeriod[$hour] = $earningsForHour->earnings;
                } else {
                    $earningsPerPeriod[$hour] = 0; // หากไม่มีการทำงานในชั่วโมงนั้นๆ
                }
            }
        } elseif ($period == 'weekly') {
            $startOfWeek = (clone $date)->startOfWeek()->startOfDay(); // clone ก่อนแก้ไข
            $endOfWeek = (clone $date)->endOfWeek()->endOfDay();
            // Earning ของแต่ละวันในสัปดาห์นั้นๆ
            $earningsPerDay = DB::table('SelectStaffDetails')
                ->select(DB::raw('DAYOFWEEK(finished_time) as day'), DB::raw('SUM(earning) as earnings'))
                ->whereBetween('finished_time', [$startOfWeek, $endOfWeek])
                ->where('staff_id', $user_id)
                ->groupBy(DB::raw('DAYOFWEEK(finished_time)'))
                ->get();
                

            // เตรียมข้อมูลในแต่ละวันของสัปดาห์
            for ($day = 1; $day <= 7; $day++) {
                $earningsForDay = $earningsPerDay->where('day', $day)->first();
                if ($earningsForDay) {
                    $earningsPerPeriod[$day] = $earningsForDay->earnings;
                } else {
                    $earningsPerPeriod[$day] = 0; // หากไม่มีการทำงานในวันนั้นๆ
                }
            }
        } elseif ($period == 'monthly') {
            // Earning ของแต่ละวันในเดือนนั้นๆ
            $earningsPerDay = DB::table('SelectStaffDetails')
                ->select(DB::raw('DAY(finished_time) as day'), DB::raw('SUM(earning) as earnings'))
                ->where('staff_id', $user_id)
                ->whereMonth('finished_time', $date->month)
                ->whereYear('finished_time', $date->year)
                ->groupBy(DB::raw('DAY(finished_time)'))
                ->get();

            // สร้างอาร์เรย์ที่มีวันที่ทุกวันในเดือน
            $daysInMonth = range(1, $date->daysInMonth);

            // เตรียมข้อมูลที่ไม่มีการทำงาน
            foreach ($daysInMonth as $day) {
                // ค้นหาว่ามีข้อมูลรายได้สำหรับวันนั้นหรือไม่
                $earningsForDay = $earningsPerDay->where('day', $day)->first();
                if ($earningsForDay) {
                    $earningsPerPeriod[$day] = $earningsForDay->earnings;
                } else {
                    $earningsPerPeriod[$day] = 0; // หากไม่มีการทำงานให้เป็น 0
                }
            }
        }
        // dd($earningsPerPeriod);
        // dd($earningsPerDay);
        // dd($earningsPerDay->toSql());



        return view('work.earning', [
            'earningsPerHour' => $earningsPerPeriod,
            'period' => $period,
            'totalEarnings' => $earningsPerDay->sum('earnings'),
            'tasks' => $earningsPerDay->count(),
        ]);
    }
}
