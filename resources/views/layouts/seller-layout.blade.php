<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ThaiWijit')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome (For Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100">

    <!-- Sidebar -->
    <div class="flex">
        <div class="w-64 bg-gray-900 min-h-screen text-white">
            <div class="p-5 text-lg font-bold">ThaiWijit</div>
            <nav class="mt-5">
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-calendar"></i> Reservation</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-tshirt"></i> Dress</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-layer-group"></i> Categories</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-flag"></i> Status</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-comment"></i> Reviews</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-chart-bar"></i> Statistic</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-cog"></i> Setting</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-sign-out-alt"></i> Logout</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-bullhorn"></i> Promotion</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-gray-700"><i class="fa fa-user-shield"></i> Admin</a>
                <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-blue-700"><i class="fa fa-question-circle"></i> Help</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Navbar -->
            <nav class="bg-black text-white flex justify-between px-6 py-3">
                <div>
                    <button class="text-white text-2xl"><i class="fa fa-bars"></i></button>
                </div>
                <div>
                    <span class="mr-2">@seller</span>
                    <i class="fa fa-user-circle text-xl"></i>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </div>
    </div>

</body>
</html>
