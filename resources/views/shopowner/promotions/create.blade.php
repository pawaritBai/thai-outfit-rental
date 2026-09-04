@extends('layouts.shopowner-layout')

@section('title', 'เพิ่มโปรโมชั่นใหม่')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('shopowner.promotions.index') }}" class="mr-4">
            <i class="fa fa-arrow-left text-gray-600"></i>
        </a>
        <h2 class="text-2xl font-bold">เพิ่มโปรโมชั่นใหม่</h2>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <form action="{{ route('shopowner.promotions.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="promotion_name" class="block text-gray-700 font-medium mb-2">ชื่อโปรโมชั่น <span class="text-red-500">*</span></label>
                <input type="text" name="promotion_name" id="promotion_name" value="{{ old('promotion_name') }}" 
                    class="w-full p-2 border rounded-md @error('promotion_name') border-red-500 @enderror" required>
                @error('promotion_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-medium mb-2">รายละเอียด</label>
                <textarea name="description" id="description" rows="3" 
                    class="w-full p-2 border rounded-md @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="discount_amount" class="block text-gray-700 font-medium mb-2">จำนวนส่วนลด (บาท) <span class="text-red-500">*</span></label>
                <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount') }}" 
                    min="1" step="1" class="w-full p-2 border rounded-md @error('discount_amount') border-red-500 @enderror" required>
                @error('discount_amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="block text-gray-700 font-medium mb-2">วันที่เริ่ม <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" 
                        class="w-full p-2 border rounded-md @error('start_date') border-red-500 @enderror" required>
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-gray-700 font-medium mb-2">วันที่สิ้นสุด <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" 
                        class="w-full p-2 border rounded-md @error('end_date') border-red-500 @enderror" required>
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="{{ route('shopowner.promotions.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    ยกเลิก
                </a>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    บันทึกโปรโมชั่น
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
