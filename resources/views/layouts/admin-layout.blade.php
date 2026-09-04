<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ThaiWijit - ร้านค้า')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome (For Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts - Jomhuria -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jomhuria&display=swap">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .header {
            width: 100%;
            height: 71px;
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: fixed;
            top: 0;
            z-index: 100;
        }
        .logo {
            color: #FFFAFA;
            font-family: 'Jomhuria', sans-serif;
            font-size: 64px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }
        .sidebar {
            width: 259px;
            height: 953px;
            background-color: #292828;
            position: fixed;
            top: 71px;
            left: 0;
            color: white;
            transition: transform 0.3s ease-in-out;
            z-index: 90;
        }
        .sidebar-collapsed {
            transform: translateX(-100%);
        }
        .content {
            margin-left: 259px;
            margin-top: 71px;
            padding: 20px;
            flex: 1;
            transition: margin-left 0.3s ease-in-out;
        }
        .content-expanded {
            margin-left: 0;
        }
        .menu-item {
            width: 259px;
            height: 65px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .menu-item:hover, .menu-item.active {
            background-color: #8B9DF9;
        }
        .menu-item a {
            color: white;
            text-decoration: none;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .user-profile {
            display: flex;
            align-items: center;
            color: white;
            cursor: pointer;
            position: relative;
        }
        .dropdown-menu {
            position: absolute;
            top: 60px;
            right: 0;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: none;
            width: 150px;
            z-index: 101;
        }
        .dropdown-menu a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
        }
        .dropdown-menu a:hover {
            background-color: #f5f5f5;
        }
        
        /* Badge Styles for Users */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-status-inactive {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        /* Status Badge Styles for Shops */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-active {
            background-color: #DEF7EC;
            color: #03543E;
        }
        .status-inactive {
            background-color: #FDE8E8;
            color: #9B1C1C;
        }
        
        /* Button Styles */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-align: center;
            transition: all 0.2s;
            display: inline-block;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #8B9DF9;
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background-color: #7A8CE8;
            transform: translateY(-1px);
        }
        .btn-success {
            background-color: #10B981;
            color: white;
            border: none;
        }
        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }
        .btn-danger {
            background-color: #EF4444;
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background-color: #DC2626;
            transform: translateY(-1px);
        }
        .btn-info {
            background-color: #3B82F6;
            color: white;
            border: none;
        }
        .btn-info:hover {
            background-color: #2563EB;
            transform: translateY(-1px);
        }
        
        /* Loading indicator */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Loading indicator -->
    <div class="loading" id="loadingIndicator">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <span class="ml-2">Loading...</span>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="flex items-center">
            <div class="logo">ThaiWijit</div>
            <div class="ml-4 cursor-pointer" id="menuToggle">
                <i class="fas fa-bars text-white text-2xl"></i>
            </div>
        </div>
        
        <!-- User profile on right side -->
        <div class="user-profile" id="userProfile">
            <i class="fas fa-user-circle text-white text-4xl mr-3"></i>
            <span class="text-white font-medium">{{ Auth::user()->name }}</span>
            <i class="fas fa-chevron-down text-white ml-2"></i>
            
            <!-- Dropdown menu -->
            <div class="dropdown-menu" id="userDropdown">
                <a href="{{ route('profile.show') }}">Profile</a>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                        Logout
                    </a>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <!-- In the sidebar section of shopowner-layout.blade.php -->
