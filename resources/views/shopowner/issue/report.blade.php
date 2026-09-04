@extends('layouts.shopowner-layout')

@section('title', 'แจ้งปัญหา')

@section('content')
<div class="container mx-auto">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 border-b pb-2">แจ้งปัญหา</h2>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('shopowner.issue.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">หัวข้อปัญหา:</label>
                <input type="text" name="title" id="title" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-2">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">รายละเอียดปัญหา:</label>
                <textarea name="description" id="description" rows="5" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-2"></textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">แนบรูปภาพ (ถ้ามี):</label>
                <input type="file" name="file" id="file" accept="image/jpeg,image/png,image/jpg,image/gif"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-2">
                <p class="text-xs text-gray-500 mt-1">รองรับไฟล์ .jpeg, .png, .jpg, .gif ขนาดสูงสุด 2MB</p>
                @error('file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-paper-plane mr-2"></i> ส่งรายงาน
                </button>
                
                <a href="{{ route('shopowner.shops.my-shop') }}" 
                   class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> กลับ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
