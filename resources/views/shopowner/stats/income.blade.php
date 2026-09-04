@extends('layouts.shopowner-layout')

@section('title', 'รายได้')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="flex items-center">
            <i class="fas fa-money-bill-wave card-header-icon mr-2"></i>
            <h2 class="card-title text-xl font-bold">สถิติรายได้</h2>
        </div>
    </div>
    <div class="card-body">
        <div class="flex flex-wrap mb-6 items-end gap-4">
            <form method="GET" action="{{ route('shopowner.stats.income') }}" class="flex gap-4">
                <button type="submit" name="period" value="weekly" class="btn {{ $period == 'weekly' ? 'btn-primary' : 'btn-outline' }}">
                    รายสัปดาห์
                </button>
                <button type="submit" name="period" value="monthly" class="btn {{ $period == 'monthly' ? 'btn-primary' : 'btn-outline' }}">
                    รายเดือน
                </button>
                <button type="submit" name="period" value="yearly" class="btn {{ $period == 'yearly' ? 'btn-primary' : 'btn-outline' }}">
                    รายปี
                </button>
            </form>
            
            <div class="border-l h-8 mx-2"></div>
            
            <form method="GET" action="{{ route('shopowner.stats.income') }}" class="flex flex-wrap items-end gap-4">
                <input type="hidden" name="period" value="custom">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เริ่มต้น</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fa fa-search mr-1"></i> ค้นหา
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">รายได้ทั้งหมด</h2>
                <p class="text-3xl text-green-600 font-bold">{{ number_format($totalEarnings, 2) }} ฿</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">การจองทั้งหมด</h2>
                <p class="text-3xl text-blue-600 font-bold">{{ $totalBookings }} รายการ</p>
            </div>
        </div>

        <div class="chart-container bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                @if($period == 'weekly')
                    สถิติรายได้สัปดาห์นี้
                @elseif($period == 'monthly')
                    สถิติรายได้เดือนนี้
                @elseif($period == 'yearly')
                    สถิติรายได้ปีนี้
                @elseif($period == 'custom')
                    สถิติรายได้ตามช่วงเวลาที่เลือก
                @endif
            </h2>
            <canvas id="earningsChart" height="100"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validate date range
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                if (endDateInput.value && startDateInput.value > endDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
            });
            
            endDateInput.addEventListener('change', function() {
                if (startDateInput.value && endDateInput.value < startDateInput.value) {
                    startDateInput.value = endDateInput.value;
                }
            });
        }
    });

    const earningsData = @json($earningsData);
    const labels = Object.keys(earningsData);
    const data = Object.values(earningsData);
    const period = @json($period);

    let chartLabels = labels;
    
    if (period === 'weekly') {
        // แปลง labels เป็นชื่อวันในสัปดาห์
        const dayNames = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'];
        
        // เปลี่ยน labels ที่เป็นตัวเลข (1, 2, 3, ...) ให้เป็นชื่อวัน
        chartLabels = labels.map(label => {
            const dayIndex = parseInt(label) - 1; // ลด 1 เพื่อให้ตรงกับ index ของ `dayNames`
            return dayNames[dayIndex];
        });
    } else if (period === 'yearly') {
        // แปลง labels เป็นชื่อเดือนในภาษาไทย
        const monthNames = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        
        // เปลี่ยน labels ที่เป็นตัวเลข (01, 02, 03, ...) ให้เป็นชื่อเดือน
        chartLabels = labels.map(label => {
            const monthIndex = parseInt(label) - 1; // ลด 1 เพื่อให้ตรงกับ index ของ `monthNames`
            return monthNames[monthIndex];
        });
    }

    const chartData = {
        labels: chartLabels,
        datasets: [{
            label: 'รายได้ (฿)',
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
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'สถิติรายได้'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ฿';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
