@extends('layouts.main')

@section('title', 'ชุดทดแทนที่ได้รับการแนะนำ')

@section('content')
<div class="container mx-auto my-5">
    <div class="mb-6">
        <a href="{{ route('profile.customer.orderDetail', ['bookingId' => $booking->booking_id]) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> กลับไปยังรายละเอียดการสั่งซื้อ
        </a>
        <h2 class="text-2xl font-bold mt-2">ชุดทดแทนที่ได้รับการแนะนำ</h2>
        <p class="text-gray-600">สำหรับการสั่งซื้อเลขที่ #{{ $booking->booking_id }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- ชุดเดิมที่มีจำนวนไม่เพียงพอ -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-lg font-semibold text-red-700">ชุดที่มีจำนวนไม่เพียงพอ</h3>
        </div>
        <div class="p-4">
            @foreach($unavailableItems as $item)
            <div class="border-b border-gray-200 py-4 last:border-0 last:pb-0 first:pt-0">
                <div class="flex items-center">
                    @if($item->cartItem && $item->cartItem->outfit && $item->cartItem->outfit->image)
                        <img src="{{ asset($item->cartItem->outfit->image) }}" alt="{{ $item->cartItem->outfit->name }}" class="w-20 h-20 object-cover rounded-md mr-4">
                    @else
                        <div class="w-20 h-20 bg-gray-200 flex items-center justify-center rounded-md mr-4">
                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="text-lg font-semibold">{{ $item->cartItem->outfit->name ?? 'ไม่ระบุชื่อชุด' }}</h4>
                        <div class="text-sm text-gray-600 mt-1">
                            <p>ขนาด: {{ $item->cartItem->size->size ?? 'ไม่ระบุ' }}</p>
                            <p>สี: {{ $item->cartItem->color->color ?? 'ไม่ระบุ' }}</p>
                            <p>จำนวนที่สั่ง: {{ $item->quantity }} ชุด</p>
                            <p class="text-red-500 font-medium">สถานะ: มีจำนวนไม่เพียงพอ</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- ชุดทดแทนที่แนะนำ -->
    @if($suggestions->count() > 0)
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-lg font-semibold text-green-700">ชุดทดแทนที่แนะนำ</h3>
                <p class="text-sm text-gray-500">กรุณาเลือกว่าจะยอมรับหรือปฏิเสธชุดทดแทนที่เสนอมา</p>
            </div>
            <div class="p-4 space-y-6">
                @foreach($suggestions as $suggestion)
                <div class="border rounded-lg overflow-hidden {{ $suggestion->status != 'Pending Selection' ? 'opacity-75' : '' }}">
                    <div class="p-4">
                        <div class="flex">
                            @if($suggestion->outfit && $suggestion->outfit->image)
                                <img src="{{ asset($suggestion->outfit->image) }}" alt="{{ $suggestion->outfit->name }}" class="w-32 h-32 object-cover rounded-md mr-4">
                            @else
                                <div class="w-32 h-32 bg-gray-200 flex items-center justify-center rounded-md mr-4">
                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <h4 class="text-lg font-semibold">{{ $suggestion->outfit->name ?? 'ชุดทดแทน' }}</h4>
                                    
                                    @if($suggestion->status == 'Pending Selection')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">รอการตอบรับ</span>
                                    @elseif($suggestion->status == 'Selected')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">ยอมรับแล้ว</span>
                                    @elseif($suggestion->status == 'Rejected')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">ปฏิเสธแล้ว</span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-600 text-sm my-2">{{ Str::limit($suggestion->outfit->description ?? '', 100) }}</p>
                                
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <p class="text-gray-600">ขนาด:</p>
                                        <p class="font-medium">{{ $suggestion->size->size ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">สี:</p>
                                        <p class="font-medium">{{ $suggestion->color->color ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">จำนวน:</p>
                                        <p class="font-medium">{{ $suggestion->quantity }} ชุด</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">ราคา:</p>
                                        <p class="font-medium">{{ number_format($suggestion->outfit->price ?? 0, 2) }} ฿ / ชุด</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($suggestion->status == 'Pending Selection')
                        <div class="mt-4 pt-4 border-t flex justify-end space-x-3">
                            <form action="{{ route('profile.customer.confirm-selection') }}" method="POST">
                                @csrf
                                <input type="hidden" name="selection_id" value="{{ $suggestion->select_outfit_id }}">
                                <input type="hidden" name="action" value="accept">
                                <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                                    ยอมรับ
                                </button>
                            </form>
                            
                            <form action="{{ route('profile.customer.confirm-selection') }}" method="POST">
                                @csrf
                                <input type="hidden" name="selection_id" value="{{ $suggestion->select_outfit_id }}">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                                    ปฏิเสธ
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="text-center py-10">
                <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
                <p class="text-lg text-gray-600">ยังไม่มีข้อเสนอชุดทดแทนจากร้านค้า</p>
                <p class="text-sm text-gray-500 mt-1">โปรดรอทางร้านค้าเสนอชุดทดแทนในไม่ช้า</p>
            </div>
        </div>
    @endif
</div>
@endsection
