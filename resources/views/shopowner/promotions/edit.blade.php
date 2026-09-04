@extends('layouts.shopowner-layout')

@section('title', 'แก้ไขโปรโมชั่น')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('shopowner.promotions.index') }}" class="mr-4">
            <i class="fa fa-arrow-left text-gray-600"></i>
        </a>
        <h2 class="text-2xl font-bold">แก้ไขโปรโมชั่น</h2>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <form action="{{ route('shopowner.promotions.update', $promotion->promotion_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="promotion_name" class="block text-gray-700 font-medium mb-2">ชื่อโปรโมชั่น <span class="text-red-500">*</span></label>
                <input type="text" name="promotion_name" id="promotion_name" value="{{ old('promotion_name', $promotion->promotion_name) }}" 
                    class="w-full p-2 border rounded-md @error('promotion_name') border-red-500 @enderror" required>
                @error('promotion_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-medium mb-2">รายละเอียด</label>
                <textarea name="description" id="description" rows="3" 
                    class="w-full p-2 border rounded-md @error('description') border-red-500 @enderror">{{ old('description', $promotion->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                </div>
            
            <div class="mb-4">
                <label for="promotion_code" class="block text-gray-700 font-medium mb-2">รหัสโปรโมชั่น</label>
                <input type="text" id="promotion_code" value="{{ $promotion->promotion_code }}" 
                    class="w-full p-2 border rounded-md bg-gray-100" readonly>
                <p class="text-sm text-gray-600 mt-1">รหัสโปรโมชั่นไม่สามารถแก้ไขได้</p>
            </div>
            
            <div class="mb-4">
                <label for="discount_amount" class="block text-gray-700 font-medium mb-2">จำนวนส่วนลด (บาท) <span class="text-red-500">*</span></label>
                <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $promotion->discount_amount) }}" 
                    min="1" step="1" class="w-full p-2 border rounded-md @error('discount_amount') border-red-500 @enderror" required>
                @error('discount_amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="block text-gray-700 font-medium mb-2">วันที่เริ่ม <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" 
                        value="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d')) }}" 
                        class="w-full p-2 border rounded-md @error('start_date') border-red-500 @enderror" required>
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-gray-700 font-medium mb-2">วันที่สิ้นสุด <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date" 
                        value="{{ old('end_date', \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d')) }}" 
                        class="w-full p-2 border rounded-md @error('end_date') border-red-500 @enderror" required>
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-4">
                <label for="is_active" class="block text-gray-700 font-medium mb-2">สถานะ</label>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="mr-2"
                        {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
                    <label for="is_active">เปิดใช้งาน</label>
                </div>
                <p class="text-sm text-gray-600 mt-1">เมื่อเปิดใช้งาน ลูกค้าจะสามารถใช้โปรโมชั่นนี้ได้ (ในช่วงวันที่กำหนด)</p>
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="{{ route('shopowner.promotions.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    ยกเลิก
                </a>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
