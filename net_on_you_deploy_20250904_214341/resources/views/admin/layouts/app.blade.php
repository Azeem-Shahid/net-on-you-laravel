<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Net On You') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
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
        
        .admin-container {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        .admin-sidebar {
            min-width: 16rem;
            max-width: 16rem;
        }
        
        .admin-content {
            min-width: 0;
            flex: 1;
        }
        
        /* Ensure tables don't cause overflow */
        .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Responsive sidebar */
        @media (max-width: 1024px) {
            .admin-sidebar {
                min-width: 100%;
                max-width: 100%;
            }
            
            .admin-content {
                min-width: 100%;
            }
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                position: fixed;
                z-index: 50;
            }
        }
        
        /* Prevent text overflow */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex admin-container">
        <!-- Admin Sidebar -->
        <nav class="bg-primary min-h-screen admin-sidebar flex-shrink-0 lg:block hidden" id="adminSidebar">
            <div class="p-6">
                <div class="mb-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-action text-2xl font-bold hover:text-white transition-colors">
                        <i class="fas fa-shield-alt mr-3"></i>
                        Admin Panel
                    </a>
        </div>

                <ul class="space-y-2">
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.analytics.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.analytics.index') }}">
                            <i class="fas fa-chart-line mr-3"></i>
                            Analytics
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.users.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users mr-3"></i>
                                        Users
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.magazines.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.magazines.index') }}">
                            <i class="fas fa-book mr-3"></i>
                                        Magazines
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.subscriptions.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.subscriptions.index') }}">
                            <i class="fas fa-credit-card mr-3"></i>
                            Subscriptions
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.transactions.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.transactions.index') }}">
                            <i class="fas fa-exchange-alt mr-3"></i>
                            Transactions
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.referrals.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.referrals.index') }}">
                            <i class="fas fa-share-alt mr-3"></i>
                                        Referrals
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.commissions.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.commissions.index') }}">
                            <i class="fas fa-percentage mr-3"></i>
                                        Commissions
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.payouts.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.payouts.index') }}">
                            <i class="fas fa-money-bill-wave mr-3"></i>
                                        Payouts
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.email-templates.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.email-templates.index') }}">
                            <i class="fas fa-envelope mr-3"></i>
                                        Email Templates
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.email-logs.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.email-logs.index') }}">
                            <i class="fas fa-list-alt mr-3"></i>
                                        Email Logs
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.campaigns.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.campaigns.index') }}">
                            <i class="fas fa-bullhorn mr-3"></i>
                                        Campaigns
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.contracts.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.contracts.index') }}">
                            <i class="fas fa-file-contract mr-3"></i>
                                        Contracts
                                    </a>
                                </li>
                    {{-- <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.languages.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.languages.index') }}">
                            <i class="fas fa-language mr-3"></i>
                                        Languages
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.translations.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.translations.index') }}">
                            <i class="fas fa-translate mr-3"></i>
                                        Translations
                                    </a>
                                </li> --}}
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.settings.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog mr-3"></i>
                                        Settings
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.security.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.security.index') }}">
                            <i class="fas fa-lock mr-3"></i>
                                        Security
                                    </a>
                                </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.command-scheduler.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.command-scheduler.index') }}">
                            <i class="fas fa-clock mr-3"></i>
                                        System Scheduler
                                    </a>
                                </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.cron-jobs.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.cron-jobs.index') }}">
                            <i class="fas fa-cogs mr-3"></i>
                                        Cron Job Management
                                    </a>
                                </li>
                </ul>
            </div>
        </nav>

        <!-- Mobile Sidebar Overlay -->
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden hidden" id="sidebarOverlay"></div>

        <!-- Mobile Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 admin-sidebar bg-primary transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden" id="mobileSidebar">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-action text-2xl font-bold hover:text-white transition-colors">
                        <i class="fas fa-shield-alt mr-3"></i>
                        Admin Panel
                    </a>
                    <button class="text-white hover:text-action lg:hidden" id="closeSidebar">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <ul class="space-y-2">
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.dashboard') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.analytics.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.analytics.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-chart-line mr-3"></i>
                            Analytics
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.users.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.users.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-users mr-3"></i>
                            Users
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.magazines.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.magazines.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-book mr-3"></i>
                            Magazines
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.subscriptions.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.subscriptions.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-credit-card mr-3"></i>
                            Subscriptions
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.transactions.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.transactions.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-exchange-alt mr-3"></i>
                            Transactions
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.referrals.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.referrals.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-share-alt mr-3"></i>
                            Referrals
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.commissions.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.commissions.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-percentage mr-3"></i>
                            Commissions
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.payouts.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.payouts.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-money-bill-wave mr-3"></i>
                            Payouts
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.email-templates.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.email-templates.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-envelope mr-3"></i>
                            Email Templates
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.email-logs.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.email-logs.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-list-alt mr-3"></i>
                            Email Logs
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.campaigns.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.campaigns.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-bullhorn mr-3"></i>
                            Campaigns
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.contracts.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.contracts.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-file-contract mr-3"></i>
                            Contracts
                        </a>
                    </li>
                    {{-- <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.languages.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.languages.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-language mr-3"></i>
                            Languages
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.translations.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.translations.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-translate mr-3"></i>
                            Translations
                                    </a>
                                </li> --}}
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.settings.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.settings.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-cog mr-3"></i>
                            Settings
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.security.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.security.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-lock mr-3"></i>
                            Security
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.command-scheduler.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.command-scheduler.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-clock mr-3"></i>
                            System Scheduler
                                    </a>
                                </li>
                                <li>
                        <a class="flex items-center px-4 py-3 text-white/80 hover:text-action hover:bg-white/10 rounded-lg transition-all {{ request()->routeIs('admin.cron-jobs.*') ? 'text-action bg-white/10' : '' }}" href="{{ route('admin.cron-jobs.index') }}" onclick="closeMobileSidebar()">
                            <i class="fas fa-cogs mr-3"></i>
                            Cron Job Management
                                    </a>
                                </li>
                            </ul>
            </div>
        </div>

        <!-- Main Content -->
        <main class="admin-content bg-white min-h-screen">
            <!-- Admin Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center min-w-0">
                        <!-- Mobile menu button -->
                        <button class="lg:hidden mr-4 text-gray-600 hover:text-gray-900" id="mobileMenuButton">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-900 truncate">@yield('title', 'Admin Dashboard')</h1>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <!-- Custom Language Widget -->
                        @include('components.custom-language-widget')
                        
                        <div class="relative">
                            <button class="flex items-center px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-action focus:ring-offset-2" id="adminDropdown">
                                <i class="fas fa-user-shield mr-1 sm:mr-2"></i>
                                <span class="hidden sm:inline">{{ auth('admin')->user()->name ?? 'Admin' }}</span>
                                <i class="fas fa-chevron-down ml-1 sm:ml-2"></i>
                            </button>
                            <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50" id="adminDropdownMenu">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                <hr class="border-gray-200">
                                <form method="POST" action="{{ route('admin.logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-4 sm:mx-6 mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    {{ session('success') }}
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-4 sm:mx-6 mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    {{ session('error') }}
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="mx-4 sm:mx-6 mt-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg relative" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-yellow-700 hover:text-yellow-900" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div class="mx-4 sm:mx-6 mt-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative" role="alert">
                    {{ session('info') }}
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-blue-700 hover:text-blue-900" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Page Content -->
            <div class="px-4 sm:px-6 py-6 overflow-hidden">
                @yield('content')
            </div>
            </main>
        
    </div>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin Scripts -->
    <script>
        // Mobile sidebar functionality
        function openMobileSidebar() {
            document.getElementById('mobileSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.remove('hidden');
        }

        function closeMobileSidebar() {
            document.getElementById('mobileSidebar').classList.add('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }

        // Mobile menu button
        document.getElementById('mobileMenuButton').addEventListener('click', openMobileSidebar);

        // Close sidebar button
        document.getElementById('closeSidebar').addEventListener('click', closeMobileSidebar);

        // Close sidebar when clicking overlay
        document.getElementById('sidebarOverlay').addEventListener('click', closeMobileSidebar);

        // Dropdown functionality
        document.getElementById('adminDropdown').addEventListener('click', function() {
            const menu = document.getElementById('adminDropdownMenu');
            menu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('adminDropdown');
            const menu = document.getElementById('adminDropdownMenu');
            
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
