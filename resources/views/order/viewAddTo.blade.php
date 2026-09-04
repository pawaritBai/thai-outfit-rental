@extends('layouts.main')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-start">ทำการสั่งซื้อ</h2>

    <!-- รายการสินค้าในตะกร้า -->
    <div class="max-w-4xl mx-auto space-y-4">
        @foreach ($cartItems as $cartItem)
            <div class="bg-white shadow-md rounded-lg p-6 flex gap-4">
                <!-- ภาพสินค้า -->
                <img src="{{ asset( $cartItem->outfit->image) }}" alt="{{ $cartItem->outfit->name }}" class="w-32 h-32 object-cover rounded-lg">

                <!-- รายละเอียดสินค้า -->
                <div class="flex-1">
                    <h4 class="text-xl font-semibold">{{ $cartItem->outfit->name }}</h4>
                    <p class="text-lg text-gray-600">สี: {{ $cartItem->outfit->sizeAndColors->where('color_id', $cartItem->color_id)->first()->color->color_name ?? 'ไม่ระบุ' }}</p>
                    <p class="text-lg text-gray-600">ขนาด: {{ $cartItem->outfit->sizeAndColors->where('size_id', $cartItem->size_id)->first()->size->size_name ?? 'ไม่ระบุ' }}</p>
                    
                    <!-- ราคา และจำนวน -->
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-lg text-green-600 font-semibold">ราคาต่อหน่วย: {{ number_format($cartItem->outfit->price, 2) }} ฿</p>
                        <div class="flex items-center">
                            <span class="px-4">{{ $cartItem->quantity }}</span>
                        </div>
                    </div>
                    
                    <p class="text-lg font-semibold mt-2">ราคารวม: {{ number_format($cartItem->quantity * $cartItem->outfit->price, 2) }} ฿</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ตัวเลือกเพิ่มเติม -->
    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6 mt-6">
        <form action="{{ route('orderdetail.addTo') }}" method="POST">
            @csrf
            @foreach ($cartItems as $cartItem)
                <input type="hidden" name="cart_item_ids[]" value="{{ $cartItem->id }}">
            @endforeach

            <!-- ตัวเลือกการจอง -->
            <div class="mb-4">
                <label class="block text-lg font-medium mb-2">รอบการจอง:</label>
                <select name="booking_cycle" class="w-full p-2 border rounded-md" required>
                    <option value="1">1 วัน</option>
                    <option value="2">2 วัน</option>
                </select>
            </div>

            <!-- วิธีรับสินค้า -->
            <div class="mb-4">
                <label class="block text-lg font-medium mb-2">วิธีการรับสินค้า:</label>
                <select name="deliveryOptions" class="w-full p-2 border rounded-md" required>
                    <option value="self pick-up">รับเอง</option>
                    <option value="delivery">จัดส่ง</option>
                </select>
            </div>

            <!-- สรุปราคา -->
            <div class="flex justify-between items-center mt-6">
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <p class="text-lg font-semibold">โค้ดส่วนลดร้านค้า</p>
                    <p class="text-gray-600">ลดสูงสุด: 1000฿</p>
                    <button class="mt-2 px-4 py-2 bg-yellow-400 text-white rounded-md">คัดลอกโค้ดส่วนลด</button>
                </div>

                <div class="bg-gray-100 p-4 rounded-lg">
                    <p class="text-lg font-semibold">ราคารวมทั้งหมด</p>
                    <p class="text-green-600 text-xl font-bold">{{ number_format($cartItems->sum(fn($cartItem) => $cartItem->quantity * $cartItem->outfit->price), 2) }} ฿</p>
                </div>
            </div>

            <!-- ปุ่มยืนยัน -->
            <div class="mt-6 text-center">
                <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300">สั่งซื้อ</button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>

</script>
