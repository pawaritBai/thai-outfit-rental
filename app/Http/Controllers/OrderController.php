<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log; // เพิ่มบนหัวไฟล์
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\CartItem;
use App\Models\ThaiOutfit;
use App\Models\Promotion;
use App\Models\Booking;
use App\Models\Shop;
use App\Models\Payment;
use App\Models\SelectService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\CustomerAddress; 



class OrderController extends Controller
{
    public function viewAddTo(Request $request)
    {
        // รับค่าจากฟอร์ม และตรวจสอบว่าเป็น Array จริงๆ
        $cartItemIds = json_decode($request->input('cart_item_ids'), true);
    
        if (!is_array($cartItemIds) || empty($cartItemIds)) {
            return redirect()->route('cartItem.allItem')->with('error', 'กรุณาเลือกสินค้าที่ต้องการสั่งซื้อ');
        }
    
        // ดึงข้อมูลสินค้าจากตะกร้าที่ถูกเลือก
        $cartItems = CartItem::with(['outfit.shop', 'outfit.sizeAndColors.size', 'outfit.sizeAndColors.color'])
                         ->whereIn('cart_item_id', $cartItemIds)
                         ->orderBy('outfit_id')
                         ->get();
    
        // ดึง outfit_id ทั้งหมดจาก cartItems
        $outfitIds = $cartItems->pluck('outfit_id')->unique();
    
        // ดึงข้อมูลชุดที่เกี่ยวข้อง
        $outfits = ThaiOutfit::with(['categories', 'sizeAndColors.size', 'sizeAndColors.color'])
                         ->whereIn('outfit_id', $outfitIds)
                         ->get();

        $customerAddress = CustomerAddress::with(['address'])
                         ->where('customer_id', Auth::id())
                         ->get();
        dd($customerAddress);
        // ดึงโปรโมชั่นที่กำลังใช้งานได้
        $shop = null;
        $activePromotion = null;
        
        // หากสินค้าทั้งหมดมาจากร้านค้าเดียวกัน
        if ($outfits->isNotEmpty()) {
            $shop_id = $outfits->first()->shop_id ?? null;
            
            if ($shop_id) {
                $shop = Shop::find($shop_id);
                
                if ($shop) {
                    $activePromotion = Promotion::where('shop_id', $shop_id)
                                               ->where('is_active', true)
                                               ->where('start_date', '<=', now())
                                               ->where('end_date', '>=', now())
                                               ->first();
                }
            }
        }
    
        return view('order.viewAddTo', compact('cartItems', 'outfits', 'activePromotion', 'shop'));
    }



