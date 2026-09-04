@extends('layouts.main')

@section('title', 'ประวัติการสั่งของ')

@section('content')
{{-- Import necessary classes at the top level of the template --}}
@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\SelectOutfitDetail;
    
    // ตรวจสอบว่ามีรายการที่มีการเสนอชุดทดแทนใหม่รอการตอบรับหรือไม่
    $pendingSuggestionsCount = 0;
    foreach ($bookings as $booking) {
        $suggestions = SelectOutfitDetail::where('booking_id', $booking->booking_id)
            ->where('customer_id', Auth::id())
            ->where('status', 'Pending Selection')
            ->count();
        
        if ($suggestions > 0) {
            $pendingSuggestionsCount++;
        }
    }
    
    // Get today's date
    $today = \Carbon\Carbon::now()->format('Y-m-d');
    
    // Array to store order details that need to be returned
    $returnOrderIds = [];
    
    // Check each booking for items that need to be returned
    foreach ($bookings as $booking) {
        foreach ($booking->orderDetails as $orderDetail) {
            if ($orderDetail->reservation_date && $orderDetail->total !== null) {
                // Add 1 day to reservation_date for return date
                $returnDate = \Carbon\Carbon::parse($orderDetail->reservation_date)->addDay()->format('Y-m-d');
                if ($returnDate <= $today) {
                    $returnOrderIds[] = $booking->booking_id;
                    break; // One match is enough to highlight the booking
                }
            }
        }
    }
@endphp

