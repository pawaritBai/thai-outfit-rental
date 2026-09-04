@extends('layouts.main')

@section('title', 'Issue')

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

                <!-- Report Issue -->
                <a href="{{ route('profile.customer.issue') }}" class="flex items-center py-2 px-3 text-purple-600 bg-purple-50 rounded-md transition-colors cursor-pointer font-semibold">
                    <i class="fas fa-flag mr-3 w-4 text-center"></i> Report Issue
                </a>
            </ul>
    </div>


    <!-- Content -->
    <div class="w-full md:w-3/4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">🚩 Issue History</h2>
            <!-- ปุ่มแจ้งปัญหา -->
            <div class="flex justify-end mb-6">
                <a href="{{ route('profile.customer.create') }}"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md shadow">
                    <i class="fas fa-plus mr-2"></i> Report Issue
                </a>
            </div>
        </div>

        <!-- รายการแจ้งเตือน -->
        @if ($notifications->isEmpty())
            <div class="text-center bg-yellow-50 text-yellow-700 p-6 rounded-lg shadow-md">
                <span class="text-lg font-medium">❌ No notifications occurred.</span>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($notifications as $notification)
                    @if (Auth::user()->userType == 'admin')
                        @if($notification->issue_id)
                            <a href="{{ route('admin.issue.replyPage', ['id' => $notification->issue_id]) }}" class="block">
                                <div class="bg-white p-6 rounded-lg shadow-md">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ e($notification->title) }}</h4>
                                    <p class="text-gray-600 mb-1"><strong class="text-gray-800">Report At:</strong> {{ e($notification->created_at) }}</p>
                                    <p class="text-gray-600"><strong class="text-gray-800">Description:</strong> {{ Str::limit(e($notification->description), 100) }}</p>
                                </div>
                            </a>
                        @else
                            <div class="text-center bg-red-50 text-red-700 p-6 rounded-lg shadow-md">
                                <span class="text-lg font-medium">❌ ข้อมูลไม่ถูกต้อง</span>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('issue.reported', ['id' => $notification->id]) }}" class="block">
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h4 class="text-lg font-semibold text-gray-800 mb-2"><strong>Title:</strong> {{ e($notification->title) }}</h4>
                                <p class="text-gray-600 mb-1"><strong class="text-gray-800">Report ID:</strong> {{ e($notification->id) }}</p>
                                <p class="text-gray-600"><strong class="text-gray-800">Description:</strong> {{ Str::limit(e($notification->description), 100) }}</p>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection