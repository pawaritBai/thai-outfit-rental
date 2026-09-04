<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\SelectService;
use App\Models\SelectStaffDetail;

class AdminController extends Controller
{
        /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get counts for dashboard statistics
        $userCount = \App\Models\User::count();
        $shopCount = \App\Models\Shop::count();
        $outfitCount = \App\Models\ThaiOutfit::count();
        $bookingCount = \App\Models\Booking::count();
        $pendingIssuesCount = \App\Models\Issue::where('status', 'reported')->count();
        
        // Get recent users
        $recentUsers = \App\Models\User::orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();
        
        // Get recent bookings
        $recentBookings = \App\Models\Booking::with(['orderDetails.cartItem.user'])
                                            ->orderBy('created_at', 'desc')
                                            ->take(5)
                                            ->get();
        
        // Get pending shop approvals
        $pendingShops = \App\Models\Shop::where('status', 'inactive')
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
        
        return view('admin.dashboard', compact(
            'userCount',
            'shopCount',
            'outfitCount',
            'bookingCount',
            'pendingIssuesCount',
            'recentUsers',
            'recentBookings',
            'pendingShops'
        ));
    }

    //
    public function showShopStatistics(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        // Shop: คำนวณยอดขายจาก Bookings และ OrderDetails (สำหรับ TOP 10)
        $ShopstatsTop10 = Booking::join('OrderDetails', 'Bookings.booking_id', '=', 'OrderDetails.booking_id')
            ->join('Shops', 'Bookings.shop_id', '=', 'Shops.shop_id')
            ->where('Bookings.purchase_date', 'LIKE', "%$month%")
            ->selectRaw('Shops.shop_id, Shops.shop_name, SUM(OrderDetails.total) as total_sales')
            ->groupBy('Shops.shop_id')
            ->orderByDesc('total_sales')
            ->take(10)
            ->get();

        // Shop: ข้อมูลทั้งหมดสำหรับรายงาน
        $ShopstatsAll = Booking::join('OrderDetails', 'Bookings.booking_id', '=', 'OrderDetails.booking_id')
            ->join('Shops', 'Bookings.shop_id', '=', 'Shops.shop_id')
            ->where('Bookings.purchase_date', 'LIKE', "%$month%")
            ->selectRaw('Shops.shop_id, Shops.shop_name, SUM(OrderDetails.total) as total_sales')
            ->groupBy('Shops.shop_id')
            ->get();

        return view('admin.statistics.shop', [
            'ShopstatsTop10' => $ShopstatsTop10,
            'ShopstatsAll' => $ShopstatsAll,
            'month' => $month,
        ]);
    }

    public function showPhotographerStatistics(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        // Photographer: ข้อมูลทั้งหมดสำหรับ TOP 10
        $photographerStatsTop10 = SelectService::where('service_type', 'photographer')
        ->where('reservation_date', 'LIKE', "%$month%")
        ->join('SelectStaffDetails', 'SelectServices.select_service_id', '=', 'SelectStaffDetails.select_service_id')
        ->join('Users', 'SelectStaffDetails.staff_id', '=', 'Users.user_id') // Join กับ Users
        ->where('SelectStaffDetails.finished_time', '<=', now())
        ->selectRaw('SelectStaffDetails.staff_id, SUM(SelectStaffDetails.earning) as total_payment, Users.name') // เพิ่ม Users.name
        ->groupBy('SelectStaffDetails.staff_id', 'Users.name') // Group by ทั้ง staff_id และ name
        ->orderByDesc('total_payment')
        ->take(10)
        ->get();

        // Photographer: ข้อมูลทั้งหมด
        $photographerStatsAll = SelectService::where('service_type', 'photographer')
        ->where('reservation_date', 'LIKE', "%$month%")
        ->join('SelectStaffDetails', 'SelectServices.select_service_id', '=', 'SelectStaffDetails.select_service_id')
        ->join('Users', 'SelectStaffDetails.staff_id', '=', 'Users.user_id') // Join กับ Users
        ->where('SelectStaffDetails.finished_time', '<=', now())
        ->selectRaw('SelectStaffDetails.staff_id, SUM(SelectStaffDetails.earning) as total_payment, Users.name') // เพิ่ม Users.name
        ->groupBy('SelectStaffDetails.staff_id', 'Users.name') // Group by ทั้ง staff_id และ name
        ->get();

        return view('admin.statistics.photographer', [
            'photographerStatsTop10' => $photographerStatsTop10,
            'photographerStatsAll' => $photographerStatsAll,
            'month' => $month,
        ]);
    }

    public function showMakeupStatistics(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        // Make-up Artist: ข้อมูลทั้งหมดสำหรับ TOP 10
        $makeUpArtistStatsTop10 = SelectService::where('service_type', 'make-up artist')
        ->where('reservation_date', 'LIKE', "%$month%")
        ->join('SelectStaffDetails', 'SelectServices.select_service_id', '=', 'SelectStaffDetails.select_service_id')
        ->join('Users', 'SelectStaffDetails.staff_id', '=', 'Users.user_id') // Join กับ Users
        ->where('SelectStaffDetails.finished_time', '<=', now())
        ->selectRaw('SelectStaffDetails.staff_id, SUM(SelectStaffDetails.earning) as total_payment, Users.name') // เพิ่ม Users.name
        ->groupBy('SelectStaffDetails.staff_id', 'Users.name') // Group by ทั้ง staff_id และ name
        ->orderByDesc('total_payment')
        ->take(10)
        ->get();

        // Make-up Artist: ข้อมูลทั้งหมด
        $makeUpArtistStatsAll = SelectService::where('service_type', 'make-up artist')
        ->where('reservation_date', 'LIKE', "%$month%")
        ->join('SelectStaffDetails', 'SelectServices.select_service_id', '=', 'SelectStaffDetails.select_service_id')
        ->join('Users', 'SelectStaffDetails.staff_id', '=', 'Users.user_id') // Join กับ Users
        ->where('SelectStaffDetails.finished_time', '<=', now())
        ->selectRaw('SelectStaffDetails.staff_id, SUM(SelectStaffDetails.earning) as total_payment, Users.name') // เพิ่ม Users.name
        ->groupBy('SelectStaffDetails.staff_id', 'Users.name') // Group by ทั้ง staff_id และ name
        ->get();

        return view('admin.statistics.makeup', [
            'makeUpArtistStatsTop10' => $makeUpArtistStatsTop10,
            'makeUpArtistStatsAll' => $makeUpArtistStatsAll,
            'month' => $month,
        ]);
    }
}
