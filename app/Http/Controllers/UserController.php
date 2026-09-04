<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File; // นำเข้า File Helper
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{

    // ฟังก์ชันแสดงรายการผู้ใช้
    public function index(Request $request)
    {
        $search = $request->input('search'); // ค่าที่ผู้ใช้พิมพ์ค้นหา
        $userTypes = $request->input('userType', []); // ตัวกรอง userType
        $orderBy = in_array($request->input('orderBy'), ['user_id', 'name', 'email', 'phone', 'username', 'userType', 'status'])
            ? $request->input('orderBy')
            : 'user_id';

        $direction = in_array(strtolower($request->input('direction')), ['asc', 'desc'])
            ? strtolower($request->input('direction'))
            : 'asc';

        // Query
        $query = User::query();

        // 🔍 ค้นหาจาก name, email, username, phone
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('user_id', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // 🎯 กรองตาม userType
        if (!empty($userTypes) && is_array($userTypes)) {
            $query->whereIn('userType', $userTypes);
        }

        if ($request->has('status') && is_array($request->status)) {
            $query->whereIn('status', $request->status);
        }

        // 🔄 จัดเรียงข้อมูล
        $query->orderBy($orderBy, $direction);

        // 🟢 ดึงข้อมูลจาก Database
        $users = $query->paginate(10); // ใช้ pagination เพื่อความเร็ว

        // ✅ ส่งค่ากลับไปที่ View
        return view('admin.users.index', compact('users', 'search', 'orderBy', 'direction', 'userTypes'));
    }



    // ฟังก์ชันแสดงฟอร์มแก้ไขผู้ใช้
    public function edit($user_id)
    {
        $user = User::findOrFail($user_id);

        return view('admin.users.edit', compact('user'));
    }



    // ฟังก์ชันอัปเดตข้อมูลผู้ใช้
    public function update(Request $request, $user_id)
    {
        // ตรวจสอบว่าผู้ใช้ที่จะอัปเดตเป็น admin ที่กำลังใช้งานอยู่หรือไม่
        if (Auth::user()->user_id == $user_id) {
            // ถ้าพยายามเปลี่ยนสถานะเป็น inactive
            if ($request->status == 'inactive') {
                return back()->with('error', 'ไม่สามารถปิดการใช้งานบัญชีของตัวเองได้');
            }
            
            // ถ้าพยายามเปลี่ยน userType
            if ($request->userType != Auth::user()->userType) {
                return back()->with('error', 'ไม่สามารถเปลี่ยนบทบาทของตัวเองได้');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'username' => 'required|string|max:255|unique:Users,username,' . $user_id . ',user_id',
            'userType' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        // ค้นหาผู้ใช้และอัปเดตข้อมูล
        $user = User::find($user_id);
        $user->update($request->all());

        $referer = request()->headers->get('referer');
        $query = parse_url($referer, PHP_URL_QUERY);
        parse_str($query ?? '', $params);

        return redirect()->route('admin.users.index', $params)
                    ->with('success', 'User updated successfully');
    }




    // ฟังก์ชันเปลี่ยนสถานะผู้ใช้
    public function toggleStatus(Request $request, $id)
    {
        try {
            // ตรวจสอบว่าผู้ใช้ที่จะ toggle เป็น admin ที่กำลังใช้งานอยู่หรือไม่
            if (Auth::user()->userType == 'admin' && Auth::user()->user_id == $id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ไม่สามารถ Deactivated ตัวเองได้'
                    ], 403);
                }
                return back()->with('error', 'ไม่สามารถ Deactivated ตัวเองได้');
            }

            $user = User::findOrFail($id);
            $newStatus = $request->input('status');

            if (!in_array($newStatus, ['active', 'inactive'])) {
                throw new \Exception('Invalid status value');
            }

            $user->status = $newStatus;
            $user->save();

            // Prepare redirect parameters
            $params = $request->only(['orderBy', 'direction', 'search']);
            
            // Handle userType filter
            if ($request->has('userType')) {
                $params['userType'] = $request->userType;
            }
            
            // Handle status filter - if we're activating a user, we might need to adjust the filter
            if ($request->has('status')) {
                $params['status'] = $request->status;
            }

            // If it's an AJAX request, return JSON response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User status updated successfully',
                    'user' => [
                        'id' => $user->user_id,
                        'status' => $user->status
                    ]
                ]);
            }

            // For non-AJAX requests, redirect back with parameters
            return redirect()->route('admin.users.index', [
                'orderBy' => $request->input('orderBy'),
                'direction' => $request->input('direction'),
                'userType' => $request->input('userType'),
                'just_toggled' => 1,
                'toggled_user_id' => $id
            ])->with('success', 'User status updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user status: ' . $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => 'Failed to update user status: ' . $e->getMessage()]);
        }
    }

    // accept form
    public function acceptance()
    {
        // ดึงข้อมูลผู้ใช้ทั้งหมด
        $users = User::where(function ($query) {
            $query->where('status', 'inactive')->where('userType','!=','customer')
                ->where('is_newUser', true);
        })
            ->get();


        // ส่งข้อมูลไปยัง view
        return view('admin.users.acceptance', compact('users'));
    }

    // updateStatus
    public function updateStatus(Request $request, $user_id, $status)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();
        if ($user->identity_path && File::exists(public_path($user->identity_path))) {
            File::delete(public_path($user->identity_path));
        }
        $user->identity_path = null;
        $user->status = $status;
        $user->is_newUser = false;
        $user->save();

        return response()->json(['success' => true]);
    }
}
