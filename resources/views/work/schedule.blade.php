@extends('layouts.staff-dashboard')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="flex items-center">
            <i class="fas fa-calendar-alt card-header-icon"></i>
            <h2 class="card-title">ตารางงานของคุณ</h2>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Filter Dropdown -->
            <form method="GET" action="{{ route(str_replace(' ', '', Auth::user()->userType).'.dashboard') }}" class="flex items-center">
                <select name="status" class="border p-2 rounded-md" onchange="this.form.submit()">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>เสร็จแล้ว</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>ยังไม่เสร็จ</option>
                </select>
            </form>
            <a href="{{ route(str_replace(' ', '', Auth::user()->userType).'.work-list') }}" 
               class="btn btn-primary">
                <i class="fas fa-clipboard-list"></i> ไปหน้ารับงาน
            </a>
        </div>
    </div>
    <div class="card-body">
        @if ($works->isEmpty())
        <div class="text-center bg-yellow-100 text-yellow-800 p-4 rounded-lg">
            ❌ ยังไม่มีงานที่ได้รับ
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($works as $work)
            <a href="{{ route(str_replace(' ', '', $work->selectService->service_type) . '.work.details', ['id' => encrypt($work->select_staff_detail_id)]) }}" class="block">
                <div class="job-card relative">
                    <!-- Completion indicator -->
                    @if($work->finished_time)
                    <div class="absolute top-0 right-0 mt-2 mr-2">
                        <span class="bg-green-500 text-white rounded-full p-1 flex items-center justify-center" 
                              title="Job completed">
                            <i class="fas fa-check"></i>
                        </span>
                    </div>
                    @endif
                    
                    <div class="job-date">
                        {{ \Carbon\Carbon::parse($work->selectService->reservation_date)->format('d M Y') }}
                    </div>
                    <p class="job-detail"><strong>Location:</strong> 
                        @if($work->selectService->address)
                            {{ e($work->selectService->address->Street) }}, 
                            {{ e($work->selectService->address->District) }}, 
                            {{ e($work->selectService->address->Province) }}
                        @else
                            Not specified
                        @endif
                    </p>
                    <p class="job-detail"><strong>Appointment Time:</strong> 
                        {{ \Carbon\Carbon::parse($work->selectService->reservation_date)->format('H:i') }}</p>
                    <p class="job-detail"><strong>จำนวนลูกค้าที่ต้องให้บริการ:</strong> {{ e($work->customer_count) }} คน</p>
                    <p class="job-detail"><strong>Earning:</strong> 
                        <span class="job-earning">{{ e($work->earning) }} ฿</span></p>
                    
                    <!-- Status indicator at the bottom -->
                    <div class="mt-3 pt-2 border-t border-gray-200">
                        @if($work->finished_time)
                            <span class="text-green-600 text-sm font-medium flex items-center">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                            </span>
                        @else
                            @php
                                $appointmentTime = \Carbon\Carbon::parse($work->selectService->reservation_date);
                                $now = \Carbon\Carbon::now();
                            @endphp
                            
                            @if($appointmentTime->isFuture())
                                <span class="text-blue-600 text-sm font-medium flex items-center">
                                    <i class="fas fa-clock mr-1"></i> Upcoming
                                </span>
                            @else
                                <span class="text-orange-600 text-sm font-medium flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Needs completion
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection