@extends('layouts.admin-layout')

@section('title', 'แก้ไขหมวดหมู่')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">แก้ไขหมวดหมู่: {{ $category->category_name }}</h2>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
            <i class="fa fa-arrow-left mr-2"></i> กลับ
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('admin.categories.update', $category->category_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อหมวดหมู่ *</label>
                <input type="text" name="category_name" id="category_name" value="{{ old('category_name', $category->category_name) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('category_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fa fa-save mr-2"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
