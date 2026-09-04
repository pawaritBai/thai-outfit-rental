<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * แสดงรายการโปรโมชั่นทั้งหมด
     */
    public function index()
    {
        // ดึงร้านค้าของผู้ใช้ปัจจุบัน
        $shop = Shop::where('shop_owner_id', Auth::id())->where('status', 'active')->first();
        
        if (!$shop) {
            return redirect()->route('shopowner.shops.my-shop')
                ->with('error', 'คุณต้องมีร้านค้าที่ได้รับการอนุมัติก่อนจึงจะสามารถจัดการโปรโมชั่นได้');
        }
        
        // ดึงโปรโมชั่นของร้านค้า
        $promotions = Promotion::where('shop_id', $shop->shop_id)->orderBy('created_at', 'desc')->get();
        
        return view('shopowner.promotions.index', compact('promotions'));
    }

    /**
     * แสดงฟอร์มสำหรับสร้างโปรโมชั่นใหม่
     */
    public function create()
    {
        // ดึงร้านค้าของผู้ใช้ปัจจุบัน
        $shop = Shop::where('shop_owner_id', Auth::id())->where('status', 'active')->first();
        
        if (!$shop) {
            return redirect()->route('shopowner.shops.my-shop')
                ->with('error', 'คุณต้องมีร้านค้าที่ได้รับการอนุมัติก่อนจึงจะสามารถจัดการโปรโมชั่นได้');
        }
        
        return view('shopowner.promotions.create');
    }

    /**
     * จัดเก็บโปรโมชั่นใหม่ในฐานข้อมูล
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูลที่ส่งมา
        $request->validate([
            'promotion_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        // ดึงร้านค้าของผู้ใช้ปัจจุบัน
        $shop = Shop::where('shop_owner_id', Auth::id())->where('status', 'active')->first();
        
        if (!$shop) {
            return redirect()->route('shopowner.shops.my-shop')
                ->with('error', 'คุณต้องมีร้านค้าที่ได้รับการอนุมัติก่อน');
        }
        
        // สร้างรหัสโปรโมชั่น (ตัวอักษร 8 ตัว)
        $promotionCode = strtoupper(Str::random(8));
        
        // สร้างโปรโมชั่นใหม่
        Promotion::create([
            'promotion_name' => $request->promotion_name,
            'description' => $request->description,
            'promotion_code' => $promotionCode,
            'discount_amount' => $request->discount_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true, // เปิดใช้งานโดยค่าเริ่มต้น
            'shop_id' => $shop->shop_id,
            'created_at' => now(),
            // Remove updated_at if it's here
        ]);
        
        return redirect()->route('shopowner.promotions.index')
            ->with('success', 'สร้างโปรโมชั่นใหม่เรียบร้อยแล้ว');
    }

    /**
     * แสดงฟอร์มแก้ไขโปรโมชั่น
     */
    public function edit($id)
    {
        // ดึงร้านค้าของผู้ใช้ปัจจุบัน
        $shop = Shop::where('shop_owner_id', Auth::id())->where('status', 'active')->first();
        
        if (!$shop) {
            return redirect()->route('shopowner.shops.my-shop')
                ->with('error', 'คุณต้องมีร้านค้าที่ได้รับการอนุมัติก่อน');
        }
        
        // ดึงข้อมูลโปรโมชั่นและตรวจสอบว่าเป็นของร้านค้านี้หรือไม่
        $promotion = Promotion::where('promotion_id', $id)
                             ->where('shop_id', $shop->shop_id)
                             ->firstOrFail();
        
        return view('shopowner.promotions.edit', compact('promotion'));
    }

    /**
     * อัปเดตข้อมูลโปรโมชั่นในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        // ตรวจสอบข้อมูลที่ส่งมา
        $request->validate([
            'promotion_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        // ดึงร้านค้าของผู้ใช้ปัจจุบัน
        $shop = Shop::where('shop_owner_id', Auth::id())->where('status', 'active')->first();
        
        if (!$shop) {
            return redirect()->route('shopowner.shops.my-shop')
                ->with('error', 'คุณต้องมีร้านค้าที่ได้รับการอนุมัติก่อน');
        }
        
        // ดึงข้อมูลโปรโมชั่นและตรวจสอบว่าเป็นของร้านค้านี้หรือไม่
        $promotion = Promotion::where('promotion_id', $id)
                             ->where('shop_id', $shop->shop_id)
                             ->firstOrFail();
        
        // อัปเดตข้อมูลโปรโมชั่น
        $promotion->update([
            'promotion_name' => $request->promotion_name,
            'description' => $request->description,
            'discount_amount' => $request->discount_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('shopowner.promotions.index')
            ->with('success', 'อัปเดตโปรโมชั่นเรียบร้อยแล้ว');
    }

    /**
     * ลบโปรโมชั่นออกจากระบบ
     */
    public function destroy($id)
    {
        // ดึงร้านค้าของผู้ใช้ปัจจุบัน
        $shop = Shop::where('shop_owner_id', Auth::id())->where('status', 'active')->first();
        
        if (!$shop) {
            return redirect()->route('shopowner.shops.my-shop')
                ->with('error', 'คุณต้องมีร้านค้าที่ได้รับการอนุมัติก่อน');
        }
        
        // ดึงข้อมูลโปรโมชั่นและตรวจสอบว่าเป็นของร้านค้านี้หรือไม่
        $promotion = Promotion::where('promotion_id', $id)
                             ->where('shop_id', $shop->shop_id)
                             ->firstOrFail();
        
        // ลบโปรโมชั่น
        $promotion->delete();
        
        return redirect()->route('shopowner.promotions.index')
            ->with('success', 'ลบโปรโมชั่นเรียบร้อยแล้ว');
    }

    /**
     * ตรวจสอบโค้ดโปรโมชั่น (API)
     */
    public function checkPromoCode(Request $request)
    {
        $request->validate([
            'promotion_code' => 'required|string',
        ]);
        
        $code = $request->promotion_code;
        
        $promotion = Promotion::where('promotion_code', $code)
                             ->where('is_active', true)
                             ->where('start_date', '<=', now())
                             ->where('end_date', '>=', now())
                             ->first();
        
        if ($promotion) {
            return response()->json([
                'valid' => true,
                'promotion' => [
                    'name' => $promotion->promotion_name,
                    'discount_amount' => $promotion->discount_amount,
                ]
            ]);
        } else {
            return response()->json([
                'valid' => false,
                'message' => 'รหัสโปรโมชั่นไม่ถูกต้องหรือหมดอายุแล้ว'
            ]);
        }
    }

    public function checkPromotion(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'shop_id' => 'required|integer',
        ]);
    
        $promotion = Promotion::where('promotion_code', $request->code)
            ->where('shop_id', $request->shop_id)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    
        if ($promotion) {
            return response()->json([
                'valid' => true,
                'discount' => $promotion->discount_amount,
                'promotion_id' => $promotion->promotion_id,
                'message' => 'โค้ดใช้งานได้'
            ]);
        }
    
        return response()->json([
            'valid' => false,
            'discount' => 0,
            'message' => 'ไม่พบโค้ดหรือหมดอายุแล้ว'
        ]);
    }
    

}