<div class="container mx-auto my-5 flex flex-col md:flex-row gap-5 min-h-screen">
    <!-- Sidebar -->
    <div class="w-full md:w-1/4 bg-white rounded-lg shadow sticky top-5 h-fit">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Account Settings</h3>
        </div>
        <ul class="p-4 space-y-2 text-sm">
            <!-- Profile -->
            <a href="{{ route('profile.index') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                <i class="fas fa-user mr-3 w-4 text-center"></i> Profile
            </a>

            <!-- Address -->
            <a href="{{ route('profile.customer.address.index') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                <i class="fas fa-map-marker-alt mr-3 w-4 text-center"></i> Address
            </a>

            <!-- Payment -->
            <a href="{{ route('payment.index') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                <i class="fas fa-credit-card mr-3 w-4 text-center"></i> Payment
            </a>

            <!-- History -->
            <a href="{{ route('profile.customer.orderHistory') }}" class="flex items-center py-2 px-3 text-purple-600 bg-purple-50 rounded-md transition-colors cursor-pointer font-semibold">
                <i class="fas fa-history mr-3 w-4 text-center"></i> History
                @if($pendingSuggestionsCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 flex items-center justify-center min-w-[20px]">
                        {{ $pendingSuggestionsCount }}
                    </span>
                @endif
            </a>

            <!-- History -->
            <a href="{{ route('profile.customer.issue') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                <i class="fas fa-flag mr-3 w-4 text-center"></i> Report Issue
            </a>
        </ul>
    </div>

    {{-- Main Content (Booking List) --}}
    <div class="w-full md:flex-1">
        <h2 class="text-2xl font-bold mb-5 text-gray-800">ประวัติการสั่งของ</h2>
        
        <!-- Global notification and filter for new suggestions -->
        @if($pendingSuggestionsCount > 0)
            <div id="global-notification" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-5 rounded-md shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-yellow-700 font-medium">
                                คุณมี {{ $pendingSuggestionsCount }} รายการที่มีชุดทดแทนใหม่รอการตอบรับ
                            </p>
                            <p class="text-yellow-600 text-sm mt-1">
                                กรุณาตรวจสอบและตอบรับหรือปฏิเสธชุดทดแทนเพื่อดำเนินการต่อ
                            </p>
                        </div>
                    </div>
                    <div>
                        <button id="show-all-orders" class="text-purple-600 hover:text-purple-800 mr-2 hidden">
                            แสดงทั้งหมด
                        </button>
                        <button id="filter-suggestions" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                            แสดงเฉพาะรายการที่มีชุดทดแทนใหม่
                        </button>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Return alerts notification -->
        @if(count($returnOrderIds) > 0)
            <div id="return-notification" class="bg-red-50 border-l-4 border-red-400 p-4 mb-5 rounded-md shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-red-700 font-medium">
                                คุณมี {{ count($returnOrderIds) }} รายการที่ต้องคืนชุดแล้ว
                            </p>
                            <p class="text-red-600 text-sm mt-1">
                                กรุณาคืนชุดโดยเร็วที่สุด เพื่อหลีกเลี่ยงค่าปรับเพิ่มเติม
                            </p>
                        </div>
                    </div>
                    <div>
                        <button id="show-all-returns" class="text-purple-600 hover:text-purple-800 mr-2 hidden">
                            แสดงทั้งหมด
                        </button>
                        <button id="filter-returns" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                            แสดงเฉพาะรายการที่ต้องคืนชุด
                        </button>
                    </div>
                </div>
            </div>
        @endif
        
        <div id="orders-container" class="space-y-4">
            @forelse ($bookings as $booking)
                @php
                    // Check if there are outfit suggestions for this booking
                    $suggestions = SelectOutfitDetail::where('booking_id', $booking->booking_id)
                        ->where('customer_id', Auth::id())
                        ->get();
                    
                    // Check if there are pending suggestions
                    $hasPendingSuggestions = $suggestions->where('status', 'Pending Selection')->count() > 0;
                    
                    // Check if status is partial paid
                    $isPartialPaid = $booking->status == 'partial paid';
                    
                    // Determine if we should show notification
                    $showNotification = $hasPendingSuggestions;
                @endphp
                
                <div class="order-item relative {{ $showNotification ? 'has-suggestions' : '' }} {{ in_array($booking->booking_id, $returnOrderIds) ? 'return-alert' : '' }}">
                    <!-- Notification badge for orders with pending suggestions -->
                    @if($showNotification)
                        <div class="absolute -top-2 -right-2 z-10">
                            <div class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse">
                                ชุดทดแทนใหม่
                            </div>
                        </div>
                    @endif
                    
                    <!-- Add return alert badge -->
                    @if(in_array($booking->booking_id, $returnOrderIds))
                        <div class="absolute -top-2 {{ $showNotification ? '-left-2' : '-right-2' }} z-10">
                            <div class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse">
                                ต้องคืนชุดแล้ว!
                            </div>
                        </div>
                    @endif
                    
                    <a href="{{ route('profile.customer.orderDetail', ['bookingId' => $booking->booking_id]) }}"
                       class="block border border-gray-200 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow bg-white">
                        <div class="flex items-center justify-between mb-4">
                            <!-- ร้านค้า -->
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-store text-purple-600"></i>
                                <div>
                                    <strong class="text-gray-700">ร้านค้า:</strong> 
                                    <span class="text-gray-900 ml-1">
                                        {{ optional(optional(optional(optional($booking->orderDetails->first())->cartItem)->thaioutfit_sizeandcolor)->outfit)->shop->shop_name ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- สถานะ -->
                            <div class="status">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                    {{ $booking->status == 'confirmed' ? 'bg-green-100 text-green-600' : 
                                       ($booking->status == 'pending' ? 'bg-orange-100 text-orange-600' : 
                                       ($booking->status == 'partial paid' ? 'bg-blue-100 text-blue-600' : 
                                       'bg-red-100 text-red-600')) }}">
                                    {{ $booking->status }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <!-- รูปสินค้า -->
                            <div class="w-24 h-24 mr-4">
                                <img src="{{ asset(optional(optional(optional($booking->orderDetails->first())->cartItem)->thaioutfit_sizeandcolor)->outfit->image ?? 'images/default.png') }}"
                                     alt="Product Image" class="w-full h-full object-cover rounded-md">
                            </div>

                            <!-- ชื่อสินค้า -->
                            <div class="flex-1">
                                <div class="text-lg font-semibold text-gray-800">
                                    {{ optional(optional(optional($booking->orderDetails->first())->cartItem)->thaioutfit_sizeandcolor)->outfit->name ?? '-' }}
                                </div>
                                <div class="text-gray-500 text-sm mt-1">
                                    {{ optional(optional(optional($booking->orderDetails->first())->cartItem)->thaioutfit_sizeandcolor)->outfit->description ?? 'ไม่มีรายละเอียด' }}
                                </div>
                                
                                <!-- Add notification for orders with pending suggestions -->
                                @if($showNotification)
                                    <div class="mt-2 bg-yellow-50 border-l-4 border-yellow-400 p-2 text-sm">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                                            <p class="text-yellow-700">มีชุดทดแทนใหม่ที่รอการตอบรับ</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <!-- บริการ -->
                            @if ($booking->selectService) 
                                @foreach ($booking->selectService as $service)
                                    <p class="text-sm text-gray-700 flex items-center space-x-2">
                                        @if ($service->service_type == 'photographer')
                                            <i class="fas fa-camera text-purple-600"></i>
                                            <strong>ถ่ายรูป:</strong>
                                            <span>{{ $service->customer_count }} คน</span>
                                        @else
                                            <i class="fas fa-paint-brush text-purple-600"></i>
                                            <strong>แต่งหน้า:</strong>
                                            <span>{{ $service->customer_count }} คน</span>
                                        @endif
                                    </p>
                                @endforeach
                            @endif
                        </div>

                        <!-- ราคารวม -->
                        <div class="mt-4 text-lg font-semibold text-purple-600 flex items-center space-x-2">
                            <i class="fas fa-wallet"></i>
                            <strong>ราคารวม:</strong>
                            <span>฿{{ number_format($booking->total_price, 2) }}</span>
                        </div>
                    </a>
                </div>
            @empty
                <p class="text-gray-500 text-center py-5">คุณยังไม่มีประวัติการสั่งของ</p>
            @endforelse
        </div>
    </div>
</div>

<style>
    .order-item.return-alert {
        border: 2px solid #f56565 !important; /* Red border */
        box-shadow: 0 0 15px rgba(245, 101, 101, 0.4) !important;
    }
    
    .order-item.return-alert:hover {
        box-shadow: 0 0 20px rgba(245, 101, 101, 0.6) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterSuggestionsBtn = document.getElementById('filter-suggestions');
        const showAllOrdersBtn = document.getElementById('show-all-orders');
        const filterReturnsBtn = document.getElementById('filter-returns');
        const showAllReturnsBtn = document.getElementById('show-all-returns');
        const ordersContainer = document.getElementById('orders-container');
        const orderItems = document.querySelectorAll('.order-item');
        
        // Check if we have items with suggestions/return alerts
        const hasSuggestionItems = document.querySelectorAll('.order-item.has-suggestions').length > 0;
        const hasReturnItems = document.querySelectorAll('.order-item.return-alert').length > 0;
        
        // Function to filter orders and show only those with suggestions
        function filterSuggestions() {
            let hasVisibleOrders = false;
            
            orderItems.forEach(order => {
                if (order.classList.contains('has-suggestions')) {
                    order.style.display = 'block';
                    hasVisibleOrders = true;
                } else {
                    order.style.display = 'none';
                }
            });
            
            // Update button visibility
            if (filterSuggestionsBtn) filterSuggestionsBtn.classList.add('hidden');
            if (showAllOrdersBtn) showAllOrdersBtn.classList.remove('hidden');
            
            // Reset other filter button states
            if (filterReturnsBtn) filterReturnsBtn.classList.remove('hidden');
            if (showAllReturnsBtn) showAllReturnsBtn.classList.add('hidden');
            
            // Add a message if no suggestions are found after filtering
            const noSuggestionsMsg = document.querySelector('.no-suggestions-msg');
            if (!hasVisibleOrders) {
                if (!noSuggestionsMsg) {
                    const msgElement = document.createElement('p');
                    msgElement.className = 'text-gray-500 text-center py-5 no-suggestions-msg';
                    msgElement.textContent = 'ไม่มีรายการที่มีชุดทดแทนใหม่รอการตอบรับ';
                    ordersContainer.appendChild(msgElement);
                }
            } else {
                if (noSuggestionsMsg) {
                    noSuggestionsMsg.remove();
                }
            }
        }
        
        // Function to filter orders and show only those that need to be returned
        function filterReturns() {
            let hasVisibleOrders = false;
            
            orderItems.forEach(order => {
                if (order.classList.contains('return-alert')) {
                    order.style.display = 'block';
                    hasVisibleOrders = true;
                } else {
                    order.style.display = 'none';
                }
            });
            
            // Update button visibility
            if (filterReturnsBtn) filterReturnsBtn.classList.add('hidden');
            if (showAllReturnsBtn) showAllReturnsBtn.classList.remove('hidden');
            
            // Reset other filter button states
            if (filterSuggestionsBtn) filterSuggestionsBtn.classList.remove('hidden');
            if (showAllOrdersBtn) showAllOrdersBtn.classList.add('hidden');
            
            // Add a message if no returns are found after filtering
            const noReturnsMsg = document.querySelector('.no-returns-msg');
            if (!hasVisibleOrders) {
                if (!noReturnsMsg) {
                    const msgElement = document.createElement('p');
                    msgElement.className = 'text-gray-500 text-center py-5 no-returns-msg';
                    msgElement.textContent = 'ไม่มีรายการที่ต้องคืนชุด';
                    ordersContainer.appendChild(msgElement);
                }
            } else {
                if (noReturnsMsg) {
                    noReturnsMsg.remove();
                }
            }
        }
        
        // Function to show all orders
        function showAllOrders() {
            orderItems.forEach(order => {
                order.style.display = 'block';
            });
            
            // Update all button visibility
            if (filterSuggestionsBtn) filterSuggestionsBtn.classList.remove('hidden');
            if (showAllOrdersBtn) showAllOrdersBtn.classList.add('hidden');
            if (filterReturnsBtn) filterReturnsBtn.classList.remove('hidden');
            if (showAllReturnsBtn) showAllReturnsBtn.classList.add('hidden');
            
            // Remove all filter messages
            const noSuggestionsMsg = document.querySelector('.no-suggestions-msg');
            if (noSuggestionsMsg) {
                noSuggestionsMsg.remove();
            }
            
            const noReturnsMsg = document.querySelector('.no-returns-msg');
            if (noReturnsMsg) {
                noReturnsMsg.remove();
            }
        }
        
        // Add event listeners to buttons
        if (filterSuggestionsBtn) {
            filterSuggestionsBtn.addEventListener('click', filterSuggestions);
        }
        
        if (showAllOrdersBtn) {
            showAllOrdersBtn.addEventListener('click', showAllOrders);
        }
        
        if (filterReturnsBtn) {
            filterReturnsBtn.addEventListener('click', filterReturns);
        }
        
        if (showAllReturnsBtn) {
            showAllReturnsBtn.addEventListener('click', showAllOrders);
        }
        
        // Hide filter buttons if no items in that category
        if (!hasSuggestionItems && filterSuggestionsBtn) {
            filterSuggestionsBtn.classList.add('hidden');
        }
        
        if (!hasReturnItems && filterReturnsBtn) {
            filterReturnsBtn.classList.add('hidden');
        }
    });
</script>
@endsection
