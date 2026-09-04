<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ThaiWijit')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- หรือใช้ <link rel="stylesheet" ...> ตามโปรเจกต์ --}}
</head>
<body class="bg-gray-100 text-gray-900">

    {{-- 🌐 Navbar --}}
    <nav class="bg-indigo-600 text-white px-6 py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold">
                <a href="/">ThaiWijit</a>
            </div>
            <div class="flex items-center space-x-6">
                <a href="/products" class="hover:underline">Products</a>
                <form action="/search" method="GET" class="hidden md:block">
                    <input type="text" name="query" placeholder="ค้นหาชุดไทย..." 
                           class="rounded px-3 py-1 text-gray-800" />
                </form>
                <a href="/cart" class="hover:underline">🛒</a>
                <a href="/profile" class="hover:underline">Profile</a>
                <a href="/logout" class="hover:underline">Logout</a>
            </div>
        </div>
    </nav>

    {{-- 🔽 Content --}}
    <main>
        @yield('content')
    </main>

</body>
</html>
