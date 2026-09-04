@extends('layouts.shopowner-layout')

@section('title', 'ร้านค้าของฉัน')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-6">ร้านค้าของฉัน</h2>

    <div class="bg-white p-6 shadow rounded-lg">
        @if(!$shop)
            <div class="text-center py-8">
                <div class="mb-6">
                    <i class="fa fa-store text-5xl text-gray-400"></i>
                    <p class="mt-4 text-lg">คุณยังไม่มีร้านค้า</p>
                    <p class="text-gray-500 mb-6">ลงทะเบียนร้านค้าใหม่เพื่อเริ่มการขาย</p>
                </div>
                <a href="{{ route('shopowner.shops.create') }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium text-lg">
                   <i class="fa fa-plus-circle mr-2"></i> ลงทะเบียนร้านค้าใหม่
                </a>
            </div>
        @else
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">ข้อมูลร้านค้า</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="mb-2"><i class="fa fa-tag mr-2 text-blue-500"></i><strong>สถานะ:</strong> 
                            @if($shop->status == 'active')
                                <span class="text-green-600 font-semibold bg-green-100 px-2 py-1 rounded-md">ใช้งานได้</span>
                            @else
                                <span class="text-red-600 font-semibold bg-red-100 px-2 py-1 rounded-md">รอการอนุมัติ</span>
                            @endif
                        </p>
                        <p class="mb-2"><i class="fa fa-store mr-2 text-blue-500"></i><strong>ชื่อร้าน:</strong> {{ $shop->shop_name }}</p>
                        
                        @if($shop->address)
                            <p class="mb-2"><i class="fa fa-map-marker-alt mr-2 text-blue-500"></i><strong>ที่ตั้ง:</strong> 
                                {{ $shop->address->HouseNumber }} 
                                ถ.{{ $shop->address->Street ? $shop->address->Street : '' }}
                                ต.{{ $shop->address->Subdistrict }} 
                                อ.{{ $shop->address->District }} 
                                จ.{{ $shop->address->Province }} 
                                {{ $shop->address->PostalCode }}
                            </p>
                        @endif
                    </div>
                </div>
                
                <div class="mb-4">
                    <h4 class="font-semibold mb-2">คำอธิบายร้านค้า:</h4>
                    <p class="bg-gray-50 p-3 rounded">{{ $shop->shop_description }}</p>
                </div>
                
                <div class="mb-4">
                    <h4 class="font-semibold mb-2">เงื่อนไขการเช่า:</h4>
                    <p class="bg-gray-50 p-3 rounded">{{ $shop->rental_terms }}</p>
                </div>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('shopowner.shops.edit-my-shop', $shop->shop_id) }}" 
                     class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                     <i class="fa fa-edit mr-1"></i> แก้ไขข้อมูลร้านค้า
                    </a>
                 @if($shop && $shop->status == 'active')
                     <a href="{{ route('shopowner.outfits.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                          <i class="fa fa-tshirt mr-1"></i> จัดการชุด
                     </a>
                     <a href="{{ route('shopowner.bookings.index') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                          <i class="fa fa-calendar-check mr-1"></i> จัดการการจอง
                     </a>
                     <a href="{{ route('shopowner.promotions.index') }}" class="px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600">
                          <i class="fa fa-percent mr-1"></i> จัดการโปรโมชั่น
                     </a>
                     <a href="{{ route('shopowner.stats.income') }}" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">
                          <i class="fa fa-chart-bar mr-1"></i> ดูสถิติ
                     </a>
                     <a href="{{ route('shopowner.issue.index') }}" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                          <i class="fa fa-exclamation-circle mr-1"></i> แจ้งปัญหา
                     </a>
                 @elseif($shop)
                     <button disabled class="px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed">
                          <i class="fa fa-tshirt mr-1"></i> จัดการชุด (รอการอนุมัติร้านค้า)
                     </button>
                     <button disabled class="px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed">
                          <i class="fa fa-calendar-check mr-1"></i> จัดการการจอง (รอการอนุมัติร้านค้า)
                     </button>
                     <button disabled class="px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed">
                          <i class="fa fa-percent mr-1"></i> จัดการโปรโมชั่น (รอการอนุมัติร้านค้า)
                     </button>
                     <button disabled class="px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed">
                          <i class="fa fa-chart-bar mr-1"></i> ดูสถิติ (รอการอนุมัติร้านค้า)
                     </button>
                     <a href="{{ route('shopowner.issue.index') }}" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                          <i class="fa fa-exclamation-circle mr-1"></i> แจ้งปัญหา
                     </a>
                 @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection