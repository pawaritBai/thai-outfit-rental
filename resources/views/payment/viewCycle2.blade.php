@extends('layouts.main')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold text-center mb-4">
        💸 รายการชำระเงินรอบที่ 2
    </h2>

    @if($payment)
        <div class="bg-gray-100 p-4 rounded mb-4">
            <p class="text-lg">ยอดที่ต้องชำระ:</p>
            <p class="text-green-600 text-2xl font-bold mb-2">
                ฿{{ number_format($payment->total, 2) }}
            </p>
            <p class="text-sm text-gray-500">รอบที่ {{ $payment->booking_cycle }}</p>
        </div>

        <form action="{{ route('payment.updateMethod', $payment->payment_id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 font-medium">เลือกวิธีชำระเงิน:</label>
                <select name="payment_method" class="w-full border border-gray-300 rounded p-2" required>
                    <option value="">-- กรุณาเลือก --</option>
                    <option value="paypal">PayPal</option>
                    <option value="credit_card">บัตรเครดิต</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition">
                ✅ ยืนยันการชำระเงิน
            </button>
        </form>
    @else
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
            🔍 ไม่พบข้อมูลการชำระเงินรอบที่ 2
        </div>
    @endif
</div>
@endsection
