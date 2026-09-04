@extends('layouts.main')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">
    <h2 class="text-2xl font-semibold mb-4">ชำระเงิน Booking #{{ $booking->booking_id }} (รอบ {{ $cycle }})</h2>

    <p class="mb-4 text-gray-700">ยอดชำระรวม: <strong class="text-green-600">{{ number_format($total, 2) }} ฿</strong></p>

    <form action="{{ route('payment.process') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
        <input type="hidden" name="booking_cycle" value="{{ $cycle }}">

        <label class="block">
            <span class="text-gray-700">วิธีการชำระเงิน</span>
            <select name="payment_method" class="w-full mt-1 border rounded p-2" required>
                <option value="">-- กรุณาเลือก --</option>
                <option value="credit card">บัตรเครดิต</option>
                <option value="bank transfer">โอนเงิน</option>
                <option value="cash">เงินสด</option>
            </select>
        </label>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
            ยืนยันการชำระเงิน
        </button>
    </form>
</div>
@endsection
