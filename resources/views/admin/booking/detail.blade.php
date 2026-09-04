@extends('layouts.admin-layout')

@section('title', 'Booking')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-8 shadow-md rounded-lg mt-10 border">
        <h2 class="text-2xl font-bold text-indigo-700 mb-6">📋 รายละเอียดการจอง #{{ $booking->booking_id }}</h2>

        <!-- ข้อมูลการจอง -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-800">
            <div><strong> วันที่จอง:</strong> {{ \Carbon\Carbon::parse($booking->purchase_date)->format('d/m/Y') }}</div>
            <div><strong> ราคารวม:</strong> {{ number_format($booking->total_price, 2) }} บาท</div>
            @if ($booking->SelectService)
                @foreach ($booking->SelectService as $service)
                @if ($service->service_type == 'photographer')
                <p><strong>📸 ต้องการถ่ายรูป:</strong> {{ $service->customer_count }} คน</p>
                @else
                <p><strong>💄 ต้องการแต่งหน้า:</strong> {{ $service->customer_count }} คน</p>
                @endif
                @endforeach
            @endif
            <div><strong> สถานะ:</strong>
                <span class="px-2 py-1 rounded-full text-sm
                @if($booking->status === 'confirmed') bg-green-200 text-green-800 
                @elseif($booking->status === 'pending') bg-yellow-200 text-yellow-800 
                @elseif($booking->status === 'cancelled') bg-red-200 text-red-800 
                @else bg-gray-200 text-gray-700 @endif">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
            <div><strong> วันที่รับของ:</strong>
                {{ $booking->pickup_date !== '0000-00-00' ? \Carbon\Carbon::parse($booking->pickup_date)->format('d/m/Y') : '-' }}
            </div>
            <div><strong> ร้าน:</strong>  
                @if ($shop)
                #{{ $shop->shop_id }} - {{ $shop->shop_name }}
                @else
                <span class="text-gray-500">ไม่มีข้อมูลร้าน</span>
                @endif
            </div>
            <div> <strong> โปรโมชั่น:</strong>
                @if ($promotion)
                #{{ $promotion->promotion_id }} - {{ $promotion->promotion_name }} {{ $promotion->description }}
                @else
                <span class="text-gray-500">ไม่มีโปรโมชัน</span>
                @endif
            </div>
            <div><strong> ลูกค้า:</strong>
                @if ($user)
                #{{ $user->user_id }} - {{ $user->name }}
                @else
                <span class="text-gray-500">ไม่มีข้อมูลลูกค้า</span>
                @endif
            </div>
            <div><strong> ที่อยู่:</strong>
            @if ($booking->address) 
                {{ $booking->address->HouseNumber }}
                {{ $booking->address->Street }} 
                {{ $booking->address->Subdistrict }} 
                {{ $booking->address->District }} 
                {{ $booking->address->Province }} 
                {{ $booking->address->PostalCode }}
            @endif
            </div>
            <div><strong> เวลาที่สร้าง:</strong> {{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</div>
        </div>

        <!-- เส้นแบ่ง -->
        <hr class="my-6">

        <!-- รายการ OrderDetails -->
        <h3 class="text-xl font-semibold text-gray-700 mb-4">📑 รายการที่จอง:</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded-lg">
                <thead>
                    <tr class="bg-indigo-100 text-left text-sm text-gray-700">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Cart Item ID</th>
                        <th class="py-3 px-4">จำนวน</th>
                        <th class="py-3 px-4">ราคารวม</th>
                        <th class="py-3 px-4">รอบการจอง</th>
                        <th class="py-3 px-4">ตัวเลือกส่ง</th>
                        <th class="py-3 px-4">สร้างเมื่อ</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-800">
                    @foreach ($orderdetails as $index => $detail)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $index + 1 }}</td>
                        <td class="py-2 px-4">{{ $detail->cart_item_id }}</td>
                        <td class="py-2 px-4">{{ $detail->quantity }}</td>
                        <td class="py-2 px-4">{{ number_format($detail->total, 2) }} บาท</td>
                        <td class="py-2 px-4">{{ $detail->booking_cycle }}</td>
                        <td class="py-2 px-4">{{ ucfirst($detail->deliveryOptions) }}</td>
                        <td class="py-2 px-4">{{ \Carbon\Carbon::parse($detail->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ปุ่มย้อนกลับ -->
        <div class="mt-8 text-right">
            <a href="{{ route('admin.booking.index') }}"
                class="inline-block bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition">
                ⬅ กลับหน้ารายการจอง
            </a>
        </div>
    </div>
@endsection