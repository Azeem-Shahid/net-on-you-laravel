<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Net On You') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1d003f',
                        action: '#00ff00',
                        danger: '#ff0000'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS to prevent overflow -->
    <style>
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }
        
        .container {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        @media (max-width: 768px) {
            .nav-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- User Navigation -->
    <nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 nav-container">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-action text-xl sm:text-2xl font-bold hover:text-white transition-colors whitespace-nowrap">
                        <i class="fas fa-home mr-2 sm:mr-3"></i>
                        {{ config('app.name', 'Net On You') }}
                    </a>
                </div>
                
                <!-- Medium screen navigation (icons only) -->
                <div class="hidden md:block lg:hidden">
                    <div class="ml-6 flex items-baseline space-x-1">
                        <a href="{{ route('dashboard') }}" class="text-white/80 hover:text-action p-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-action bg-white/10' : '' }}" title="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                        </a>
                        <a href="{{ route('magazines.index') }}" class="text-white/80 hover:text-action p-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('magazines.*') ? 'text-action bg-white/10' : '' }}" title="Magazines">
                            <i class="fas fa-book"></i>
                        </a>
                        <a href="{{ route('transactions.index') }}" class="text-white/80 hover:text-action p-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('transactions.*') ? 'text-action bg-white/10' : '' }}" title="Transactions">
                            <i class="fas fa-exchange-alt"></i>
                        </a>
                        <a href="{{ route('payment.history') }}" class="text-white/80 hover:text-action p-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('payment.*') ? 'text-action bg-white/10' : '' }}" title="Payments">
                            <i class="fas fa-credit-card"></i>
                        </a>
                        <a href="{{ route('referrals.index') }}" class="text-white/80 hover:text-action p-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('referrals.*') ? 'text-action bg-white/10' : '' }}" title="Referrals">
                            <i class="fas fa-users"></i>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-white/80 hover:text-action p-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('profile.*') ? 'text-action bg-white/10' : '' }}" title="Profile">
                            <i class="fas fa-user-edit"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Large screen navigation (icons + text) -->
                <div class="hidden lg:block">
                    <div class="ml-10 flex items-baseline space-x-2">
                        <a href="{{ route('dashboard') }}" class="text-white/80 hover:text-action px-2 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-action bg-white/10' : '' }}">
                            <i class="fas fa-tachometer-alt mr-1"></i>
                            <span class="hidden xl:inline">Dashboard</span>
                        </a>
                        <a href="{{ route('magazines.index') }}" class="text-white/80 hover:text-action px-2 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('magazines.*') ? 'text-action bg-white/10' : '' }}">
                            <i class="fas fa-book mr-1"></i>
                            <span class="hidden xl:inline">Magazines</span>
                        </a>
                        <a href="{{ route('transactions.index') }}" class="text-white/80 hover:text-action px-2 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('transactions.*') ? 'text-action bg-white/10' : '' }}">
                            <i class="fas fa-exchange-alt mr-1"></i>
                            <span class="hidden xl:inline">Transactions</span>
                        </a>
                        <a href="{{ route('payment.history') }}" class="text-white/80 hover:text-action px-2 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('payment.*') ? 'text-action bg-white/10' : '' }}">
                            <i class="fas fa-credit-card mr-1"></i>
                            <span class="hidden xl:inline">Payments</span>
                        </a>
                        <a href="{{ route('referrals.index') }}" class="text-white/80 hover:text-action px-2 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('referrals.*') ? 'text-action bg-white/10' : '' }}">
                            <i class="fas fa-users mr-1"></i>
                            <span class="hidden xl:inline">Referrals</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-white/80 hover:text-action px-2 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('profile.*') ? 'text-action bg-white/10' : '' }}">
                            <i class="fas fa-user-edit mr-1"></i>
                            <span class="hidden xl:inline">Profile</span>
                        </a>
                    </div>
                </div>
                
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        <!-- Custom Language Widget -->
                        @include('components.custom-language-widget')
                        
                        <div class="relative">
                            <button class="flex items-center text-white/80 hover:text-action px-3 py-2 rounded-md text-sm font-medium transition-colors" id="userDropdown">
                                <i class="fas fa-user mr-2"></i>
                                {{ auth()->user()->name ?? 'User' }}
                                <i class="fas fa-chevron-down ml-2"></i>
                            </button>
                            <div class="hidden absolute right-0 mt-2 w-48 bg-primary border border-action/30 rounded-lg shadow-lg z-50" id="userDropdownMenu">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-white hover:bg-action/20 rounded-t-lg transition-colors">
                                    <i class="fas fa-user-edit mr-2"></i>Edit Profile
                                </a>
                                <a href="{{ route('profile.change-password') }}" class="block px-4 py-2 text-sm text-white hover:bg-action/20 transition-colors">
                                    <i class="fas fa-key mr-2"></i>Change Password
                                </a>
                                <hr class="border-action/30">
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-white hover:bg-action/20 rounded-b-lg transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="text-white/80 hover:text-action p-2 rounded-md" id="mobileMenuButton">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="hidden md:hidden" id="mobileMenu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('dashboard') }}" class="text-white/80 hover:text-action block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'text-action bg-white/10' : '' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="{{ route('magazines.index') }}" class="text-white/80 hover:text-action block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('magazines.*') ? 'text-action bg-white/10' : '' }}">
                    <i class="fas fa-book mr-2"></i>Magazines
                </a>
                <a href="{{ route('transactions.index') }}" class="text-white/80 hover:text-action block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('transactions.*') ? 'text-action bg-white/10' : '' }}">
                    <i class="fas fa-exchange-alt mr-2"></i>Transactions
                </a>
                <a href="{{ route('payment.history') }}" class="text-white/80 hover:text-action block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('payment.*') ? 'text-action bg-white/10' : '' }}">
                    <i class="fas fa-credit-card mr-2"></i>Payments
                </a>
                <a href="{{ route('referrals.index') }}" class="text-white/80 hover:text-action block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('referrals.*') ? 'text-action bg-white/10' : '' }}">
                    <i class="fas fa-users mr-2"></i>Referrals
                </a>
                <a href="{{ route('profile.edit') }}" class="text-white/80 hover:text-action block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('profile.*') ? 'text-action bg-white/10' : '' }}">
                    <i class="fas fa-user-edit mr-2"></i>Profile
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-action/20 border border-action text-action px-4 py-3 rounded-lg relative" role="alert">
                    {{ session('success') }}
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-action hover:text-action/80" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-danger/20 border border-danger text-danger px-4 py-3 rounded-lg relative" role="alert">
                    {{ session('error') }}
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-danger hover:text-danger/80" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 bg-yellow-500/20 border border-yellow-500 text-yellow-400 px-4 py-3 rounded-lg relative" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-yellow-400 hover:text-yellow-300" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 bg-blue-500/20 border border-blue-500 text-blue-400 px-4 py-3 rounded-lg relative" role="alert">
                    {{ session('info') }}
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-blue-400 hover:text-blue-300" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom User Scripts -->
    <script>
        // Dropdown functionality
        document.getElementById('userDropdown').addEventListener('click', function() {
            const menu = document.getElementById('userDropdownMenu');
            menu.classList.toggle('hidden');
        });

        // Mobile menu functionality
        document.getElementById('mobileMenuButton').addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const menu = document.getElementById('userDropdownMenu');
            
            if (!dropdown.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>
