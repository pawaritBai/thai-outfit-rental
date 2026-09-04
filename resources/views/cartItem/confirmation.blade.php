@extends('layouts.main')

@section('title', 'Booking Confirmation')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md mt-8">
    <h2 class="text-2xl font-bold mb-4 text-green-600">🧾 สลิปการสั่งซื้อ</h2>

    <div class="mb-6">
        <p><strong>หมายเลขการจอง:</strong> {{ $booking->booking_id }}</p>
        <p><strong>วันที่สั่งซื้อ:</strong> {{ $booking->purchase_date->format('d/m/Y H:i') }}</p>
        <p><strong>สถานะ:</strong> 
            <span class="text-yellow-600 font-semibold">
                {{ ucfirst($booking->status) }}
            </span>
        </p>
        <p><strong>ร้าน:</strong> {{ $booking->shop->name ?? 'ไม่พบชื่อร้าน' }}</p>
        @if($booking->promotion)
            <p><strong>โปรโมชั่น:</strong> {{ $booking->promotion->promotion_code }} (-{{ number_format($booking->promotion->discount_amount) }}฿)</p>
        @endif
    </div>

    <h3 class="text-xl font-semibold mb-2">รายละเอียดสินค้า</h3>
    <div class="divide-y">
        @foreach($booking->orderDetails as $detail)
        <div class="py-4 flex justify-between items-center">
            <div>
                <p class="font-medium">{{ $detail->cartItem->outfit->name ?? 'ไม่พบชื่อชุด' }}</p>
                <p class="text-sm text-gray-500">จำนวน: {{ $detail->quantity }} | ขนาด: {{ $detail->cartItem->size->size ?? '-' }} | สี: {{ $detail->cartItem->color->color ?? '-' }}</p>
                <p class="text-sm text-gray-500">ระยะเวลาเช่า: {{ $detail->booking_cycle }} วัน</p>
                <p class="text-sm text-gray-500">จัดส่งแบบ: {{ $detail->deliveryOptions == 'delivery' ? 'จัดส่งถึงบ้าน' : 'รับเองที่ร้าน' }}</p>
            </div>
            <div class="text-right">
                <p class="font-semibold">{{ number_format($detail->total, 0) }}฿</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 border-t pt-4 text-right">
        <p class="text-lg font-bold">ราคารวม: {{ number_format($booking->total_price, 0) }}฿</p>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('user.orders') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            ดูรายการคำสั่งซื้อทั้งหมด
        </a>
    </div>
</div>
@endsection
