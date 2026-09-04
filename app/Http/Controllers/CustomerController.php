<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\SelectOutfitDetail;
use App\Models\ThaiOutfit;
use App\Models\CartItem;
use App\Models\OrderDetail;
use App\Models\Payment;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // เพิ่มเมธอดใหม่เข้าไปใน class ที่มีอยู่
    public function bookings()
    {
        $user = Auth::user();
        
        // ดึงรายการจองทั้งหมดของลูกค้า
        $bookings = Booking::with(['shop', 'orderDetails.cartItem.outfit'])
            ->where('user_id', $user->user_id)
            ->orderBy('purchase_date', 'desc')
            ->get();
        
        return view('customer.bookings.index', compact('bookings'));
    }

    public function showBooking($id)
    {
        $user = Auth::user();
        
        // ดึงข้อมูลการจองและรายละเอียดที่เกี่ยวข้อง
        $booking = Booking::with([
                'shop', 
                'orderDetails.cartItem.outfit',
                'orderDetails.cartItem.size',
                'orderDetails.cartItem.color',
                'promotion',
                'address'
            ])
            ->where('user_id', $user->user_id)
            ->where('booking_id', $id)
            ->firstOrFail();
        
        // ตรวจสอบว่ามีข้อเสนอชุดทดแทนหรือไม่
        $outfitSuggestions = SelectOutfitDetail::with(['outfit'])
            ->where('booking_id', $id)
            ->where('customer_id', $user->user_id)
            ->where('status', SelectOutfitDetail::STATUS_PENDING)
            ->get();
        
        return view('customer.bookings.show', compact('booking', 'outfitSuggestions'));
    }
    
    /**
     * แสดงรายการข้อเสนอชุดทดแทนทั้งหมดสำหรับการจองนี้
     */
    public function outfitSuggestions($bookingId)
    {
        $user = Auth::user();
        
        // ตรวจสอบว่าการจองนี้เป็นของผู้ใช้นี้จริงหรือไม่
        $booking = Booking::where('booking_id', $bookingId)
                     ->where('user_id', $user->user_id)
                     ->firstOrFail();
        
        // ชุดที่มีจำนวนไม่เพียงพอ (booking_cycle = 2)
        $originalOutfits = OrderDetail::with(['cartItem.outfit', 'cartItem.size', 'cartItem.color'])
                               ->where('booking_id', $bookingId)
                               ->where('booking_cycle', 2)
                               ->get();
        
        // ข้อเสนอชุดทดแทนที่รอการตอบรับ
        $suggestions = SelectOutfitDetail::with(['outfit', 'size', 'color'])
                                    ->where('booking_id', $bookingId)
                                    ->where('customer_id', $user->user_id)
                                    ->where('status', 'Pending Selection')
                                    ->get();
        
        return view('customer.outfit-suggestions', compact('booking', 'originalOutfits', 'suggestions'));
    }
    
    /**
     * ยืนยันหรือปฏิเสธข้อเสนอชุดทดแทน
     */
    public function confirmSelection(Request $request)
    {
        $request->validate([
            'selection_id' => 'required|exists:SelectOutfitsDetails,select_outfit_id',
            'action' => 'required|in:accept,reject',
        ]);
        
        $user = Auth::user();
        $selection = SelectOutfitDetail::where('select_outfit_id', $request->selection_id)
                                  ->where('customer_id', $user->user_id)
                                  ->firstOrFail();
        
        if ($request->action === 'accept') {
            // ยอมรับข้อเสนอ
            $selection->status = 'Selected';
            $selection->save();
            
            // อัพเดท OrderDetail หรือ CartItem ที่เกี่ยวข้อง (ทำตามความเหมาะสม)
            // อาจต้องเพิ่มโค้ดที่นี่...
            
            return redirect()->route('customer.bookings.show', $selection->booking_id)
                         ->with('success', 'คุณได้ยอมรับข้อเสนอชุดทดแทนเรียบร้อยแล้ว');
        } else {
            // ปฏิเสธข้อเสนอ
            $selection->status = 'Rejected';
            $selection->save();
            
            return redirect()->route('customer.bookings.show', $selection->booking_id)
                         ->with('info', 'คุณได้ปฏิเสธข้อเสนอชุดทดแทน ร้านค้าอาจเสนอชุดอื่นเพิ่มเติม');
        }
    }
}
