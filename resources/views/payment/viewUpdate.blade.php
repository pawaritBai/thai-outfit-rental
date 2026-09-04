@extends('layouts.main')

@section('content')
    <div class="container mx-auto px-4 py-6">
        @if (session('success'))
            <h2 class="text-xl font-bold mb-4 text-green-600">
                {{ session('success') }}
            </h2>
        @endif

        <h3 class="text-lg mb-2">รายการชำระเงิน</h3>

        @forelse($payments as $payment)
            <div class="border p-4 rounded mb-4 shadow">
                <p>ยอดที่ต้องชำระ: <strong>{{ number_format($payment->total, 2) }}</strong> บาท</p>
                <p>รอบการชำระ: {{ $payment->booking_cycle }}</p>

                <form action="{{ route('payment.updateMethod', $payment->payment_id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('PUT')

                    <label class="block mb-1">เลือกวิธีชำระเงิน:</label>
                    <select name="payment_method" class="border p-2 rounded w-full mb-2" required>
                        <option value="">เลือกวิธีชำระเงิน</option>
                        <option value="credit_card">บัตรเครดิต</option>
                        <option value="paypal">PayPal</option>
                    </select>

                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        ยืนยันการชำระเงิน
                    </button>
                </form>
            </div>
        @empty
            <p class="text-gray-600">ไม่พบรายการที่ต้องชำระ</p>
        @endforelse
    </div>
@endsection