    public function store(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();
    
        try {
            Log::debug('เริ่ม store()');
    
            // ✅ ตรวจสอบประเภทที่อยู่ (ไม่เปลี่ยนแปลง)
            $addressType = $request->input('address_type');
            $staffAddressId = null;
            $customerAddressId = null;
    
            if ($addressType === 'custom') {
                $staffAddressData = $request->input('staff_address');
                $staffAddress = new Address();
                $staffAddress->Province = $staffAddressData['province'] ?? '';
                $staffAddress->District = $staffAddressData['district'] ?? '';
                $staffAddress->Subdistrict = $staffAddressData['subdistrict'] ?? '';
                $staffAddress->PostalCode = $staffAddressData['postal_code'] ?? '';
                $staffAddress->HouseNumber = $staffAddressData['detail'] ?? '';
                $staffAddress->Street = $staffAddressData['street'] ?? '';
                $staffAddress->CreatedAt = now();
                $staffAddress->save();
    
                $staffAddressId = $staffAddress->AddressID;
    
                $customerAddress = new CustomerAddress();
                $customerAddress->customer_id = $user->user_id;
                $customerAddress->AddressID = $staffAddressId;
                $customerAddress->AddressName = 'บริการเสริม';
                $customerAddress->save();
    
                $customerAddressId = $customerAddress->cus_address_id;
            } else {
                $cus_address_id = $request->input('cus_address_id');
                $customerAddress = CustomerAddress::with('address')->findOrFail($cus_address_id);
                $customerAddressId = $customerAddress->cus_address_id;
                $staffAddressId = $customerAddress->address->AddressID;
            }
    
            // ✅ ดึง cart items พร้อม overent (ไม่เปลี่ยนแปลง)
            $cartItemIds = $request->cart_item_ids;
            $cartItems = CartItem::with('outfit')
                ->select('*')
                ->whereIn('cart_item_id', $cartItemIds)
                ->get();
    
            // ✅ จัดกลุ่ม CartItem ตาม shop_id และ reservation_date (ไม่เปลี่ยนแปลง)
            $groupedItems = $cartItems->groupBy(function ($item) {
                return $item->outfit->shop_id . '|' . $item->reservation_date;
            });
    
            // ✅ วนลูปสร้าง Booking สำหรับแต่ละกลุ่ม
            $bookings = [];
            foreach ($groupedItems as $groupKey => $groupItems) {
                [$shop_id, $reservation_date] = explode('|', $groupKey);
    
                $normalItems = $groupItems->where('overent', 0);
                $overentItems = $groupItems->where('overent', 1);
                $hasOverrented = $overentItems->isNotEmpty();
    
                // ✅ บริการเสริมสำหรับกลุ่มนี้
                $services = $request->input("selected_services.{$shop_id}.{$reservation_date}", []);
                $staffTotal = 0;
                if (!empty($services)) {
                    $staffTotal = (
                        (int)($services['photographer']['count'] ?? 0) +
                        (int)($services['makeup']['count'] ?? 0)
                    ) * 2000; // จำนวนคน * 2000
                }
    
                // ✅ โปรโมชั่น (ไม่เปลี่ยนแปลง)
                $promotionCode = $request->input('promotion_code');
                $promotion = null;
                $discountAmount = 0;
    
                if ($promotionCode) {
                    $promotion = Promotion::where('promotion_code', $promotionCode)
                        ->where('shop_id', $shop_id)
                        ->where('is_active', true)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->first();
    
                    if ($promotion) {
                        $discountAmount = $promotion->discount_amount;
                    }
                }
    
                // ✅ ยอดรวมสินค้าสำหรับกลุ่มนี้ (เฉพาะรอบ 1 สำหรับ Payment)
                $productTotalNormal = $normalItems->sum(fn($item) => $item->quantity * $item->outfit->price);
                $productTotalOverent = $overentItems->sum(fn($item) => $item->quantity * $item->outfit->price);
                $totalWithDiscount = max($productTotalNormal + $staffTotal - $discountAmount, 0);
    
                // ✅ สถานะ (ไม่เปลี่ยนแปลง)
                $bookingStatus = $overentItems->isNotEmpty() ? 'partial paid' : 'confirmed';
    
                // ✅ สร้าง Booking
                $booking = Booking::create([
                    'purchase_date' => now(),
                    'total_price' => $totalWithDiscount,
                    'status' => $bookingStatus,
                    'shop_id' => $shop_id,
                    'user_id' => $user->user_id,
                    'promotion_id' => $promotion?->promotion_id,
                    'AddressID' => $customerAddressId,
                    'hasOverrented' => $hasOverrented,
                ]);
    
                $bookings[$groupKey] = $booking;
    
                // ✅ บันทึก OrderDetail
                foreach ($groupItems as $item) {
                    $cycle = $item->overent == 1 ? 2 : 1;
    
                    $reservationDate = $item->reservation_date;
                    if ($item->overent == 1) {
                        $matchingItem = $cartItems->first(function ($cartItem) use ($item) {
                            return $cartItem->outfit_id == $item->outfit_id &&
                                   $cartItem->size_id == $item->size_id &&
                                   $cartItem->color_id == $item->color_id &&
                                   $cartItem->overent == 0;
                        });
    
                        if ($matchingItem) {
                            $reservationDate = $matchingItem->reservation_date;
                        }
                    }
    
                    if (!$reservationDate) {
                        throw new \Exception("ไม่พบวันที่จองสำหรับ CartItem ID: {$item->cart_item_id}");
                    }
    
                    $orderDetailData = [
                        'quantity' => $item->quantity,
                        'booking_cycle' => $cycle,
                        'booking_id' => $booking->booking_id,
                        'cart_item_id' => $item->cart_item_id,
                        'reservation_date' => $reservationDate,
                        'deliveryOptions' => 'default',
                        'total' => $item->overent == 1 ? null : $item->quantity * $item->outfit->price, // ถ้า overent == 1 ให้ total = 0
                    ];
    
                    OrderDetail::create($orderDetailData);
    
                    $item->status = 'REMOVED';
                    $item->purchased_at = now();
                    $item->save();
                }
    
                // ✅ สร้าง Payment เฉพาะสำหรับรอบ 1 (overent == 0) เท่านั้น
                if ($normalItems->isNotEmpty()) {
                    $paymentTotal = $productTotalNormal + $staffTotal - $discountAmount;
                    if ($paymentTotal > 0) {
                        Payment::create([
                            'payment_method' => 'paypal',
                            'total' => $paymentTotal,
                            'status' => 'unpaid',
                            'booking_cycle' => '1',
                            'booking_id' => $booking->booking_id,
                        ]);
                    }
                }
    
                // ✅ บันทึกบริการเสริม (ไม่เปลี่ยนแปลง)
                if (!empty($services)) {
                    if (isset($services['photographer']) && $services['photographer']['count'] > 0) {
                        $datetimeString = $reservation_date . ' ' . $services['photographer']['time'] . ':00';
                        $datetime = new \DateTime($datetimeString);
                        SelectService::create([
                            'service_type' => 'photographer',
                            'customer_count' => $services['photographer']['count'],
                            'reservation_date' => $datetime,
                            'booking_id' => $booking->booking_id,
                            'AddressID' => $staffAddressId,
                        ]);
                    }
                    if (isset($services['makeup']) && $services['makeup']['count'] > 0) {
                        $datetimeString = $reservation_date . ' ' . $services['makeup']['time'] . ':00';
                        $datetime = new \DateTime($datetimeString);
                        SelectService::create([
                            'service_type' => 'make-up artist',
                            'customer_count' => $services['makeup']['count'],
                            'reservation_date' => $datetime,
                            'booking_id' => $booking->booking_id,
                            'AddressID' => $staffAddressId,
                        ]);
                    }
                }
            }
    
            DB::commit();
            return redirect()->route('payment.viewUpdate', $booking->booking_id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('เกิดข้อผิดพลาด:', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    



    
    
    
    


    

}