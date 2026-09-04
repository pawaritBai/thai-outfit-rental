@extends('layouts.staff-dashboard')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="flex items-center">
            <i class="fas fa-clipboard-list card-header-icon"></i>
            <h2 class="card-title">รายการงานที่เปิดรับ</h2>
        </div>
        <div>
            @php
                $routePrefix = str_replace(' ', '', Auth::user()->userType);
            @endphp
            <a href="{{ route($routePrefix.'.dashboard') }}" 
               class="btn btn-primary">
                <i class="fas fa-calendar-alt"></i> ไปหน้าตารางงาน
            </a>
        </div>
    </div>
    <div class="card-body">
        @if ($services->isEmpty())
        <div class="text-center bg-yellow-100 text-yellow-800 p-4 rounded-lg">
            ไม่มีงานที่เปิดรับในขณะนี้
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($services as $service)
            @php
            $staff_count = count($service->work_distribution);
            $assigned_customers = $service->work_distribution[$service->staff_count] ?? 0;
            $earning = $assigned_customers * 2000;
            @endphp

            <div class="job-card" id="job-{{ $service->select_service_id }}">
                <h3 class="text-lg font-bold">Booking ID: {{ $service->booking_id }}</h3>
                <p class="job-detail"><strong>ประเภทงาน:</strong> {{ e($service->service_type) }}</p>
                <p class="job-detail"><strong>วันเวลาที่ต้องให้บริการ:</strong>
                    {{ \Carbon\Carbon::parse($service->reservation_date)->format('d M Y, H:i') }}
                </p>
                <p class="job-detail"><strong>จำนวนลูกค้าที่ต้องให้บริการ:</strong> {{ $assigned_customers }} คน</p>
                <p class="job-detail"><strong>ช่างที่รับแล้ว:</strong> {{ $service->staff_count }} / {{ $staff_count }}</p>
                <p class="job-detail"><strong>ค่าตอบแทน:</strong> <span class="job-earning">{{ $earning }} ฿</span></p>

                @if ($service->booking->user)
                <p class="job-detail"><strong>Customer Name:</strong>
                    {{ e($service->booking->user->name) }}
                </p>
                <p class="job-detail"><strong>Phone :</strong>
                    {{ e($service->booking->user->phone) }}
                </p>
                @else
                <p class="job-detail"><strong>Customer Name:</strong>
                    ไม่ระบุ
                </p>
                <p class="job-detail"><strong>Phone :</strong>
                    ไม่ระบุ
                </p>
                @endif
                @if ($service->address)
                <p class="job-detail"><strong>ที่อยู่:</strong> {{ e($service->address->Street) }}, {{ e($service->address->District) }}, {{ e($service->address->Province) }}</p>
                @else
                <p class="job-detail"><strong>ที่อยู่:</strong> ไม่ระบุ</p>
                @endif

                @if (Auth::user()->userType === $service->service_type && $assigned_customers > 0)
                <button class="btn btn-success mt-3 w-full"
                    onclick="acceptJob({{$service->select_service_id}},'{{ $service->service_type }}')">
                    ✅ รับงาน
                </button>
                <div id="response-{{ $service->select_service_id }}" class="mt-2 text-center"></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function acceptJob(serviceId, userType) {
        const button = document.querySelector(`#job-${serviceId} button`);
        const cleanedUserType = userType.replace(/\s+/g, '');
        const url = `/${cleanedUserType}/accept-job`;
        button.disabled = true;

        const data = {
            select_service_id: serviceId
        };

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error('เกิดข้อผิดพลาดในการรับงาน');
                return response.json();
            })
            .then(result => {
                document.getElementById(`response-${serviceId}`).innerHTML =
                    '<span class="text-green-600">✅ รับงานสำเร็จ!</span>';

                const staffCountElement = document.querySelector(`#job-${serviceId} p:nth-child(5)`);
                staffCountElement.innerHTML = `<strong>ช่างที่รับแล้ว:</strong> ${result.staff_count} / ${result.required_staff}`;

                button.style.display = 'none';
            })
            .catch(error => {
                document.getElementById(`response-${serviceId}`).innerHTML =
                    `<span class="text-red-600">${error.message}</span>`;
                button.disabled = false;
            });
    }
</script>
@endsection
