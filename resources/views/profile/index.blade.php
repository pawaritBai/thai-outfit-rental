@extends('layouts.main')

@section('title', 'My Profile')

@section('content')
<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-5 px-4">
        
        <!-- Sidebar -->
        <div class="w-full md:w-1/4 bg-white rounded-lg shadow sticky top-5 h-fit">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Account Settings</h3>
            </div>
            <ul class="p-4 space-y-2 text-sm">
                <!-- Profile -->
                <a href="{{ route('profile.index') }}" class="flex items-center py-2 px-3 text-purple-600 bg-purple-50 rounded-md transition-colors cursor-pointer font-semibold">
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

                <!-- Report Issue -->
                <a href="{{ route('profile.customer.issue') }}" class="flex items-center py-2 px-3 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors cursor-pointer">
                    <i class="fas fa-flag mr-3 w-4 text-center"></i> Report Issue
                </a>
            </ul>
        </div>


        {{-- Main Content --}}
        <div class="w-full md:flex-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                
                {{-- Header --}}
                <div class="bg-purple-500 h-32 relative">
                    <div class="absolute top-4 right-4">
                        <a href="{{ route('profile.editCus') }}" 
                           class="bg-white text-sm text-gray-700 hover:text-white hover:bg-purple-600 border border-purple-200 px-4 py-2 rounded shadow transition-colors duration-200">
                            <i class="fas fa-edit mr-1"></i> Edit Profile
                        </a>
                    </div>
                </div>

                {{-- Profile Image & Name --}}
                <div class="flex flex-col items-center -mt-16 px-6 pt-4 pb-0">
                    <div class="relative">
                        <img src="{{ $user->profilePicture ? asset($user->profilePicture) : asset('images/default-profile.png') }}"
                            class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg" />
                        <div class="absolute bottom-1 right-1 bg-purple-600 text-white rounded-full h-6 w-6 flex items-center justify-center cursor-pointer shadow">
                            <i class="fas fa-camera text-xs"></i>
                        </div>
                    </div>
                    <h2 class="mt-3 text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
                </div>

                {{-- Tabs --}}
                <div class="px-6 mt-4 border-b">
                    <ul class="flex text-sm font-semibold text-gray-600">
                        <li class="mr-6 pb-2 px-1 border-b-2 border-purple-600 text-purple-600">Personal Information</li>
                    </ul>
                </div>

                {{-- Information --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-md p-4 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase mb-1">FULL NAME</p>
                            <p class="text-gray-800 font-medium">{{ $user->name }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-md p-4 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase mb-1">MOBILE NUMBER</p>
                            <p class="text-gray-800 font-medium">{{ $user->phone ?? '9999999999' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-md p-4 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase mb-1">EMAIL ADDRESS</p>
                            <p class="text-gray-800 font-medium">{{ $user->email }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-md p-4 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase mb-1">GENDER</p>
                            <p class="text-gray-800 font-medium">
                                @if($user->gender == 'male') Male
                                @elseif($user->gender == 'female') Female
                                @elseif($user->gender == 'others') Others
                                @else Not specified
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection