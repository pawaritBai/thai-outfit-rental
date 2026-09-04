@extends('layouts.staff-dashboard')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="flex items-center">
            <i class="fas fa-money-bill-wave card-header-icon"></i>
            <h2 class="card-title">รายได้ของคุณ</h2>
        </div>
    </div>
    <div class="card-body">
        <div class="flex mb-6">
            @php
                $routePrefix = str_replace(' ', '', Auth::user()->userType);
            @endphp
            <form method="GET" action="{{ route($routePrefix.'.work.earning') }}" class="flex gap-4">
                <button type="submit" name="period" value="daily" class="btn {{ $period == 'daily' ? 'btn-primary' : 'btn-outline' }}">
                    Daily
                </button>
                <button type="submit" name="period" value="weekly" class="btn {{ $period == 'weekly' ? 'btn-primary' : 'btn-outline' }}">
                    Weekly
                </button>
                <button type="submit" name="period" value="monthly" class="btn {{ $period == 'monthly' ? 'btn-primary' : 'btn-outline' }}">
                    Monthly
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">You have earned</h2>
                <p class="text-3xl text-green-600 font-bold">{{ number_format($totalEarnings, 2) }} ฿</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">You have done</h2>
                <p class="text-3xl text-blue-600 font-bold">{{ $tasks }} Tasks</p>
            </div>
        </div>

        <div class="chart-container">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                @if($period == 'daily')
                Daily Earnings
                @elseif($period == 'weekly')
                Weekly Earnings
                @else
                Monthly Earnings
                @endif
            </h2>
            <canvas id="earningsChart" height="100"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const earningsData = @json($earningsPerHour); // ส่งข้อมูลจาก PHP ไปยัง JavaScript

    const labels = Object.keys(earningsData); // ดึง key (วันที่)
    const data = Object.values(earningsData); // ดึง value (รายได้)
    const period = @json($period); // ดึง value (รายได้)

    if (period == 'weekly') {

        // แปลง labels เป็นชื่อวันในสัปดาห์
        const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        // เปลี่ยน labels ที่เป็นตัวเลข (1, 2, 3, ...) ให้เป็นชื่อวัน (Monday, Tuesday, Wednesday, ...)
        const updatedLabels = labels.map(label => {
            const dayIndex = parseInt(label) - 1; // ลด 1 เพื่อให้ตรงกับ index ของ `dayNames`
            return dayNames[dayIndex];
        });


        const chartData = {
            labels: dayNames,
            datasets: [{
                label: 'Earnings (฿)',
                data: data,
                backgroundColor: 'rgba(139, 157, 249, 0.5)',
                borderColor: 'rgba(139, 157, 249, 1)',
                borderWidth: 1
            }]
        };

        const earningsChart = new Chart(document.getElementById('earningsChart').getContext('2d'), {
            type: 'bar',
            data: chartData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 200
                        }
                    }
                }
            }
        });
    } else {
        const chartData = {
            labels: labels,
            datasets: [{
                label: 'Earnings (฿)',
                data: data,
                backgroundColor: 'rgba(139, 157, 249, 0.5)',
                borderColor: 'rgba(139, 157, 249, 1)',
                borderWidth: 1
            }]
        };

        const earningsChart = new Chart(document.getElementById('earningsChart').getContext('2d'), {
            type: 'bar',
            data: chartData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 200
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
