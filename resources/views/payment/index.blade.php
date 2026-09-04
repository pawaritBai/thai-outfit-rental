@extends('layouts.main')

@section('content')
<div class="container mx-auto p-4 flex flex-col md:flex-row gap-6">

    <!-- Sidebar -->
    <div class="w-full md:w-1/4 bg-white rounded-lg shadow h-fit">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Account Settings</h3>
        </div>
        <ul class="p-4 space-y-2 text-sm">
            <a href="{{ route('profile.index') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors">
                <i class="fas fa-user mr-3 w-4 text-center"></i> Profile
            </a>
            <a href="{{ route('profile.customer.address.index') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors">
                <i class="fas fa-map-marker-alt mr-3 w-4 text-center"></i> Address
            </a>
            <a href="{{ route('payment.index') }}" class="flex items-center py-2 px-3 text-purple-600 bg-purple-50 rounded-md transition-colors font-semibold">
                <i class="fas fa-credit-card mr-3 w-4 text-center"></i> Payment
            </a>
            <a href="{{ route('profile.customer.orderHistory') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors">
                <i class="fas fa-history mr-3 w-4 text-center"></i> History
            </a>
            <a href="{{ route('profile.customer.issue') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                <i class="fas fa-flag mr-3 w-4 text-center"></i> Report Issue
            </a>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="w-full md:w-3/4">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">รายการการชำระเงิน</h2>

        <!-- Alert for Unpaid Bookings -->
        @if($bookings->where('unpaid', '>', 0)->count() > 0)
        <div class="bg-orange-200 p-4 rounded-md mb-6 border-2">
            <p class="text-orange-700 font-medium">คุณมี {{ $bookings->where('unpaid', '>', 0)->count() }} รายการที่ต้องชำระเงินเพิ่ม</p>
            <p class="text-orange-600 text-sm mt-1">
                กรุณาดำเนินการชำระเงินเพื่อให้คำสั่งซื้อสมบูรณ์
            </p>
            <button id="filterUnpaidBtn" class="trigger-btn bg-orange-500 text-white px-4 py-2 rounded-md hover:scale-105 transition-transform duration-200 relative mt-2">
                ดูเฉพาะรายการค้างชำระ
                <span class="notification-badge bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center absolute -top-1 -right-1">
                    {{ $bookings->where('unpaid', '>', 0)->count() }}
                </span>
            </button>
        </div>
        @endif

        <!-- Bookings List -->
        <div id="bookingsList" class="space-y-4">
            @forelse ($bookings as $booking)
            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 booking-item border border-gray-100" data-unpaid="{{ $booking->unpaid > 0 ? 'true' : 'false' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800 text-lg">
                            Booking #{{ $booking->booking_id }}
                            <span class="text-sm text-gray-500">({{ $booking->purchase_date->format('Y-m-d') }})</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">สถานะ: <span class="font-medium">{{ $booking->status }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-700">ยอดรวม: <span class="font-semibold">฿{{ number_format($booking->total_with_staff, 2) }}</span></p>
                        <p class="text-gray-700">ชำระแล้ว: <span class="font-semibold">฿{{ number_format($booking->paid, 2) }}</span></p>
                        @if($booking->unpaid > 0)
                            <p class="text-red-500">ค้างชำระ: <span class="font-semibold">฿{{ number_format($booking->unpaid, 2) }}</span></p>
                        @else
                            <p class="text-green-600 text-sm mt-1">✔ ชำระครบแล้ว</p>
                        @endif
                    </div>
                </div>
                <div class="mt-4 flex justify-end items-center gap-4">
                    <a href="{{ route('profile.customer.orderDetail', $booking->booking_id) }}"
                        class="text-blue-500 hover:text-blue-600 text-sm font-medium transition-colors">
                        ดูรายละเอียดคำสั่งซื้อ
                    </a>
                    @if($booking->hasOverrented == 1 && $booking->unpaid > 0)
                        <a href="{{ route('payment.createCycle2', $booking->booking_id) }}"
                            class="inline-flex items-center px-4 py-1.5 bg-purple-600 text-white text-sm rounded-full hover:bg-purple-700 transition-colors shadow-sm">
                            ชำระเงิน (รอบ 2)
                        </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-white p-6 rounded-xl shadow-sm text-gray-500 text-center border border-gray-100">
                ไม่พบข้อมูลการชำระเงิน
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtn = document.getElementById('filterUnpaidBtn');
        const bookingItems = document.querySelectorAll('.booking-item');
        let isFiltered = false;

        console.log('Filter button:', filterBtn);
        console.log('Booking items:', bookingItems.length);

        if (filterBtn) {
            filterBtn.addEventListener('click', function () {
                console.log('Button clicked, isFiltered:', isFiltered);
                if (!isFiltered) {
                    bookingItems.forEach(item => {
                        if (item.dataset.unpaid === 'true') {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    filterBtn.childNodes[0].textContent = 'ดูทั้งหมด';
                    isFiltered = true;
                } else {
                    bookingItems.forEach(item => {
                        item.style.display = 'block';
                    });
                    filterBtn.childNodes[0].textContent = 'ดูเฉพาะรายการค้างชำระ';
                    isFiltered = false;
                }
            });
        } else {
            console.log('Filter button not found');
        }
    });
</script>
@endpush