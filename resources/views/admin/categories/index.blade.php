@extends('layouts.admin-layout')

@section('title', 'จัดการหมวดหมู่')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-tags mr-2 text-[#8B9DF9]"></i>จัดการหมวดหมู่
        </h1>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center shadow-md">
            <i class="fa fa-plus mr-2"></i> เพิ่มหมวดหมู่ใหม่
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-md mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-md mb-4 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-list text-[#8B9DF9] text-xl mr-3"></i>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">รายการหมวดหมู่ทั้งหมด</h2>
                    <p class="text-gray-600 text-sm">จัดการหมวดหมู่สำหรับชุดไทยในระบบ</p>
                </div>
            </div>
        </div>
        
        @if(count($categories) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider w-24">ID</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">ชื่อหมวดหมู่</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider w-40">จำนวนชุด</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider w-40">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="bg-gray-100 text-gray-800 px-3 py-1.5 rounded-full font-medium text-base">{{ $category->category_id }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-base font-medium text-gray-900">{{ $category->category_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(($category->outfits_count ?? 0) > 0)
                                    <span class="px-3 py-1.5 inline-flex text-base leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $category->outfits_count ?? 0 }} ชุด
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 inline-flex text-base leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        0 ชุด
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category->category_id) }}" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm flex items-center whitespace-nowrap">
                                        <i class="fas fa-edit mr-1"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->category_id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm flex items-center whitespace-nowrap"
                                                onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่นี้?')">
                                            <i class="fas fa-trash mr-1"></i> ลบ
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-center">
                {{ $categories->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <div class="inline-block p-6 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-folder-open text-gray-400 text-4xl"></i>
                </div>
                <p class="text-gray-500 mb-4 text-lg">ยังไม่มีหมวดหมู่ในระบบ</p>
                <a href="{{ route('admin.categories.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 text-base">
                    <i class="fa fa-plus mr-2"></i> เพิ่มหมวดหมู่ใหม่
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    /* เพิ่ม animation สำหรับ hover effects */
    .hover\:bg-gray-50:hover {
        transition: all 0.2s ease;
    }
    
    /* ปรับแต่ง pagination */
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
        font-size: 1rem;
    }
    
    .pagination > * {
        margin: 0 0.25rem;
    }
    
    /* เพิ่มขนาดอักษรให้กับปุ่มใน pagination */
    .pagination a, .pagination span {
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
    }
</style>
@endsection
