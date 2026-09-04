@extends('layouts.shopowner-layout')

@section('title', 'จัดการโปรโมชั่น')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">จัดการโปรโมชั่นร้านค้า</h2>
        <a href="{{ route('shopowner.promotions.create') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
            <i class="fa fa-plus-circle mr-1"></i> เพิ่มโปรโมชั่นใหม่
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if($promotions->isEmpty())
        <div class="bg-white p-6 shadow rounded-lg">
            <div class="text-center py-8">
                <div class="mb-6">
                    <i class="fa fa-percent text-5xl text-gray-400"></i>
                    <p class="mt-4 text-lg">ยังไม่มีโปรโมชั่น</p>
                    <p class="text-gray-500 mb-6">สร้างโปรโมชั่นใหม่เพื่อเพิ่มยอดขาย</p>
                </div>
                <a href="{{ route('shopowner.promotions.create') }}" 
                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium text-lg">
                    <i class="fa fa-plus-circle mr-2"></i> เพิ่มโปรโมชั่นใหม่
                </a>
            </div>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ชื่อโปรโมชั่น
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            รหัสโปรโมชั่น
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ส่วนลด
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            วันที่เริ่ม - สิ้นสุด
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            สถานะ
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            จัดการ
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($promotions as $promotion)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $promotion->promotion_name }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($promotion->description, 30) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $promotion->promotion_code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($promotion->discount_amount, 2) }} ฿
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($promotion->start_date)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($promotion->end_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($promotion->is_active && now()->between($promotion->start_date, $promotion->end_date))
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ใช้งานได้
                                    </span>
                                @elseif(!$promotion->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        ปิดใช้งาน
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        หมดอายุ
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('shopowner.promotions.edit', $promotion->promotion_id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fa fa-edit"></i> แก้ไข
                                </a>
                                <form action="{{ route('shopowner.promotions.destroy', $promotion->promotion_id) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจที่จะลบโปรโมชั่นนี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fa fa-trash"></i> ลบ
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
