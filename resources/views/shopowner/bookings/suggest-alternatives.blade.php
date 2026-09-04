@extends('layouts.shopowner-layout')

@section('title', 'เสนอชุดทดแทน')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('shopowner.bookings.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fa fa-arrow-left mr-2"></i>กลับไปยังรายการจอง
            </a>
            <h2 class="text-2xl font-bold mt-2">เสนอชุดทดแทน</h2>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Original Outfit Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-lg font-semibold">ชุดที่ลูกค้าต้องการ (จำนวนไม่พอ)</h3>
            </div>
            <div class="p-4">
                <div class="flex mb-4">
                    @if($originalOutfit->image)
                        <img src="{{ asset($originalOutfit->image) }}" alt="{{ $originalOutfit->name }}" class="h-40 w-40 object-cover rounded mr-4">
                    @else
                        <div class="h-40 w-40 bg-gray-200 flex items-center justify-center rounded mr-4">
                            <i class="fa fa-image text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="text-xl font-semibold mb-2">{{ $originalOutfit->name }}</h4>
                        <p class="text-gray-600 mb-2">{{ Str::limit($originalOutfit->description, 100) }}</p>
                        <p class="font-medium mb-1">ขนาด: {{ $cartItem->size->size ?? 'ไม่ระบุ' }}</p>
                        <p class="font-medium mb-1">สี: {{ $cartItem->color->color ?? 'ไม่ระบุ' }}</p>
                        <p class="font-medium mb-1">จำนวนที่ต้องการ: <span class="text-red-600">{{ $orderDetail->quantity }} ชุด</span></p>
                        <p class="font-medium">ราคาต่อชุด: {{ number_format($originalOutfit->price, 2) }} ฿</p>
                        <p class="font-medium mt-2">วันที่จอง: <span id="reservation-date">{{ $orderDetail->reservation_date ? \Carbon\Carbon::parse($orderDetail->reservation_date)->format('d/m/Y') : 'ไม่ระบุ' }}</span></p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-semibold mb-2">หมวดหมู่:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($originalOutfit->categories as $category)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">{{ $category->category_name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customer Information Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-lg font-semibold">ข้อมูลลูกค้าและการจอง</h3>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">ชื่อ-นามสกุล:</p>
                    <p class="font-medium">{{ $booking->user->name ?? 'ไม่ระบุ' }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">อีเมล:</p>
                    <p class="font-medium">{{ $booking->user->email ?? 'ไม่ระบุ' }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">เบอร์โทรศัพท์:</p>
                    <p class="font-medium">{{ $booking->user->phone ?? 'ไม่ระบุ' }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">เลขที่การจอง:</p>
                    <p class="font-medium">{{ $booking->booking_id }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">วันที่สั่งซื้อ:</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($booking->purchase_date)->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alternative Outfits Section -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="bg-gray-50 px-4 py-3 border-b">
            <h3 class="text-lg font-semibold">ชุดทดแทนที่มีหมวดหมู่เดียวกัน ({{ $alternativeOutfits->count() }} ชุด)</h3>
        </div>
        
        <div class="flex space-x-4 p-4 text-sm">
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-green-500 inline-block mr-1"></span>
                <span>เพียงพอ</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-yellow-500 inline-block mr-1"></span>
                <span>ไม่เพียงพอ</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-red-500 inline-block mr-1"></span>
                <span>หมด/ไม่พบข้อมูล</span>
            </div>
        </div>
        
        @if($alternativeOutfits->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($alternativeOutfits as $outfit)
                    <div class="border rounded-lg overflow-hidden hover:shadow-md transition duration-300" id="outfit-card-{{ $outfit->outfit_id }}">
                        @if($outfit->image)
                            <img src="{{ asset($outfit->image) }}" alt="{{ $outfit->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fa fa-image text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h4 class="text-lg font-semibold mb-2">{{ $outfit->name }}</h4>
                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($outfit->description, 100) }}</p>
                            
                            <!-- Shop Information -->
                            <p class="text-sm text-blue-600 mb-3">
                                <i class="fa fa-store mr-1"></i> ร้าน: {{ $outfit->shop->shop_name ?? 'ไม่ระบุ' }}
                            </p>
                            
                            <div class="mb-3">
                                <!-- Size Selection -->
                                <div class="mb-2">
                                    <label for="size-{{ $outfit->outfit_id }}" class="block text-sm font-medium text-gray-700">ขนาด:</label>
                                    <select 
                                        id="size-{{ $outfit->outfit_id }}" 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md size-selector"
                                        data-outfit-id="{{ $outfit->outfit_id }}"
                                    >
                                        @foreach($outfit->sizeAndColors->pluck('size')->unique('size_id') as $size)
                                            <option 
                                                value="{{ $size->size_id }}" 
                                                {{ $size->size_id == $cartItem->size_id ? 'selected' : '' }}
                                            >
                                                {{ $size->size }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Color Selection -->
                                <div class="mb-2">
                                    <label for="color-{{ $outfit->outfit_id }}" class="block text-sm font-medium text-gray-700">สี:</label>
                                    <select 
                                        id="color-{{ $outfit->outfit_id }}" 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md color-selector"
                                        data-outfit-id="{{ $outfit->outfit_id }}"
                                    >
                                        @foreach($outfit->sizeAndColors->pluck('color')->unique('color_id') as $color)
                                            <option 
                                                value="{{ $color->color_id }}" 
                                                {{ $color->color_id == $cartItem->color_id ? 'selected' : '' }}
                                            >
                                                {{ $color->color }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Stock Information -->
                                <div id="stock-info-{{ $outfit->outfit_id }}" class="text-sm mb-2">
                                    <p>จำนวนคงเหลือ: <span id="stock-amount-{{ $outfit->outfit_id }}" class="font-medium">กำลังตรวจสอบ...</span></p>
                                </div>
                                
                                <!-- Hidden sizeDetail_id field -->
                                <input type="hidden" id="sizeDetail-{{ $outfit->outfit_id }}" value="">
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold">{{ number_format($outfit->price, 2) }} ฿</span>
                                
                                <form action="{{ route('shopowner.bookings.save-selection') }}" method="POST" id="selection-form-{{ $outfit->outfit_id }}" onsubmit="return validateSelection({{ $outfit->outfit_id }});">
                                    @csrf
                                    <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                                    <input type="hidden" name="orderDetail_id" value="{{ $orderDetail->orderDetail_id }}">
                                    <input type="hidden" name="outfit_id" value="{{ $outfit->outfit_id }}">
                                    <input type="hidden" name="sizeDetail_id" id="form-sizeDetail-{{ $outfit->outfit_id }}" value="">
                                    
                                    <button type="submit" class="bg-gray-400 text-white px-4 py-2 rounded-md" id="submit-btn-{{ $outfit->outfit_id }}" disabled>
                                        เลือกชุดนี้
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10">
                <i class="fa fa-search text-4xl text-gray-400 mb-4"></i>
                <p class="text-lg text-gray-600">ไม่พบชุดทดแทนที่มีหมวดหมู่เดียวกัน</p>
                <p class="text-sm text-gray-500 mt-1">ลองเพิ่มสินค้าใหม่หรือปรับสต็อกสินค้าของคุณ</p>
            </div>
        @endif
    </div>
</div>

<style>
    button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .stock-sufficient {
        color: #10B981; /* เขียว */
    }
    
    .stock-insufficient {
        color: #F59E0B; /* เหลือง */
    }
    
    .stock-empty {
        color: #EF4444; /* แดง */
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get reservation date from the original outfit
    const reservationDateStr = document.getElementById('reservation-date').textContent.trim();
    let reservationDate = '';
    
    if (reservationDateStr !== 'ไม่ระบุ') {
        // Convert date format from DD/MM/YYYY to YYYY-MM-DD for API calls
        const parts = reservationDateStr.split('/');
        reservationDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
    } else {
        // Use today's date as fallback
        const today = new Date();
        reservationDate = today.toISOString().split('T')[0];
    }
    
    // For each outfit, check stock for default size and color
    @foreach($alternativeOutfits as $outfit)
        updateStockInfo({{ $outfit->outfit_id }}, reservationDate);
    @endforeach
    
    // Add event listeners to size and color selectors
    document.querySelectorAll('.size-selector, .color-selector').forEach(selector => {
        selector.addEventListener('change', function() {
            const outfitId = this.dataset.outfitId;
            updateStockInfo(outfitId, reservationDate);
        });
    });
    
    // ดึงค่าจำนวนชุดที่ลูกค้าต้องการ
    const requiredQuantity = {{ $orderDetail->quantity }};
    
    // Function to update stock information
    function updateStockInfo(outfitId, date) {
        const sizeSelector = document.getElementById(`size-${outfitId}`);
        const colorSelector = document.getElementById(`color-${outfitId}`);
        const stockAmountElement = document.getElementById(`stock-amount-${outfitId}`);
        const sizeDetailInput = document.getElementById(`sizeDetail-${outfitId}`);
        const formSizeDetailInput = document.getElementById(`form-sizeDetail-${outfitId}`);
        const submitBtn = document.getElementById(`submit-btn-${outfitId}`);
        
        if (!sizeSelector || !colorSelector) return;
        
        const sizeId = sizeSelector.value;
        const colorId = colorSelector.value;
        
        // Disable the button while checking
        if (submitBtn) submitBtn.disabled = true;
        
        // Update stock display to show loading
        if (stockAmountElement) stockAmountElement.textContent = 'กำลังตรวจสอบ...';
        
        // Call the API to get sizeDetail_id and stock amount
        fetch(`/api/outfit-stock?outfit_id=${outfitId}&size_id=${sizeId}&color_id=${colorId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update sizeDetail_id in hidden inputs
                    if (sizeDetailInput) sizeDetailInput.value = data.sizeDetail_id;
                    if (formSizeDetailInput) formSizeDetailInput.value = data.sizeDetail_id;
                    
                    // Update stock display
                    if (stockAmountElement) {
                        // ลบคลาสสีทั้งหมดก่อน
                        stockAmountElement.classList.remove('text-green-600', 'text-yellow-600', 'text-red-600');
                        
                        // เช็คว่าจำนวนคงเหลือพอกับที่ลูกค้าต้องการหรือไม่
                        if (data.stockAmount >= requiredQuantity) {
                            stockAmountElement.textContent = `${data.stockAmount} ชุด (เพียงพอ)`;
                            stockAmountElement.classList.add('text-green-600');
                            
                            // เปิดใช้งานปุ่ม submit
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('bg-gray-400');
                                submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                            }
                        } else if (data.stockAmount > 0) {
                            stockAmountElement.textContent = `${data.stockAmount} ชุด (ไม่พอ ต้องการ ${requiredQuantity} ชุด)`;
                            stockAmountElement.classList.add('text-yellow-600');
                            
                            // ปิดใช้งานปุ่ม submit
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                                submitBtn.classList.add('bg-gray-400');
                            }
                        } else {
                            stockAmountElement.textContent = 'หมด';
                            stockAmountElement.classList.add('text-red-600');
                            
                            // ปิดใช้งานปุ่ม submit
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                                submitBtn.classList.add('bg-gray-400');
                            }
                        }
                    }
                } else {
                    // กรณีเกิดข้อผิดพลาด
                    if (stockAmountElement) {
                        stockAmountElement.classList.remove('text-green-600', 'text-yellow-600', 'text-red-600');
                        stockAmountElement.textContent = 'ไม่พบข้อมูล';
                        stockAmountElement.classList.add('text-red-600');
                    }
                    
                    // ปิดใช้งานปุ่ม submit
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        submitBtn.classList.add('bg-gray-400');
                    }
                }
            })
            .catch(error => {
                // กรณีเกิดข้อผิดพลาดในการเรียก API
                console.error('Error checking stock:', error);
                if (stockAmountElement) {
                    stockAmountElement.classList.remove('text-green-600', 'text-yellow-600', 'text-red-600');
                    stockAmountElement.textContent = 'เกิดข้อผิดพลาด';
                    stockAmountElement.classList.add('text-red-600');
                }
                
                // ปิดใช้งานปุ่ม submit
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    submitBtn.classList.add('bg-gray-400');
                }
            });
    }
    
    // Validation function before form submission
    window.validateSelection = function(outfitId) {
        const sizeDetailInput = document.getElementById(`form-sizeDetail-${outfitId}`);
        const stockAmountElement = document.getElementById(`stock-amount-${outfitId}`);
        
        if (!sizeDetailInput || !sizeDetailInput.value) {
            alert('กรุณารอให้ระบบตรวจสอบจำนวนสินค้าคงเหลือให้เสร็จสิ้นก่อน');
            return false;
        }
        
        if (stockAmountElement && stockAmountElement.textContent === 'หมด') {
            alert('ขออภัย สินค้านี้หมด ไม่สามารถเลือกได้');
            return false;
        }
        
        // Check if stock is still being checked
        if (stockAmountElement && stockAmountElement.textContent === 'กำลังตรวจสอบ...') {
            alert('กรุณารอให้ระบบตรวจสอบจำนวนสินค้าคงเหลือให้เสร็จสิ้นก่อน');
            return false;
        }
        
        return true;
    };
});
</script
@endsection
