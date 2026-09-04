@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">ทำการสั่งซื้อ</h1>
        <div class="flex items-center space-x-4">
            <a href="{{ route('profile') }}" class="text-lg">Profile</a>
            <a href="{{ route('cart') }}" class="text-lg">ตะกร้าสินค้า</a>
            <a href="{{ route('logout') }}" class="text-lg">Logout</a>
        </div>
    </div>

    @foreach($cartItems as $item)
    <div class="flex justify-between items-center p-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/outfits/' . $item->image) }}" alt="Outfit Image" class="w-20 h-20 object-cover">
            <div>
                <h2 class="text-lg font-medium">{{ $item->outfit_name }}</h2>
                <p class="text-sm text-gray-500">ราคาต่อหน่วย: {{ number_format($item->price, 2) }}฿</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <span>จำนวน</span>
                <input type="number" value="{{ $item->quantity }}" class="w-12 text-center border border-gray-300 rounded">
            </div>
            <div>
                <p>ราคาสุทธิ: {{ number_format($item->price * $item->quantity, 2) }}฿</p>
            </div>
        </div>
    </div>
    @endforeach

    <div class="flex justify-between items-center mt-6">
        <p class="text-xl font-semibold">ยอดรวม: {{ number_format($total, 2) }}฿</p>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">ชำระเงิน</button>
    </div>
</div>
@endsection
