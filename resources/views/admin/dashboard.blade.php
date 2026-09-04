@extends('layouts.admin-layout')

@section('title', 'แดชบอร์ดผู้ดูแลระบบ')

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-tachometer-alt mr-2 text-[#8B9DF9]"></i>แดชบอร์ด
        </h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Users Card -->
        <div class="card bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">ผู้ใช้ทั้งหมด</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\User::count() }}</p>
                </div>
            </div>
            <div class="bg-blue-50 px-6 py-2">
                <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> ดูผู้ใช้ทั้งหมด
                </a>
            </div>
        </div>

        <!-- Shops Card -->
        <div class="card bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 flex items-center">
                <div class="rounded-full bg-purple-100 p-3 mr-4">
                    <i class="fas fa-store text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">ร้านค้าทั้งหมด</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Shop::count() }}</p>
                </div>
            </div>
            <div class="bg-purple-50 px-6 py-2">
                <a href="{{ route('admin.shops.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> ดูร้านค้าทั้งหมด
                </a>
            </div>
        </div>

        <!-- Outfits Card -->
        <div class="card bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 flex items-center">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                    <i class="fas fa-tshirt text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">ชุดไทยทั้งหมด</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\ThaiOutfit::count() }}</p>
                </div>
            </div>
            <div class="bg-green-50 px-6 py-2">
                <a href="{{ route('admin.outfits.adminindex') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> ดูชุดไทยทั้งหมด
                </a>
            </div>
        </div>

        <!-- Bookings Card -->
        <div class="card bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 flex items-center">
                <div class="rounded-full bg-yellow-100 p-3 mr-4">
                    <i class="fas fa-calendar-check text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">การจองทั้งหมด</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Booking::count() }}</p>
                </div>
            </div>
            <div class="bg-yellow-50 px-6 py-2">
                <a href="{{ route('admin.booking.index') }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> ดูการจองทั้งหมด
                </a>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="card bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 flex items-center">
                <div class="rounded-full bg-red-100 p-3 mr-4">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">รายงานปัญหาทั้งหมด</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Issue::count() }}</p>
                </div>
            </div>
            <div class="bg-red-50 px-6 py-2">
                <a href="{{ route('admin.issue.show') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> ดูรายงานปัญหาทั้งหมด
                </a>
            </div>
        </div>

        <!-- Categories Card (เพิ่มใหม่) -->
        <div class="card bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 flex items-center">
                <div class="rounded-full bg-indigo-100 p-3 mr-4">
                    <i class="fas fa-tags text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">หมวดหมู่ทั้งหมด</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ \App\Models\OutfitCategory::count() }}</p>
                </div>
            </div>
            <div class="bg-indigo-50 px-6 py-2">
                <a href="{{ route('admin.categories.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> ดูหมวดหมู่ทั้งหมด
                </a>
            </div>
        </div>
    </div>

    <!-- ปรับโครงสร้างส่วนนี้ให้ "ภาพรวมระบบ" และ "การกระจายตัวของหมวดหมู่" อยู่ในระดับเดียวกัน -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- ภาพรวมระบบ -->
        <div class="card bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="card-header">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-chart-line mr-2 text-[#8B9DF9]"></i>ภาพรวมระบบ
                </h2>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 gap-6 p-4"> <!-- เพิ่ม padding และปรับ gap -->
                    <!-- Active vs Inactive Users -->
                    <div>   
                        <h3 class="text-lg font-semibold mb-4">สถานะผู้ใช้</h3>
                        <div class="flex items-center justify-between bg-gray-100 p-4 rounded-lg">
                            <div class="text-center flex-1"> <!-- เพิ่ม flex-1 เพื่อให้ขยายเต็มพื้นที่ -->
                                <div class="text-2xl font-bold text-green-600">{{ \App\Models\User::where('status', 'active')->count() }}</div>
                                <div class="text-sm text-gray-600">ผู้ใช้ที่ใช้งานอยู่</div>
                            </div>
                            <div class="border-r border-gray-300 h-12 mx-4"></div> <!-- เพิ่มเส้นแบ่งตรงกลาง -->
                            <div class="text-center flex-1"> <!-- เพิ่ม flex-1 เพื่อให้ขยายเต็มพื้นที่ -->
                                <div class="text-2xl font-bold text-red-600">{{ \App\Models\User::where('status', 'inactive')->count() }}</div>
                                <div class="text-sm text-gray-600">ผู้ใช้ที่ไม่ได้ใช้งาน</div>
                            </div>
                        </div>
                    </div>

                    <!-- Active vs Inactive Shops -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">สถานะร้านค้า</h3>
                        <div class="flex items-center justify-between bg-gray-100 p-4 rounded-lg">
                            <div class="text-center flex-1"> <!-- เพิ่ม flex-1 เพื่อให้ขยายเต็มพื้นที่ -->
                                <div class="text-2xl font-bold text-green-600">{{ \App\Models\Shop::where('status', 'active')->count() }}</div>
                                <div class="text-sm text-gray-600">ร้านค้าที่เปิดให้บริการ</div>
                            </div>
                            <div class="border-r border-gray-300 h-12 mx-4"></div> <!-- เพิ่มเส้นแบ่งตรงกลาง -->
                            <div class="text-center flex-1"> <!-- เพิ่ม flex-1 เพื่อให้ขยายเต็มพื้นที่ -->
                                <div class="text-2xl font-bold text-red-600">{{ \App\Models\Shop::where('status', 'inactive')->count() }}</div>
                                <div class="text-sm text-gray-600">ร้านค้าที่ปิดให้บริการ</div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Status -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">สถานะการจอง</h3>
                        <div class="grid grid-cols-3 gap-2 bg-gray-100 p-4 rounded-lg">
                            <div class="text-center">
                                <div class="text-xl font-bold text-yellow-600">{{ \App\Models\Booking::whereIn('status', ['pending', 'partial paid'])->count() }}</div>
                                <div class="text-xs text-gray-600">รอดำเนินการ</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-green-600">{{ \App\Models\Booking::where('status', 'confirmed')->count() }}</div>
                                <div class="text-xs text-gray-600">ยืนยันแล้ว</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-red-600">{{ \App\Models\Booking::where('status', 'cancelled')->count() }}</div>
                                <div class="text-xs text-gray-600">ยกเลิกแล้ว</div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Status -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">สถานะรายงานปัญหา</h3>
                        <div class="grid grid-cols-3 gap-2 bg-gray-100 p-4 rounded-lg">
                            <div class="text-center">
                                <div class="text-xl font-bold text-red-600">{{ \App\Models\Issue::where('status', 'reported')->count() }}</div>
                                <div class="text-xs text-gray-600">รายงานแล้ว</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-yellow-600">{{ \App\Models\Issue::where('status', 'in_progress')->count() }}</div>
                                <div class="text-xs text-gray-600">กำลังดำเนินการ</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-green-600">{{ \App\Models\Issue::where('status', 'fixed')->count() }}</div>
                                <div class="text-xs text-gray-600">แก้ไขแล้ว</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- การกระจายตัวของหมวดหมู่ (ย้ายมาอยู่ข้างขวา) -->
        <div class="card bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="card-header">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-tags mr-2 text-[#8B9DF9]"></i>การกระจายตัวของหมวดหมู่
                </h2>
            </div>
            <div class="card-body">
                <div class="p-4">
                    @php
                        // ใช้ OutfitCategory แทน Category และใช้ความสัมพันธ์ outfits() ที่มีอยู่ในโมเดล
                        $categories = \App\Models\OutfitCategory::withCount('outfits')->orderByDesc('outfits_count')->take(10)->get();
                        $totalOutfits = \App\Models\ThaiOutfit::count();
                    @endphp
                    
                    @if($categories->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-info-circle text-2xl mb-2"></i>
                            <p>ไม่พบข้อมูลหมวดหมู่</p>
                        </div>
                    @else
                        <!-- เพิ่มช่องว่างด้านบนตรงนี้ -->
                        <div class="mt-4"></div>
                        
                        @foreach($categories as $category)
                            <div class="mb-6"> <!-- เพิ่ม margin-bottom จาก mb-4 เป็น mb-6 -->
                                <div class="flex justify-between mb-2"> <!-- เพิ่ม margin-bottom จาก mb-1 เป็น mb-2 -->
                                    <span class="text-sm font-medium text-gray-700">{{ $category->category_name }}</span>
                                    <span class="text-sm font-medium text-gray-700">{{ $category->outfits_count }} ชุด</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3"> <!-- เพิ่มความสูงจาก h-2.5 เป็น h-3 -->
                                    @php
                                        $percentage = $totalOutfits > 0 
                                            ? ($category->outfits_count / $totalOutfits) * 100 
                                            : 0;
                                    @endphp
                                    <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $percentage }}%"></div> <!-- ปรับความสูงให้ตรงกัน -->
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="mt-8 text-center"> <!-- เพิ่ม margin-top จาก mt-6 เป็น mt-8 -->
                            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-list mr-2"></i> ดูหมวดหมู่ทั้งหมด
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

