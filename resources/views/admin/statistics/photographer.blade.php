@extends('layouts.admin-layout')

@section('title', 'Photographer Statistics')

@section('content')

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-camera mr-2 text-[#8B9DF9]"></i>สถิติช่างภาพ
        </h1>

        <!-- Filter by Month -->
        <form method="GET" action="{{ route('admin.statistics.photographer') }}" class="flex gap-4 mb-6">
            <input type="month" name="month" value="{{ $month }}" class="px-4 py-2 border rounded-md">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Filter</button>
        </form>

        <!-- Role Selection Buttons -->
        <div class="mb-6">
            <button class="px-4 py-2 bg-blue-500 text-white rounded-md" id="shopBtn">Shop</button>
            <button class="px-4 py-2 bg-green-500 text-white rounded-md" id="photographerBtn">Photographer</button>
            <button class="px-4 py-2 bg-pink-500 text-white rounded-md" id="makeUpArtistBtn">Make-up Artist</button>
        </div>

        <!-- Graphs -->
        <div class="mb-6">
            <canvas id="barChart" height="100"></canvas>
        </div>

        <!-- Report Table -->
        <div>
            <h3 class="text-xl font-semibold mb-4">Top 10 Photographers</h3>
            <table class="min-w-full bg-white shadow-md rounded-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Staff ID</th>
                        <th class="px-4 py-2">Staff Name</th>
                        <th class="px-4 py-2">Total Earning</th>
                    </tr>
                </thead>
                <tbody id="photographerReport">
                    <!-- Photographer Data goes here -->
                </tbody>
            </table>

            <h3 class="text-xl font-semibold mt-6 mb-4">All Photographer Report</h3>
            <table class="min-w-full bg-white shadow-md rounded-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Staff ID</th>
                        <th class="px-4 py-2">Staff Name</th>
                        <th class="px-4 py-2">Total Earning</th>
                    </tr>
                </thead>
                <tbody id="allPhotographerReport">
                    <!-- All Photographer Data goes here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const photographerStatsTop10 = @json($photographerStatsTop10);
        const photographerStatsAll = @json($photographerStatsAll);
        

        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('barChart').getContext('2d');
            const barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: photographerStatsTop10.map(photographer => photographer.name),
                    datasets: [{
                        label: 'Total Earning (Baht)',
                        data: photographerStatsTop10.map(photographer => photographer.total_payment),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Update the report table for Photographer data
            function updatePhotographerTable() {
                const photographerReport = document.getElementById('photographerReport');
                photographerReport.innerHTML = photographerStatsTop10.map(photographer => `
                    <tr>
                        <td class="px-4 py-2 text-center">${photographer.staff_id}</td>
                        <td class="px-4 py-2 text-center">${photographer.name}</td>
                        <td class="px-4 py-2 text-center">${photographer.total_payment}</td>
                    </tr>
                `).join('');
            }

            // Update the report table for all Photographer data
            function updateAllPhotographerTable() {
                const allPhotographerReport = document.getElementById('allPhotographerReport');
                allPhotographerReport.innerHTML = photographerStatsAll.map(photographer => `
                    <tr>
                        <td class="px-4 py-2 text-center">${photographer.staff_id}</td>
                        <td class="px-4 py-2 text-center">${photographer.name}</td>
                        <td class="px-4 py-2 text-center">${photographer.total_payment}</td>
                    </tr>
                `).join('');
            }

            // Initialize tables on load
            updatePhotographerTable();
            updateAllPhotographerTable();

            // Switch between different charts when the button is clicked
            document.getElementById('shopBtn').addEventListener('click', function () {
                window.location.href = "{{ route('admin.statistics.shop') }}";
            });

            document.getElementById('makeUpArtistBtn').addEventListener('click', function () {
                // Redirect to Make-up Artist statistics route
                window.location.href = "{{ route('admin.statistics.make-upartist') }}";
            });
        });
    </script>

@endsection
