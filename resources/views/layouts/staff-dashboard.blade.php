<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Dashboard - ThaiWijit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jomhuria&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
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
            height: 100vh;
            background-color: #292828;
            position: fixed;
            top: 71px;
            left: 0;
            color: white;
            transition: transform 0.3s ease-in-out;
            z-index: 90;
            overflow-y: auto;
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
            background-color: #f3f4f6;
            min-height: calc(100vh - 71px);
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

        /* Card Styles */
        .card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .card-header {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header-icon {
            font-size: 1.25rem;
            color: #8B9DF9;
            margin-right: 0.75rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }
        .card-body {
            padding: 1.5rem;
        }

        /* Badge Styles */
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

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #8B9DF9;
            color: white;
        }
        .btn-primary:hover {
            background-color: #7a8ce8;
            transform: translateY(-1px);
        }
        .btn-success {
            background-color: #10b981;
            color: white;
        }
        .btn-success:hover {
            background-color: #059669;
        }
        .btn-danger {
            background-color: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background-color: #dc2626;
        }
        .btn-info {
            background-color: #3b82f6;
            color: white;
        }
        .btn-info:hover {
            background-color: #2563eb;
        }
        .btn-outline {
            background-color: transparent;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }
        .btn-outline:hover {
            background-color: #f3f4f6;
        }
        .btn i {
            margin-right: 0.375rem;
        }

        /* Loading indicator */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }

        /* Job card styles */
        .job-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
            position: relative;
            border-left: 4px solid #e5e7eb;
        }
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        /* Completed job indicator */
        .job-card.completed {
            border-left-color: #10b981;
        }
        /* Upcoming job indicator */
        .job-card.upcoming {
            border-left-color: #3b82f6;
        }
        /* Needs completion indicator */
        .job-card.needs-completion {
            border-left-color: #f97316;
        }
        .job-date {
            font-weight: 600;
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
        }
        .job-detail {
            margin-bottom: 0.25rem;
        }
        .job-earning {
            color: #10b981;
            font-weight: 600;
        }
        /* Status badge in corner */
        .status-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
        }
        .status-badge.completed {
            background-color: #10b981;
        }

        /* Chart container */
        .chart-container {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        /* Notification Button */
        .notification-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background-color: #f97316;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }
        .notification-btn:hover {
            background-color: #ea580c;
            transform: translateY(-1px);
        }
        .notification-btn i {
            margin-right: 0.5rem;
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
            <span class="text-white font-medium">{{ Auth::user()->name ?? 'Staff' }}</span>
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
    <div class="sidebar" id="sidebar">
        <div class="py-6">
            @php
                // Get the user type and handle both "make-up artist" and "photographer"
                $userType = Auth::user()->userType;
                $routePrefix = str_replace(' ', '', $userType);
                $currentRoute = Route::currentRouteName();
            @endphp
            
            <div class="menu-item {{ $currentRoute == $routePrefix.'.dashboard' ? 'active' : '' }}">
                <a href="{{ route($routePrefix.'.dashboard') }}">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    <span>ตารางงานของคุณ</span>
                </a>
            </div>
            
            <div class="menu-item {{ $currentRoute == $routePrefix.'.work-list' ? 'active' : '' }}">
                <a href="{{ route($routePrefix.'.work-list') }}">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    <span>รายการงานที่เปิดรับ</span>
                </a>
            </div>
            
            <div class="menu-item {{ $currentRoute == $routePrefix.'.work.earning' ? 'active' : '' }}">
                <a href="{{ route($routePrefix.'.work.earning') }}">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    <span>รายได้</span>
                </a>
            </div>
            
            <div class="menu-item {{ $currentRoute == 'issue.show' ? 'active' : '' }}">
                <a href="{{ route($routePrefix.'.issue.index') }}">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <span>รายงานปัญหา</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Content -->
    <div class="content" id="mainContent">
        @php
            use App\Models\SelectService;
            use Illuminate\Support\Facades\Auth;
            use Carbon\Carbon;

            // ดึงวันที่ปัจจุบัน
            $today = Carbon::today()->toDateString();

            // ดึงข้อมูลงานที่ reservation_date ตรงกับวันนี้
            $tasksToday = SelectService::query()
                ->join('SelectStaffDetails', 'SelectServices.select_service_id', '=', 'SelectStaffDetails.select_service_id')
                ->where('SelectStaffDetails.staff_id', Auth::id())
                ->whereDate('SelectServices.reservation_date', $today)
                ->get();

            // นับจำนวนงานในวันนี้
            $taskCount = $tasksToday->count();
        @endphp

        <!-- แสดงปุ่มแจ้งเตือนถ้ามีงานในวันนี้ -->
        @if($taskCount > 0)
            <a href="{{ route($routePrefix.'.dashboard') }}" class="notification-btn">
                <i class="fas fa-bell"></i>
                คุณมีงาน {{ $taskCount }} งานในวันนี้ที่ต้องไปทำ!
            </a>
        @endif

        @yield('content')
    </div>

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
        });
    </script>
</body>
</html>