@extends('layouts.admin-layout')

@section('title', 'Make-Up Artist Statistics')

@section('content')

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-paint-brush mr-2 text-[#8B9DF9]"></i>สถิติช่างแต่งหน้า
        </h1>
        <!-- Filter by Month -->
        <form method="GET" action="{{ route('admin.statistics.make-upartist') }}" class="flex gap-4 mb-6">
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
            <h3 class="text-xl font-semibold mb-4">Top 10 Make-up Artists</h3>
            <table class="min-w-full bg-white shadow-md rounded-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Staff ID</th>
                        <th class="px-4 py-2">Staff Name</th>
                        <th class="px-4 py-2">Total Earning</th>
                    </tr>
                </thead>
                <tbody id="makeUpArtistReport">
                    <!-- Make-up Artist Data goes here -->
                </tbody>
            </table>

            <h3 class="text-xl font-semibold mt-6 mb-4">All Make-up Artists Report</h3>
            <table class="min-w-full bg-white shadow-md rounded-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Staff ID</th>
                        <th class="px-4 py-2">Staff Name</th>
                        <th class="px-4 py-2">Total Earning</th>
                    </tr>
                </thead>
                <tbody id="allMakeUpArtistReport">
                    <!-- All Make-up Artist Data goes here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const makeUpArtistStatsTop10 = @json($makeUpArtistStatsTop10);
        const makeUpArtistStatsAll = @json($makeUpArtistStatsAll);

        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('barChart').getContext('2d');
            const barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: makeUpArtistStatsTop10.map(artist => artist.name),
                    datasets: [{
                        label: 'Total Earning (Baht)',
                        data: makeUpArtistStatsTop10.map(artist => artist.total_payment),
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

            // Update the report table for Make-up Artist data
            function updateMakeUpArtistTable() {
                const makeUpArtistReport = document.getElementById('makeUpArtistReport');
                makeUpArtistReport.innerHTML = makeUpArtistStatsTop10.map(artist => `
                    <tr>
                        <td class="px-4 py-2 text-center">${artist.staff_id}</td>
                        <td class="px-4 py-2 text-center">${artist.name}</td>
                        <td class="px-4 py-2 text-center">${artist.total_payment}</td>
                    </tr>
                `).join('');
            }

            // Update the report table for all Make-up Artist data
            function updateAllMakeUpArtistTable() {
                const allMakeUpArtistReport = document.getElementById('allMakeUpArtistReport');
                allMakeUpArtistReport.innerHTML = makeUpArtistStatsAll.map(artist => `
                    <tr>
                        <td class="px-4 py-2 text-center">${artist.staff_id}</td>
                        <td class="px-4 py-2 text-center">${artist.name}</td>
                        <td class="px-4 py-2 text-center">${artist.total_payment}</td>
                    </tr>
                `).join('');
            }

            // Initialize tables on load
            updateMakeUpArtistTable();
            updateAllMakeUpArtistTable();

            // Switch between different charts when the button is clicked
            document.getElementById('shopBtn').addEventListener('click', function () {
                window.location.href = "{{ route('admin.statistics.shop') }}";
            });

            document.getElementById('photographerBtn').addEventListener('click', function () {
                // Redirect to Photographer statistics route
                window.location.href = "{{ route('admin.statistics.photographer') }}";
            });
        });
    </script>

@endsection
