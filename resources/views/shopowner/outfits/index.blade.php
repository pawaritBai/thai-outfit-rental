<UPDATED_CODE>@extends('layouts.shopowner-layout')

@section('title', 'จัดการชุด')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">จัดการชุด</h2>
        <a href="{{ route('shopowner.outfits.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fa fa-plus mr-2"></i> เพิ่มชุดใหม่
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search and Filter Card -->
     
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">ค้นหาและกรอง</h3>
            <button type="button" id="toggle-filters" class="text-blue-600 hover:text-blue-800">
                <i class="fa fa-filter mr-1"></i> แสดง/ซ่อน ตัวกรอง
            </button>
        </div>

        <form action="{{ route('shopowner.outfits.index') }}" method="GET">
            <!-- Search Bar -->
            <div class="mb-4">
                <div class="flex">
                    <input type="text" name="search" placeholder="ค้นหาตามชื่อชุด..." value="{{ request('search') }}"
                        class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Advanced Filters (Initially Hidden) -->
            <div id="advanced-filters" class="{{ request()->anyFilled(['categories', 'sizes', 'colors', 'status']) ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">ทั้งหมด</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>พร้อมใช้งาน</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่พร้อมใช้งาน</option>
                        </select>
                    </div>
                    
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่</label>
                        <select name="categories[]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">ทั้งหมด</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}" {{ in_array($category->category_id, (array)request('categories')) ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Size Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ขนาด</label>
                        <select name="sizes[]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">ทั้งหมด</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->size_id }}" {{ in_array($size->size_id, (array)request('sizes')) ? 'selected' : '' }}>
                                    {{ $size->size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Color Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สี</label>
                        <select name="colors[]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">ทั้งหมด</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->color_id }}" {{ in_array($color->color_id, (array)request('colors')) ? 'selected' : '' }}>
                                    {{ $color->color }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!-- Filter Buttons -->
            <div class="flex items-center justify-between">
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fa fa-filter mr-2"></i> กรอง
                    </button>
                    <a href="{{ route('shopowner.outfits.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        <i class="fa fa-times mr-2"></i> ล้างตัวกรอง
                    </a>
                </div>
                
                <div>
                    <label class="text-sm text-gray-600 mr-2">เรียงตาม:</label>
                    <select name="orderBy" onchange="this.form.submit()" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="name" {{ request('orderBy') == 'name' ? 'selected' : '' }}>ชื่อชุด</option>
                        <option value="price" {{ request('orderBy') == 'price' ? 'selected' : '' }}>ราคา</option>
                        <option value="created_at" {{ request('orderBy') == 'created_at' ? 'selected' : '' }}>วันที่เพิ่ม</option>
                    </select>
                    
                    <select name="direction" onchange="this.form.submit()" 
                        class="ml-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>น้อยไปมาก</option>
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>มากไปน้อย</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        @if(count($outfits) > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รูปภาพ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อชุด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคา</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">มัดจำ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ค่าปรับ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">คงเหลือ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รายละเอียด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่เพิ่ม</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($outfits as $outfit)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $outfit->outfit_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($outfit->image && file_exists(public_path($outfit->image)))
                                <img src="{{ asset($outfit->image) }}" alt="{{ $outfit->name }}" class="h-16 w-16 object-cover rounded">
                            @else
                                <div class="h-16 w-16 bg-gray-200 flex items-center justify-center rounded">
                                    <i class="fa fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $outfit->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($outfit->price, 2) }} บาท</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($outfit->depositfee, 2) }} บาท</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($outfit->penaltyfee, 2) }} บาท</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $outfit->sizeAndColors->sum('amount') }}</div>
                        </td>
                        <td class="p-3 relative">
                            <button type="button" class="text-blue-600 hover:text-blue-800 relative" 
                                    id="btn-{{ $outfit->outfit_id }}"
                                    onclick="toggleVariants('variants-{{ $outfit->outfit_id }}', 'btn-{{ $outfit->outfit_id }}')">
                                แสดงรายละเอียด <i class="fa fa-chevron-down"></i>
                            </button>
                            <div id="variants-{{ $outfit->outfit_id }}" class="hidden fixed z-50 bg-white rounded-lg shadow-lg border p-4" style="min-width: 500px; max-width: 800px;">
                                <div class="flex justify-between mb-2">
                                    <h3 class="font-bold">รายละเอียดชุด: {{ $outfit->name }}</h3>
                                    <button onclick="toggleVariants('variants-{{ $outfit->outfit_id }}')" class="text-gray-500 hover:text-gray-700">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                                
                                <!-- แสดงประเภทชุด -->
                                <div class="mb-3 pb-3 border-b">
                                    <p class="font-medium text-gray-700">ประเภท: 
                                        @if(isset($outfit->categories) && $outfit->categories->count() > 0)
                                            <span class="inline-flex flex-wrap gap-1">
                                                @foreach($outfit->categories as $category)
                                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                                        {{ $category->category_name }}
                                                    </span>
                                                @endforeach
                                            </span>
                                        @else
                                            <span class="text-gray-500">ไม่ระบุประเภท</span>
                                        @endif
                                    </p>
                                </div>
                                
                                <!-- รายละเอียดขนาดและสี -->
                                <h4 class="font-medium mb-2">ขนาดและสี:</h4>
                                @if(isset($outfit->sizeAndColors) && $outfit->sizeAndColors->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($outfit->sizeAndColors as $variant)
                                            <div class="bg-gray-50 p-3 rounded border flex items-center">
                                                <div class="flex-1 whitespace-nowrap">
                                                    <span class="font-medium">{{ $variant->size->size }}</span> - 
                                                    <span class="font-medium">{{ $variant->color->color }}</span>
                                                </div>
                                                <div class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ $variant->amount }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-gray-500">ไม่มีข้อมูลขนาดและสี</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($outfit->status == 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">พร้อมใช้งาน</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">ไม่พร้อมใช้งาน</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if(is_string($outfit->created_at))
                                    {{ \Carbon\Carbon::parse($outfit->created_at)->format('d/m/Y H:i') }}
                                @else
                                    {{ $outfit->created_at->format('d/m/Y H:i') }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('shopowner.outfits.edit', $outfit->outfit_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fa fa-edit"></i> แก้ไข
                                </a>
                                <form action="{{ route('shopowner.outfits.destroy', $outfit->outfit_id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบชุดนี้?')">
                                        <i class="fa fa-trash"></i> ลบ
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 flex justify-between items-center border-t">
                {{ $outfits->links() }}
                <a href="{{ route('shopowner.outfits.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fa fa-plus mr-2"></i> เพิ่มชุดใหม่
                </a>
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                <p>ไม่พบข้อมูลชุดตามเงื่อนไขที่ค้นหา</p>
                <div class="mt-4">
                    @if(request()->anyFilled(['search', 'categories', 'sizes', 'colors', 'status']))
                        <a href="{{ route('shopowner.outfits.index') }}" class="inline-block px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 mr-2">
                            <i class="fa fa-times mr-2"></i> ล้างตัวกรอง
                        </a>
                    @endif
                    <a href="{{ route('shopowner.outfits.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fa fa-plus mr-2"></i> เพิ่มชุดใหม่
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Toggle variants function
    function toggleVariants(id, buttonId) {
        const variantsElement = document.getElementById(id);
        const button = document.getElementById(buttonId);
        
        // ซ่อนทุกรายการที่อาจเปิดอยู่
        document.querySelectorAll('[id^="variants-"]').forEach(el => {
            if (el.id !== id) {
                el.classList.add('hidden');
            }
        });
        
        // สลับการแสดงผลของรายการที่คลิก
        variantsElement.classList.toggle('hidden');
        
        // ถ้ากำลังแสดงผล ให้ตรวจสอบตำแหน่งเพื่อไม่ให้ล้นหน้าจอ
        if (!variantsElement.classList.contains('hidden')) {
            // รับตำแหน่งของปุ่มที่คลิก
            const buttonRect = button.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            const viewportWidth = window.innerWidth;
            
            // รีเซ็ตตำแหน่งก่อน
            variantsElement.style.left = '-150px';
            variantsElement.style.top = 'auto';
            variantsElement.style.bottom = 'auto';
            
            // คำนวณว่า popup จะล้นด้านล่างของหน้าจอหรือไม่
            const popupHeight = variantsElement.offsetHeight;
            const spaceBelow = viewportHeight - buttonRect.bottom;
            
            // ถ้าพื้นที่ด้านล่างไม่พอ ให้แสดง popup ด้านบนแทน
            if (spaceBelow < popupHeight && buttonRect.top > popupHeight) {
                variantsElement.style.bottom = '100%';
                variantsElement.style.top = 'auto';
                variantsElement.style.marginBottom = '10px';
            } else {
                variantsElement.style.top = '100%';
                variantsElement.style.bottom = 'auto';
                variantsElement.style.marginTop = '10px';
            }
            
            // ตรวจสอบด้านข้างด้วย
            const popupRect = variantsElement.getBoundingClientRect();
            if (popupRect.right > viewportWidth) {
                variantsElement.style.left = 'auto';
                variantsElement.style.right = '0';
            }
        }
    }

    // เพิ่ม event listener เพื่อปิดรายละเอียดเมื่อคลิกที่อื่น
    document.addEventListener('click', function(event) {
        const isClickInsideVariants = event.target.closest('[id^="variants-"]');
        const isClickOnButton = event.target.closest('button[onclick^="toggleVariants"]');
        
        if (!isClickInsideVariants && !isClickOnButton) {
            document.querySelectorAll('[id^="variants-"]').forEach(el => {
                el.classList.add('hidden');
            });
        }
    });

    // Toggle advanced filters
    document.addEventListener('DOMContentLoaded', function() {
        const toggleFiltersBtn = document.getElementById('toggle-filters');
        const advancedFilters = document.getElementById('advanced-filters');
        
        if (toggleFiltersBtn && advancedFilters) {
            toggleFiltersBtn.addEventListener('click', function() {
                advancedFilters.classList.toggle('hidden');
            });
        }
    });
</script>

<style>
    .table-container {
        position: relative;
    }
    
    [id^="variants-"] {
        position: absolute;
        z-index: 50;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        padding: 1rem;
        min-width: 500px;
        max-width: 800px;
        max-height: 80vh; /* จำกัดความสูงสูงสุด */
        overflow-y: auto; /* เพิ่ม scroll เมื่อเนื้อหาเยอะ */
    }
    
    /* เพิ่ม transition เพื่อให้การแสดง/ซ่อนดูนุ่มนวลขึ้น */
    [id^="variants-"] {
        transition: opacity 0.2s ease-in-out;
    }
    
    [id^="variants-"].hidden {
        opacity: 0;
        pointer-events: none;
    }
</style>
