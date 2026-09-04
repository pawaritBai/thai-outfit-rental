@extends('layouts.admin-layout')

@section('title', 'จัดการร้านค้า')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-store mr-2 text-[#8B9DF9]"></i>จัดการร้านค้า
        </h1>

        <form action="{{ route('admin.shops.acceptance') }}" method="GET">
            @csrf
            <button type="submit" class="btn btn-primary relative">
                <i class="fas fa-check-circle mr-2"></i>รายการรออนุมัติ
                @php
                    $pendingShopsCount = \App\Models\Shop::where('status', 'pending')->count();
                @endphp
                @if($pendingShopsCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $pendingShopsCount }}
                    </span>
                @endif
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-[#8B9DF9] text-xl mr-3"></i>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">จัดการรายการร้านค้า</h2>
                    <p class="text-gray-600">ดูและจัดการร้านค้าทั้งหมดในระบบ คลิกที่หัวข้อตารางเพื่อเรียงลำดับ</p>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.shops.index') }}" class="mb-4 flex p-4">
            <input type="text" name="search" placeholder="ค้นหา รหัสร้าน, ชื่อร้าน"
                class="border p-2 w-full rounded-l-md"
                value="{{ request('search') }}">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md">ค้นหา</button>
        </form>

        <div class="table-container">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="header-cell text-center">
                            <form action="{{ route('admin.shops.index') }}" method="GET">
                                @csrf
                                <input type="hidden" name="orderBy" value="shop_id">
                                <input type="hidden" name="direction" value="{{ request('orderBy') == 'shop_id' && request('direction') == 'asc' ? 'desc' : 'asc' }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="w-full flex justify-center items-center">
                                    ID
                                    <i class="fas fa-{{ request('orderBy') == 'shop_id' ? (request('direction') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }} ml-1"></i>
                                </button>
                            </form>
                        </th>
                        <th class="header-cell text-center">
                            <form action="{{ route('admin.shops.index') }}" method="GET">
                                @csrf
                                <input type="hidden" name="orderBy" value="shop_name">
                                <input type="hidden" name="direction" value="{{ request('orderBy') == 'shop_name' && request('direction') == 'asc' ? 'desc' : 'asc' }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="w-full flex justify-center items-center">
                                    ชื่อร้าน
                                    <i class="fas fa-{{ request('orderBy') == 'shop_name' ? (request('direction') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }} ml-1"></i>
                                </button>
                            </form>
                        </th>
                        <th class="header-cell text-center">
                            <form action="{{ route('admin.shops.index') }}" method="GET">
                                @csrf
                                <input type="hidden" name="orderBy" value="shop_description">
                                <input type="hidden" name="direction" value="{{ request('orderBy') == 'shop_description' && request('direction') == 'asc' ? 'desc' : 'asc' }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="w-full flex justify-center items-center">
                                    รายละเอียด
                                    <i class="fas fa-{{ request('orderBy') == 'shop_description' ? (request('direction') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }} ml-1"></i>
                                </button>
                            </form>
                        </th>
                        <th class="header-cell text-center">
                            <form action="{{ route('admin.shops.index') }}" method="GET">
                                @csrf
                                <input type="hidden" name="orderBy" value="shop_location">
                                <input type="hidden" name="direction" value="{{ request('orderBy') == 'shop_location' && request('direction') == 'asc' ? 'desc' : 'asc' }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="w-full flex justify-center items-center">
                                    ที่ตั้ง
                                    <i class="fas fa-{{ request('orderBy') == 'shop_location' ? (request('direction') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }} ml-1"></i>
                                </button>
                            </form>
                        </th>
                        <th class="header-cell text-center">
                            <form action="{{ route('admin.shops.index') }}" method="GET">
                                @csrf
                                <input type="hidden" name="orderBy" value="status">
                                <input type="hidden" name="direction" value="{{ request('orderBy') == 'status' && request('direction') == 'asc' ? 'desc' : 'asc' }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="w-full flex justify-center items-center">
                                    สถานะ
                                    <i class="fas fa-{{ request('orderBy') == 'status' ? (request('direction') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }} ml-1"></i>
                                </button>
                            </form>
                        </th>
                        <th class="header-cell text-center">
                            <form action="{{ route('admin.shops.index') }}" method="GET">
                                @csrf
                                <input type="hidden" name="orderBy" value="created_at">
                                <input type="hidden" name="direction" value="{{ request('orderBy') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc' }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="w-full flex justify-center items-center">
                                    วันที่สร้าง
                                    <i class="fas fa-{{ request('orderBy') == 'created_at' ? (request('direction') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }} ml-1"></i>
                                </button>
                            </form>
                        </th>
                        <th class="header-cell text-center">
                            <form action="{{ route('admin.shops.index') }}" method="GET">
                                @csrf
                                <input type="hidden" name="orderBy" value="shop_owner_id">
                                <input type="hidden" name="direction" value="{{ request('orderBy') == 'shop_owner_id' && request('direction') == 'asc' ? 'desc' : 'asc' }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="w-full flex justify-center items-center">
                                    ชื่อเจ้าของ
                                    <i class="fas fa-{{ request('orderBy') == 'shop_owner_id' ? (request('direction') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }} ml-1"></i>
                                </button>
                            </form>
                        </th>
                        <th class="w-full flex justify-center items-center">
                        </th>
                    </tr>
                </thead>                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($shops as $shop)
                    <tr class="table-row">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $shop->shop_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium">{{ $shop->shop_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div class="truncate-text" title="{{ $shop->shop_description }}">
                                {{ $shop->shop_description }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                @if($shop->address)
                                    {{ $shop->address->HouseNumber }} 
                                    {{ $shop->address->Street }} 
                                    {{ $shop->address->Subdistrict }} 
                                    {{ $shop->address->District }} 
                                    {{ $shop->address->Province }} 
                                    {{ $shop->address->PostalCode }}
                                @else
                                    ไม่มีข้อมูลที่อยู่
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-badge {{ $shop->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ $shop->status == 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($shop->created_at)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $shop->user ? $shop->user->name : ''}}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                            <form action="{{ route('admin.shops.edit', $shop->shop_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-edit mr-1"></i> แก้ไข
                                </button>
                            </form>
                            <form action="{{ route('admin.shops.toggleStatus', $shop->shop_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="{{ $shop->status == 'active' ? 'inactive' : 'active' }}">
                                <input type="hidden" name="orderBy" value="{{ request('orderBy') }}">
                                <input type="hidden" name="direction" value="{{ request('direction') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="btn {{ $shop->status == 'active' ? 'btn-danger' : 'btn-success' }}">
                                    @if($shop->status == 'active')
                                    <i class="fas fa-ban mr-1"></i> ปิดใช้งาน
                                    @else
                                    <i class="fas fa-check-circle mr-1"></i> เปิดใช้งาน
                                    @endif
                                </button>
                            </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* คง style เดิมไว้ */
</style>
@endsection