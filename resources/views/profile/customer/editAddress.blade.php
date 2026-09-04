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
            <a href="{{ route('profile.customer.address.index') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
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
        </ul>
    </div>


    {{-- Content --}}
    <div class="w-full md:w-3/4">
        <h2 class="text-2xl font-bold mb-4">แก้ไขที่อยู่</h2>
        <form action="{{ route('profile.customer.address.update', $cusAddress->cus_address_id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('profile.customer.form', ['data' => $cusAddress])
            <button type="submit" class="mt-4 bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">อัปเดต</button>
        </form>
    </div>
</div>
@endsection