<div class="sidebar" id="sidebar">
    <div class="py-6">
        @php
            // Check if shop owner has registered any shop (regardless of status)
            $hasRegisteredShop = \App\Models\Shop::where('shop_owner_id', Auth::id())->exists();
            // Check for active shop (for other menu items)
            $userShop = \App\Models\Shop::where('shop_owner_id', Auth::id())->where('status', 'active')->first();
            
            // Get current route name
            $currentRouteName = Route::currentRouteName();
        @endphp

        <div class="menu-item {{ $currentRouteName == 'admin.dashboard' ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" data-section="dashboard-home">
                <i class="fas fa-tachometer-alt mr-3"></i>
                <span>แดชบอร์ด</span>
            </a>
        </div>
        
        <div class="menu-item {{ $currentRouteName == 'admin.users.index' ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}">
                <i class="fas fa-user mr-3"></i>
                <span>ผู้ใช้งาน</span>
            </a>
        </div>
        
        <div class="menu-item {{ $currentRouteName == 'admin.shops.index' ? 'active' : '' }}">
            <a href="{{ route('admin.shops.index') }}">
                <i class="fas fa-store mr-3"></i>
                <span>ร้านค้า</span>
            </a>
        </div>

        <div class="menu-item {{ $currentRouteName == 'admin.outfits.adminindex' ? 'active' : '' }}">
            <a href="{{ route('admin.outfits.adminindex') }}">
                <i class="fas fa-tshirt mr-3"></i>
                <span>ชุด</span>
            </a>
        </div>
        <div class="menu-item {{ $currentRouteName == 'admin.categories.index' ? 'active' : '' }}">
            <a href="{{ route('admin.categories.index') }}">
                <i class="fas fa-tags mr-3"></i>
                <span>หมวดหมู่</span>
            </a>
        </div>
        <div class="menu-item {{ $currentRouteName == 'admin.booking.index' ? 'active' : '' }}">
            <a href="{{ route('admin.booking.index') }}">
                <i class="fas fa-calendar-check mr-3"></i>
                <span>การจอง</span>
            </a>
        </div>
        <div class="menu-item {{ $currentRouteName == 'admin.issue.show' ? 'active' : '' }}">
            <a href="{{ route('admin.issue.show') }}">
                <i class="fas fa-flag mr-3"></i>
                <span>ปัญหาที่รายงาน</span>
            </a>
        </div>
        <div class="menu-item {{ $currentRouteName == 'admin.statistics.shop' ? 'active' : '' }}">
            <a href="{{ route('admin.statistics.shop') }}">
                <i class="fas fa-chart-bar fa-rotate-270 mr-3"></i>
                <span>สถิติ</span>
            </a>
        </div>
    </div>
</div>

    
    <!-- Content -->
    <div class="content" id="mainContent">
        <!-- Page Content -->
        <div class="max-w-7xl mx-auto">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    <!-- เพิ่ม Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

   <!-- In the JavaScript section at the bottom of shopowner-layout.blade.php -->
   <script>
    $(document).ready(function() {
        // Toggle sidebar visibility with animation
        $('#menuToggle').click(function() {
            $('#sidebar').toggleClass('sidebar-collapsed');
            $('#mainContent').toggleClass('content-expanded');
            
            // Change menu icon based on sidebar state
            if ($('#sidebar').hasClass('sidebar-collapsed')) {
                $('#menuToggle i').removeClass('fa-bars').addClass('fa-bars-staggered');
            } else {
                $('#menuToggle i').removeClass('fa-bars-staggered').addClass('fa-bars');
            }
        });
        
        // Toggle user dropdown
        $('#userProfile').click(function(e) {
            e.stopPropagation();
            $('#userDropdown').toggle();
        });
        
        // Close dropdown when clicking elsewhere
        $(document).click(function() {
            $('#userDropdown').hide();
        });
        
        // Prevent dropdown from closing when clicking inside it
        $('#userDropdown').click(function(e) {
            e.stopPropagation();
        });
        
        // Show loading indicator when navigating
        $('a:not([href^="#"]):not([target="_blank"]):not([onclick])').click(function() {
            if ($(this).attr('href') && !$(this).closest('.menu-item').is('[style*="opacity: 0.5"]')) {
                $('#loadingIndicator').show();
            }
        });
        
        // Show loading indicator when submitting forms
        $('form:not(#logoutForm)').submit(function() {
            $('#loadingIndicator').show();
        });
        
        // Hide loading indicator when page is fully loaded
        $(window).on('load', function() {
            $('#loadingIndicator').hide();
        });
    });
</script>


</body>
</html>