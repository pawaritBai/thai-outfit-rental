<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function showPaymentForm($booking_id, $cycle)
    {
        $booking = Booking::findOrFail($booking_id);

        $orderDetails = OrderDetail::where('booking_id', $booking_id)
            ->where('booking_cycle', $cycle)
            ->get();

        $total = $orderDetails->sum('total');

        return view('payment.form', compact('booking', 'cycle', 'orderDetails', 'total'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,booking_id',
            'booking_cycle' => 'required|in:1,2',
            'payment_method' => 'required|string',
        ]);

        $booking_id = $request->booking_id;
        $cycle = $request->booking_cycle;

        // รวมยอดจาก OrderDetail ตามรอบ
        $total = OrderDetail::where('booking_id', $booking_id)
            ->where('booking_cycle', $cycle)
            ->sum('total');

        // ตรวจสอบซ้ำว่าเคยจ่ายรอบนี้แล้วหรือยัง
        $existing = Payment::where('booking_id', $booking_id)
            ->where('booking_cycle', $cycle)
            ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'ชำระเงินรอบนี้ไปแล้ว']);
        }

        Payment::create([
            'payment_method' => $request->payment_method,
            'total' => $total,
            'status' => 'paid',
            'booking_cycle' => $cycle,
            'booking_id' => $booking_id,
        ]);

        return redirect()->route('cartItem.allItem')->with('success', 'ชำระเงินสำเร็จแล้ว');
    }

    public function index()
    {
        $bookings = Booking::with(['payments', 'orderDetails', 'selectService'])
            ->whereBelongsTo(auth()->user())
            //->where('status', 'confirmed') // ✅ เพิ่มเงื่อนไขตรงนี้
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($bookings as $booking) {
            $paid = $booking->payments->sum('total');
            $total = $booking->total_price;

            $booking->total_price = $total;
            $booking->paid = $paid;
            $booking->unpaid = $total - $paid;
            $booking->total_with_staff = $total;
        }

        return view('payment.index', compact('bookings'));
    }


    public function viewUpdate($booking_id)
{
    $booking = Booking::with(['payments'])->where('booking_id', $booking_id)->firstOrFail();

    // ✅ ต้องใช้ ->filter() หรือ ->where() บน Collection
    $payments = $booking->payments->where('status', 'unpaid');

    return view('payment.viewUpdate', compact('payments', 'booking'));
}

public function updateMethod(Request $request, $id)
{
    $request->validate([
        'payment_method' => 'required|in:credit_card,paypal',
    ]);

    $payment = Payment::findOrFail($id);

    $payment->payment_method = $request->payment_method;
    $payment->status = 'paid'; // หากต้องการให้ระบบจ่ายทันที
    $payment->save();

    return redirect()->route('payment.index')->with('success', 'บันทึกการชำระเงินเรียบร้อยแล้ว');
}

public function updateToCycle2($booking_id)
{
    try {
        $booking = Booking::with('orderDetails')->findOrFail($booking_id);

        if ($booking->status !== 'partial paid') {
            return back()->withErrors('Booking นี้ไม่ได้อยู่ในสถานะที่รอรอบ 2');
        }

        // ✅ คำนวณยอดรอบ 2 จาก OrderDetails
        $totalCycle2 = $booking->orderDetails
            ->where('booking_cycle', 2)
            ->sum('total');

        if ($totalCycle2 <= 0) {
            return back()->withErrors('ไม่พบยอดค้างในรอบ 2');
        }

        // ✅ เพิ่ม Payment รอบที่ 2
        Payment::create([
            'payment_method' => null, // ยังไม่เลือกวิธี
            'total' => $totalCycle2,
            'status' => 'unpaid',
            'booking_cycle' => 2,
            'booking_id' => $booking->booking_id,
        ]);

        // ✅ เปลี่ยนสถานะ booking เป็น confirmed
        $booking->status = 'confirmed';
        $booking->save();

        return redirect()->route('payment.viewCycle2',  $booking->booking_id)->with('success', 'เพิ่มรอบชำระเงินรอบ 2 แล้ว');
    } catch (\Exception $e) {
        return back()->withErrors('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

public function viewCycle2($booking_id)
{
    $booking = Booking::with('payments')->findOrFail($booking_id);

    // ดึง Payment รอบ 2 ที่ยังไม่จ่าย
    $payment = $booking->payments
        ->where('booking_cycle', 2)
        ->where('status', 'unpaid')
        ->first();

    return view('payment.viewCycle2', compact('booking', 'payment'));
}

}
