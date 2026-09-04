@extends('layouts.shopowner-layout')

@section('title', 'รายละเอียดการจอง')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('shopowner.bookings.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fa fa-arrow-left mr-2"></i>กลับไปยังรายการจอง
            </a>
            <h2 class="text-2xl font-bold mt-2">รายละเอียดการจอง #{{ $booking->booking_id }}</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Order Summary Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-lg font-semibold">สรุปการจอง</h3>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">สถานะ:</span>
                        <span class="font-medium">
                            @if($booking->status == 'pending')
                                <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold">
                                    รอการยืนยัน
                                </span>
                            @elseif($booking->status == 'confirmed')
                                <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">
                                    ยืนยันแล้ว
                                </span>
                            @elseif($booking->status == 'partial paid')
                                <span class="px-2 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold">
                                    ชำระบางส่วน
                                </span>
                            @elseif($booking->status == 'cancelled')
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">
                                    ยกเลิก
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">วันที่สั่งซื้อ:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($booking->purchase_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">จำนวนรายการ:</span>
                        <span class="font-medium">{{ $booking->orderDetails->count() }} รายการ</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">จำนวนชุดรวม:</span>
                        <span class="font-medium">{{ $booking->orderDetails->sum('quantity') }} ชุด</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">ยอดรวม:</span>
                        <span class="font-bold text-lg">{{ number_format($booking->orderDetails->sum('total'), 2) }} ฿</span>
                    </div>
                </div>
                
                @if($booking->promotion)
                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-semibold mb-2">โปรโมชั่นที่ใช้</h4>
                    <div class="bg-blue-50 p-3 rounded-md">
                        <p class="text-blue-700 font-medium">{{ $booking->promotion->promotion_name }}</p>
                        <p class="text-sm text-blue-600">รหัส: {{ $booking->promotion->promotion_code }}</p>
                        <p class="text-sm text-blue-600">ส่วนลด: {{ number_format($booking->promotion->discount_amount, 2) }} ฿</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Customer Information Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-lg font-semibold">ข้อมูลลูกค้า</h3>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">ชื่อ-นามสกุล:</p>
                    <p class="font-medium">{{ $booking->user->name ?? 'ไม่ระบุ' }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">อีเมล:</p>
                    <p class="font-medium">{{ $booking->user->email ?? 'ไม่ระบุ' }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">เบอร์โทรศัพท์:</p>
                    <p class="font-medium">{{ $booking->user->phone ?? 'ไม่ระบุ' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Delivery Information Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-lg font-semibold">ข้อมูลการจัดส่ง</h3>
            </div>
            <div class="p-4">
                @if($booking->customerAddress && $booking->customerAddress->address)
                    <div class="mb-4">
                        <p class="text-gray-600 mb-1">จัดส่งที่:</p>
                        <p class="font-medium">
                            บ้านเลขที่ {{ $booking->customerAddress->address->HouseNumber }}
                            @if($booking->customerAddress->address->Street) ถนน{{ $booking->customerAddress->address->Street }} @endif
                            @if($booking->customerAddress->address->Subdistrict) ตำบล/แขวง{{ $booking->customerAddress->address->Subdistrict }} @endif
                            @if($booking->customerAddress->address->District) อำเภอ/เขต{{ $booking->customerAddress->address->District }} @endif
                            @if($booking->customerAddress->address->Province) จังหวัด{{ $booking->customerAddress->address->Province }} @endif
                            @if($booking->customerAddress->address->PostalCode) {{ $booking->customerAddress->address->PostalCode }} @endif
                        </p>
                    </div>
                @else
                    <div class="mb-4">
                        <p class="text-gray-600 mb-1">จัดส่งที่:</p>
                        <p class="font-medium">รับที่ร้าน</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-600 mb-1">ที่ตั้งร้าน:</p>
                        @if($booking->shop && $booking->shop->address)
                            <p class="font-medium">
                                บ้านเลขที่ {{ $booking->shop->address->HouseNumber }}
                                @if($booking->shop->address->Street) ถนน{{ $booking->shop->address->Street }} @endif
                                @if($booking->shop->address->Subdistrict) ตำบล/แขวง{{ $booking->shop->address->Subdistrict }} @endif
                                @if($booking->shop->address->District) อำเภอ/เขต{{ $booking->shop->address->District }} @endif
                                @if($booking->shop->address->Province) จังหวัด{{ $booking->shop->address->Province }} @endif
                                @if($booking->shop->address->PostalCode) {{ $booking->shop->address->PostalCode }} @endif
                            </p>
                        @else
                            <p class="font-medium">{{ $booking->shop->shop_location ?? 'ไม่ระบุ' }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Separate Order Items Cards - Available Items -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-lg font-semibold text-green-700">รายการชุดที่มีจำนวนเพียงพอ</h3>
            <p class="text-sm text-gray-500">รายการชุดที่มีสินค้าพร้อมให้เช่าทันที</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รูปภาพ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อชุด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ขนาด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สี</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวน</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคาต่อชิ้น</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่จอง</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคารวม</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $hasAvailableItems = false; @endphp
                    @foreach($booking->orderDetails as $orderDetail)
                        @if($orderDetail->booking_cycle == 1)
                            @php $hasAvailableItems = true; @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($orderDetail->cartItem && $orderDetail->cartItem->outfit && $orderDetail->cartItem->outfit->image)
                                        <img src="{{ asset($orderDetail->cartItem->outfit->image) }}" alt="{{ $orderDetail->cartItem->outfit->name }}" class="h-16 w-16 object-cover rounded">
                                    @else
                                        <div class="h-16 w-16 bg-gray-200 flex items-center justify-center rounded">
                                            <i class="fa fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $orderDetail->cartItem->outfit->name ?? 'ไม่ระบุ' }}
                                    </div>
                                    @if($orderDetail->cartItem && $orderDetail->cartItem->outfit)
                                        <div class="text-xs text-gray-500 mt-1">
                                            รหัสชุด: {{ $orderDetail->cartItem->outfit->outfit_id }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @php
                                            $size = 'ไม่ระบุ';
                                            
                                            // Try multiple possible paths to get size data
                                            if($orderDetail->cartItem) {
                                                // Path 1: Direct access to size field
                                                if(!empty($orderDetail->cartItem->size) && is_string($orderDetail->cartItem->size)) {
                                                    $size = $orderDetail->cartItem->size;
                                                }
                                                // Path 2: Access through size_id
                                                elseif(!empty($orderDetail->cartItem->size_id)) {
                                                    $sizeObj = \App\Models\ThaiOutfitSize::find($orderDetail->cartItem->size_id);
                                                    if($sizeObj) {
                                                        $size = $sizeObj->size;
                                                    }
                                                }
                                                // Path 3: Access through sizeAndColor
                                                elseif(isset($orderDetail->cartItem->sizeAndColor_id)) {
                                                    $sizeAndColor = \App\Models\ThaiOutfitSizeAndColor::find($orderDetail->cartItem->sizeAndColor_id);
                                                    if($sizeAndColor && $sizeAndColor->size_id) {
                                                        $sizeObj = \App\Models\ThaiOutfitSize::find($sizeAndColor->size_id);
                                                        if($sizeObj) {
                                                            $size = $sizeObj->size;
                                                        }
                                                    }
                                                }
                                                // Path 4: Debug output - print the actual data we have
                                                if($size === 'ไม่ระบุ') {
                                                    $cartItemData = json_encode($orderDetail->cartItem);
                                                    $size = "ไม่พบข้อมูลขนาด - Debug: " . (strlen($cartItemData) > 50 ? substr($cartItemData, 0, 50)."..." : $cartItemData);
                                                }
                                            }
                                            
                                            echo $size;
                                        @endphp
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @php
                                            $color = 'ไม่ระบุ';
                                            
                                            // Try multiple possible paths to get color data
                                            if($orderDetail->cartItem) {
                                                // Path 1: Direct access to color field
                                                if(!empty($orderDetail->cartItem->color) && is_string($orderDetail->cartItem->color)) {
                                                    $color = $orderDetail->cartItem->color;
                                                }
                                                // Path 2: Access through color_id
                                                elseif(!empty($orderDetail->cartItem->color_id)) {
                                                    $colorObj = \App\Models\ThaiOutfitColor::find($orderDetail->cartItem->color_id);
                                                    if($colorObj) {
                                                        $color = $colorObj->color;
                                                    }
                                                }
                                                // Path 3: Access through sizeAndColor
                                                elseif(isset($orderDetail->cartItem->sizeAndColor_id)) {
                                                    $sizeAndColor = \App\Models\ThaiOutfitSizeAndColor::find($orderDetail->cartItem->sizeAndColor_id);
                                                    if($sizeAndColor && $sizeAndColor->color_id) {
                                                        $colorObj = \App\Models\ThaiOutfitColor::find($sizeAndColor->color_id);
                                                        if($colorObj) {
                                                            $color = $colorObj->color;
                                                        }
                                                    }
                                                }
                                                // Path 4: Debug output - print the actual data we have
                                                if($color === 'ไม่ระบุ') {
                                                    $cartItemData = json_encode($orderDetail->cartItem);
                                                    $color = "ไม่พบข้อมูลสี - Debug: " . (strlen($cartItemData) > 50 ? substr($cartItemData, 0, 50)."..." : $cartItemData);
                                                }
                                            }
                                            
                                            echo $color;
                                        @endphp
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $orderDetail->quantity }} ชุด</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($orderDetail->cartItem->outfit->price ?? 0, 2) }} ฿
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $orderDetail->reservation_date ? \Carbon\Carbon::parse($orderDetail->reservation_date)->format('d/m/Y') : 'ไม่ระบุ' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($orderDetail->total, 2) }} ฿
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    
                    @if(!$hasAvailableItems)
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">ไม่พบรายการชุดที่มีจำนวนเพียงพอ</td>
                        </tr>
                    @endif
                </tbody>
                @if($hasAvailableItems)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-medium">จำนวนรวม:</td>
                        <td class="px-6 py-4 font-medium">
                            {{ $booking->orderDetails->where('booking_cycle', 1)->sum('quantity') }} ชุด
                        </td>
                        <td colspan="2" class="px-6 py-4 text-right font-medium">ยอดรวม:</td>
                        <td class="px-6 py-4 font-bold">
                            {{ number_format($booking->orderDetails->where('booking_cycle', 1)->sum('total'), 2) }} ฿
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Separate Order Items Cards - Backordered Items -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-lg font-semibold text-orange-700">รายการชุดที่มีจำนวนไม่เพียงพอ</h3>
            <p class="text-sm text-gray-500">รายการชุดที่ต้องการการจัดการพิเศษ </p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รูปภาพ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อชุด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ขนาด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สี</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวน</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคาต่อชิ้น</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่จอง</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคารวม</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $hasBackorderedItems = false; @endphp
                    @foreach($booking->orderDetails as $orderDetail)
                        @if($orderDetail->booking_cycle == 2)
                            @php $hasBackorderedItems = true; @endphp
                            <tr class="bg-orange-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($orderDetail->cartItem && $orderDetail->cartItem->outfit && $orderDetail->cartItem->outfit->image)
                                        <img src="{{ asset($orderDetail->cartItem->outfit->image) }}" alt="{{ $orderDetail->cartItem->outfit->name }}" class="h-16 w-16 object-cover rounded">
                                    @else
                                        <div class="h-16 w-16 bg-gray-200 flex items-center justify-center rounded">
                                            <i class="fa fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $orderDetail->cartItem->outfit->name ?? 'ไม่ระบุ' }}
                                    </div>
                                    @if($orderDetail->cartItem && $orderDetail->cartItem->outfit)
                                        <div class="text-xs text-gray-500 mt-1">
                                            รหัสชุด: {{ $orderDetail->cartItem->outfit->outfit_id }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @php
                                            $size = 'ไม่ระบุ';
                                            
                                            // Try multiple possible paths to get size data
                                            if($orderDetail->cartItem) {
                                                // Path 1: Direct access to size field
                                                if(!empty($orderDetail->cartItem->size) && is_string($orderDetail->cartItem->size)) {
                                                    $size = $orderDetail->cartItem->size;
                                                }
                                                // Path 2: Access through size_id
                                                elseif(!empty($orderDetail->cartItem->size_id)) {
                                                    $sizeObj = \App\Models\ThaiOutfitSize::find($orderDetail->cartItem->size_id);
                                                    if($sizeObj) {
                                                        $size = $sizeObj->size;
                                                    }
                                                }
                                                // Path 3: Access through sizeAndColor
                                                elseif(isset($orderDetail->cartItem->sizeAndColor_id)) {
                                                    $sizeAndColor = \App\Models\ThaiOutfitSizeAndColor::find($orderDetail->cartItem->sizeAndColor_id);
                                                    if($sizeAndColor && $sizeAndColor->size_id) {
                                                        $sizeObj = \App\Models\ThaiOutfitSize::find($sizeAndColor->size_id);
                                                        if($sizeObj) {
                                                            $size = $sizeObj->size;
                                                        }
                                                    }
                                                }
                                                // Path 4: Debug output - print the actual data we have
                                                if($size === 'ไม่ระบุ') {
                                                    $cartItemData = json_encode($orderDetail->cartItem);
                                                    $size = "ไม่พบข้อมูลขนาด - Debug: " . (strlen($cartItemData) > 50 ? substr($cartItemData, 0, 50)."..." : $cartItemData);
                                                }
                                            }
                                            
                                            echo $size;
                                        @endphp
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @php
                                            $color = 'ไม่ระบุ';
                                            
                                            // Try multiple possible paths to get color data
                                            if($orderDetail->cartItem) {
                                                // Path 1: Direct access to color field
                                                if(!empty($orderDetail->cartItem->color) && is_string($orderDetail->cartItem->color)) {
                                                    $color = $orderDetail->cartItem->color;
                                                }
                                                // Path 2: Access through color_id
                                                elseif(!empty($orderDetail->cartItem->color_id)) {
                                                    $colorObj = \App\Models\ThaiOutfitColor::find($orderDetail->cartItem->color_id);
                                                    if($colorObj) {
                                                        $color = $colorObj->color;
                                                    }
                                                }
                                                // Path 3: Access through sizeAndColor
                                                elseif(isset($orderDetail->cartItem->sizeAndColor_id)) {
                                                    $sizeAndColor = \App\Models\ThaiOutfitSizeAndColor::find($orderDetail->cartItem->sizeAndColor_id);
                                                    if($sizeAndColor && $sizeAndColor->color_id) {
                                                        $colorObj = \App\Models\ThaiOutfitColor::find($sizeAndColor->color_id);
                                                        if($colorObj) {
                                                            $color = $colorObj->color;
                                                        }
                                                    }
                                                }
                                                // Path 4: Debug output - print the actual data we have
                                                if($color === 'ไม่ระบุ') {
                                                    $cartItemData = json_encode($orderDetail->cartItem);
                                                    $color = "ไม่พบข้อมูลสี - Debug: " . (strlen($cartItemData) > 50 ? substr($cartItemData, 0, 50)."..." : $cartItemData);
                                                }
                                            }
                                            
                                            echo $color;
                                        @endphp
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $orderDetail->quantity }} ชุด</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($orderDetail->cartItem->outfit->price ?? 0, 2) }} ฿
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $orderDetail->reservation_date ? \Carbon\Carbon::parse($orderDetail->reservation_date)->format('d/m/Y') : 'ไม่ระบุ' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($orderDetail->total, 2) }} ฿
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('shopowner.bookings.suggest-alternatives', ['booking' => $booking->booking_id, 'orderDetail' => $orderDetail->orderDetail_id]) }}" 
                                       class="text-orange-600 hover:text-orange-900 bg-orange-100 px-3 py-1 rounded">
                                        <i class="fa fa-exchange-alt mr-1"></i> เสนอชุดทดแทน
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    
                    @if(!$hasBackorderedItems)
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">ไม่พบรายการชุดที่มีจำนวนไม่เพียงพอ</td>
                        </tr>
                    @endif
                </tbody>
                @if($hasBackorderedItems)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-medium">จำนวนรวม:</td>
                        <td class="px-6 py-4 font-medium">
                            {{ $booking->orderDetails->where('booking_cycle', 2)->sum('quantity') }} ชุด
                        </td>
                        <td colspan="2" class="px-6 py-4 text-right font-medium">ยอดรวม:</td>
                        <td class="px-6 py-4 font-bold">
                            {{ number_format($booking->orderDetails->where('booking_cycle', 2)->sum('total'), 2) }} ฿
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- เพิ่มหลังส่วนรายการชุดที่มีจำนวนไม่เพียงพอ -->
    @php
        $selections = \App\Models\SelectOutfitDetail::where('booking_id', $booking->booking_id)->get();
    @endphp

    @if($selections->count() > 0)
    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-lg font-semibold">ประวัติการเสนอชุดทดแทน</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชุดทดแทน</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวน</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($selections as $selection)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($selection->created_at)->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($selection->outfit && $selection->outfit->image)
                                    <img src="{{ asset($selection->outfit->image) }}" alt="{{ $selection->outfit->name }}" class="h-10 w-10 object-cover rounded mr-3">
                                @endif
                                <div class="text-sm font-medium text-gray-900">{{ $selection->outfit->name ?? 'ไม่ระบุ' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $selection->quantity }} ชุด</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($selection->status == 'Pending Selection')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    รอการตอบรับ
                                </span>
                            @elseif($selection->status == 'Selected')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ยอมรับแล้ว
                                </span>
                            @elseif($selection->status == 'Rejected')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    ปฏิเสธแล้ว
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-4">
        <!-- Status update section removed to fix undefined route error -->
    </div>
</div>
@endsection
