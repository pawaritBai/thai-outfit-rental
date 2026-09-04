@extends('layouts.admin-layout')

@section('title', 'Shop Statistics')

@section('content')

    <div class="container mx-auto p-6">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-store mr-2 text-[#8B9DF9]"></i>สถิติร้านค้า
    </h1>

        <!-- Filter by Month -->
        <form method="GET" action="{{ route('admin.statistics.shop') }}" class="flex gap-4 mb-6">
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
            <h3 class="text-xl font-semibold mb-4">Top 10 Shops</h3>
            <table class="min-w-full bg-white shadow-md rounded-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Shop ID</th>
                        <th class="px-4 py-2">Shop Name</th>
                        <th class="px-4 py-2">Total Sales</th>
                    </tr>
                </thead>
                <tbody id="shopReport">
                    <!-- Shop Data goes here -->
                </tbody>
            </table>

            <h3 class="text-xl font-semibold mt-6 mb-4">All Shops Report</h3>
            <table class="min-w-full bg-white shadow-md rounded-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Shop ID</th>
                        <th class="px-4 py-2">Shop Name</th>
                        <th class="px-4 py-2">Total Sales</th>
                    </tr>
                </thead>
                <tbody id="allShopReport">
                    <!-- All Shop Data goes here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Data from your controller for Shop stats (example data, replace with dynamic data)
        const shopStatsTop10 = @json($ShopstatsTop10); // Assuming data is passed from the backend
        const shopStatsAll = @json($ShopstatsAll); // Assuming data is passed from the backend

        
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize the chart for Shop data
            const ctx = document.getElementById('barChart').getContext('2d');
            const barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: shopStatsTop10.map(shop => shop.shop_name), // Mapping shop name for X-axis
                    datasets: [{
                        label: 'Total Sales (Baht)',
                        data: shopStatsTop10.map(shop => shop.total_sales), // Mapping sales for Y-axis
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

            // Update the report table for Shop data
            function updateShopTable() {
                const shopReport = document.getElementById('shopReport');
                shopReport.innerHTML = shopStatsTop10.map(shop => `
                    <tr>
                        <td class="px-4 py-2 text-center">${shop.shop_id}</td>
                        <td class="px-4 py-2 text-center">${shop.shop_name}</td>
                        <td class="px-4 py-2 text-center">${shop.total_sales}</td>
                    </tr>
                `).join('');
            }

            // Update the report table for all Shop data
            function updateAllShopTable() {
                const allShopReport = document.getElementById('allShopReport');
                allShopReport.innerHTML = shopStatsAll.map(shop => `
                    <tr>
                        <td class="px-4 py-2 text-center">${shop.shop_id}</td>
                        <td class="px-4 py-2 text-center">${shop.shop_name}</td>
                        <td class="px-4 py-2 text-center">${shop.total_sales}</td>
                    </tr>
                `).join('');
            }

            // Initialize tables on load
            updateShopTable();
            updateAllShopTable();

            // Switch between different charts when the button is clicked
            document.getElementById('shopBtn').addEventListener('click', function () {
                // Update the chart for Shop data
                barChart.data.labels = shopStatsTop10.map(shop => shop.shop_name);
                barChart.data.datasets[0].data = shopStatsTop10.map(shop => shop.total_sales);
                barChart.update();

                // Update the report table
                updateShopTable();
                updateAllShopTable();
            });

            document.getElementById('photographerBtn').addEventListener('click', function () {
                // Update the chart for Photographer data
                barChart.data.labels = photographerStatsTop10.map(photographer => photographer.staff_id);
                barChart.data.datasets[0].data = photographerStatsTop10.map(photographer => photographer.total_payment);
                barChart.update();

                // Update the tables for Photographer data
                const photographerReport = document.getElementById('shopReport');
                photographerReport.innerHTML = photographerStatsTop10.map(photographer => `
                    <tr>
                        <td class="px-4 py-2">${photographer.staff_id}</td>
                        <td class="px-4 py-2">${photographer.staff_id}</td>
                        <td class="px-4 py-2">${photographer.total_payment}</td>
                    </tr>
                `).join('');
            });

            document.getElementById('makeUpArtistBtn').addEventListener('click', function () {
                // Update the chart for Make-up Artist data
                barChart.data.labels = makeUpArtistStatsTop10.map(artist => artist.staff_id);
                barChart.data.datasets[0].data = makeUpArtistStatsTop10.map(artist => artist.total_payment);
                barChart.update();

                // Update the tables for Make-up Artist data
                const makeUpArtistReport = document.getElementById('shopReport');
                makeUpArtistReport.innerHTML = makeUpArtistStatsTop10.map(artist => `
                    <tr>
                        <td class="px-4 py-2">${artist.staff_id}</td>
                        <td class="px-4 py-2">${artist.staff_id}</td>
                        <td class="px-4 py-2">${artist.total_payment}</td>
                    </tr>
                `).join('');
            });

            document.getElementById('photographerBtn').addEventListener('click', function () {
                // Redirect to Photographer statistics route
                window.location.href = "{{ route('admin.statistics.photographer') }}";
            });

            document.getElementById('makeUpArtistBtn').addEventListener('click', function () {
                // Redirect to Make-up Artist statistics route
                window.location.href = "{{ route('admin.statistics.make-upartist') }}";
            });
        });


    </script>
@endsection