<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ThaiWijit')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts - Jomhuria and Montserrat -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jomhuria&family=Montserrat:wght@400;500;600&display=swap">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        
        .logo {
            color: #FFFAFA;
            font-family: 'Jomhuria', sans-serif;
            font-size: 72px;
            font-style: normal;
            font-weight: 400;
            line-height: 1;
            letter-spacing: 0.02em;
            margin-right: 2rem;
        }
        
        .nav-link {
            font-weight: 500;
            letter-spacing: 0.03em;
            font-size: 18px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: white;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover:after {
            width: 70%;
        }
        
        /* Dropdown menu styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown::after {
            content: '';
            position: absolute;
            height: 20px;
            width: 100%;
            bottom: -20px;
            left: 0;
            z-index: 1; /* Ensure it's above other elements */
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 220px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 1;
            border-radius: 0.5rem;
            margin-top: 20px; /* Increase this value to match the ::after height */
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s, transform 0.3s;
            overflow: hidden;
        }
        
        /* Add a small gap to prevent the menu from closing when moving cursor */
        .dropdown:hover .dropdown-content {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Add this to create a hidden area between the dropdown trigger and content */
        .dropdown::after {
            content: '';
            position: absolute;
            height: 20px;
            width: 100%;
            bottom: -20px;
            left: 0;
        }
        
        .dropdown-item {
            color: #4B5563;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
        }
        
        .dropdown-item:hover {
            background-color: #F9FAFB;
            border-left-color: #8B9DF9;
            padding-left: 23px;
        }
        
        .search-input {
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .search-input:focus {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            outline: none;
        }
        
        .search-button {
            background-color: #8B9DF9;
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .search-button:hover {
            background-color: #7A8CE8;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .icon-link {
            position: relative;
            padding: 0.5rem;
            transition: all 0.2s;
        }
        
        .icon-link:hover {
            transform: translateY(-2px);
        }
        
        .auth-link {
            font-weight: 500;
            letter-spacing: 0.03em;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
            font-size: 16px;
        }
        
        .auth-link:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all dropdown elements
        const dropdowns = document.querySelectorAll('.dropdown');
        
        dropdowns.forEach(dropdown => {
            const dropdownContent = dropdown.querySelector('.dropdown-content');
            let timeoutId;
            
            // Show dropdown on hover with a single unified approach
            dropdown.addEventListener('mouseenter', () => {
                clearTimeout(timeoutId);
                dropdownContent.style.display = 'block';
                // Small delay to ensure CSS transition works
                requestAnimationFrame(() => {
                    dropdownContent.style.opacity = '1';
                    dropdownContent.style.transform = 'translateY(0)';
                });
            });
            
            // Hide dropdown when mouse leaves the entire dropdown component
            dropdown.addEventListener('mouseleave', () => {
                timeoutId = setTimeout(() => {
                    dropdownContent.style.opacity = '0';
                    dropdownContent.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        dropdownContent.style.display = 'none';
                    }, 300);
                }, 100); // Reduced delay for better responsiveness
            });
        });
    });
</script>
<body class="bg-gray-100">

    <!-- Navbar -->
    <header class="bg-indigo-400 py-4 px-8 flex items-center justify-between shadow-md">
        <div class="flex items-center">
            <a href="{{ route('outfits.index') }}" class="logo">ThaiWijit</a>
            
            <!-- Menu with Product dropdown -->
            <nav class="flex space-x-6 text-white">
                <div class="dropdown">
                    <a href="#" class="nav-link flex items-center">
                        Products <i class="fas fa-chevron-down ml-2 text-xs opacity-70"></i>
                    </a>
                    <!-- Replace line 173 and surrounding context with this -->
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item" data-category="all">All Products</a>
                    
                    @foreach($categories ?? [] as $category)
                        <a href="#" class="dropdown-item" data-category="{{ $category->category_id }}">
                            {{ $category->category_name }}
                        </a>
                    @endforeach
                </div>

                </div>
            </nav>
        </div>

        <!-- Search Form -->
        <form action="{{ route('outfits.search') }}" method="GET" class="flex-grow mx-12 max-w-2xl">
            <div class="flex gap-3">
                <input type="text" name="searchkey" placeholder="ค้นหาชุดไทย..." class="search-input">
                <button type="submit" class="search-button">
                    <i class="fas fa-search mr-2"></i> ค้นหา
                </button>
            </div>
        </form>

        <!-- Icons and Auth -->
        <div class="flex items-center space-x-6 text-white">
            <a href="{{ route('cartItem.allItem') }}" class="icon-link relative">
                <i class="fa fa-shopping-cart text-xl"></i>
            </a>
            @guest
                <a href="{{ route('login') }}" class="auth-link hover:text-white/90">Login</a>
                <a href="{{ route('register') }}" class="auth-link bg-white text-indigo-500 rounded-full px-5 py-2 hover:bg-opacity-90">Register</a>
            @else
                <a href="{{ route('profile.index') }}" class="auth-link hover:text-white/90">Profile</a>
                <a href="{{ route('logout') }}" class="auth-link hover:text-white/90"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endguest
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        @yield('content')
    </div>
    @stack('scripts') {{-- <-- แทรกไว้ตรงนี้ --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dropdown behavior code (keep your existing code)
        
        // Add category filtering functionality
        const categoryLinks = document.querySelectorAll('.dropdown-item[data-category]');
        const contentContainer = document.querySelector('.container'); // Adjust this selector to match your main content container
        
        categoryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const categoryId = this.getAttribute('data-category');
                let url = '{{ route("outfits.index") }}';
                
                // Add category parameter if not "all"
                if (categoryId !== 'all') {
                    url += '?category=' + categoryId;
                }
                
                // Show loading indicator
                const loadingIndicator = document.createElement('div');
                loadingIndicator.className = 'text-center py-12';
                loadingIndicator.innerHTML = '<div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>';
                
                contentContainer.innerHTML = '';
                contentContainer.appendChild(loadingIndicator);
                
                // Fetch filtered results
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Create a temporary element to parse the HTML
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    // Extract just the content we need (adjust the selector as needed)
                    const newContent = tempDiv.querySelector('.container');
                    if (newContent) {
                        contentContainer.innerHTML = newContent.innerHTML;
                    } else {
                        contentContainer.innerHTML = html;
                    }
                    
                    // Update active category styling
                    categoryLinks.forEach(item => item.classList.remove('font-bold', 'text-indigo-600', 'border-l-indigo-500'));
                    this.classList.add('font-bold', 'text-indigo-600', 'border-l-indigo-500');
                })
                .catch(error => {
                    console.error('Error fetching filtered results:', error);
                    contentContainer.innerHTML = '<div class="text-center text-red-500 p-4">Error loading products. Please try again.</div>';
                });
            });
        });
    });
</script>

</body>
</html>
