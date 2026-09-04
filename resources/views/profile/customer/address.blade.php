@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 mt-6 flex flex-col md:flex-row gap-6">
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
                <a href="{{ route('profile.customer.address.index') }}" class="flex items-center py-2 px-3 text-purple-600 bg-purple-50 rounded-md transition-colors cursor-pointer font-semibold">
                    <i class="fas fa-map-marker-alt mr-3 w-4 text-center"></i> Address
                </a>

                <!-- Payment -->
                <a href="{{ route('payment.index') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                    <i class="fas fa-credit-card mr-3 w-4 text-center"></i> Payment
                </a>

                <!-- History -->
                <a href="{{ route('profile.customer.orderHistory') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                    <i class="fas fa-history mr-3 w-4 text-center"></i> History
                </a>

                <!-- Report Issue -->
                <a href="{{ route('profile.customer.issue') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                    <i class="fas fa-flag mr-3 w-4 text-center"></i> Report Issue
                </a>
            </ul>
    </div>


    <!-- Content -->
    <div class="w-full md:w-3/4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">ที่อยู่ของคุณ</h2>
            <a href="{{ route('profile.customer.address.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md shadow">
                + เพิ่มที่อยู่ใหม่
            </a>
        </div>

        @forelse($addresses as $addr)
            <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm">
                <h3 class="font-semibold text-lg text-purple-700 mb-2">{{ $addr->AddressName }}</h3>
                <p class="text-gray-700">{{ $addr->address->HouseNumber }} {{ $addr->address->Street }}</p>
                <p class="text-gray-700">ต.{{ $addr->address->Subdistrict }} อ.{{ $addr->address->District }} จ.{{ $addr->address->Province }}</p>
                <p class="text-gray-700">รหัสไปรษณีย์ {{ $addr->address->PostalCode }}</p>

                <div class="flex gap-4 mt-3">
                    <a href="{{ route('profile.customer.address.edit', $addr->cus_address_id) }}"
                       class="text-blue-600 hover:underline">แก้ไข</a>

                    <form action="{{ route('profile.customer.address.delete', $addr->cus_address_id) }}"
                          method="POST" onsubmit="return confirm('คุณต้องการลบที่อยู่นี้หรือไม่?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">ลบ</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-gray-500">ยังไม่มีที่อยู่ที่บันทึกไว้</div>
        @endforelse
    </div>
</div>
@endsection
