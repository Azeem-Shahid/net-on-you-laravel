<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'NetOnYou') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Language Handler for GTranslate Integration -->
    <script src="{{ asset('js/language-handler.js') }}"></script>
    


    
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
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Header with GTranslate Widget -->
        <x-admin-header />
        
        <!-- Sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-primary px-6 pb-4 border-r border-action/20">
                <div class="flex h-16 shrink-0 items-center">
                    <h1 class="text-xl font-bold text-action">NetOnYou Admin</h1>
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        Users
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.magazines.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.magazines.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.magazines.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        Magazines
                                    </a>
                                </li>
                                                            <li>
                                <a href="{{ route('admin.transactions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.transactions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.transactions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                    </svg>
                                    Transactions
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.subscriptions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.subscriptions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.subscriptions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Subscriptions
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.referrals.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.referrals.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.referrals.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Referrals
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.commissions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.commissions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.commissions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    Commissions
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.payouts.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.payouts.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.payouts.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Payouts
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.analytics.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.analytics.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.analytics.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Analytics
                                </a>
                            </li>
                            
                            <!-- Email & Communication Section -->
                            <li class="pt-6">
                                <div class="text-xs font-semibold text-action/60 uppercase tracking-wider">Communication</div>
                            </li>
                            <li>
                                <a href="{{ route('admin.email-templates.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.email-templates.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.email-templates.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Email Templates
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.email-logs.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.email-logs.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.email-logs.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Email Logs
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.campaigns.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.campaigns.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.campaigns.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6" />
                                    </svg>
                                    Campaigns
                                </a>
                            </li>
                                <li>
                                    <a href="{{ route('admin.subscriptions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.subscriptions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.subscriptions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Subscriptions
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.referrals.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.referrals.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.referrals.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Referrals
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.commissions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.commissions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.commissions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        Commissions
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.payouts.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.payouts.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.payouts.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Payouts
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.analytics.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.analytics.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.analytics.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Analytics
                                    </a>
                                </li>
                                
                                <!-- Email & Communication Section -->
                                <li class="pt-6">
                                    <div class="text-xs font-semibold text-action/60 uppercase tracking-wider">Communication</div>
                                </li>
                                <li>
                                    <a href="{{ route('admin.email-templates.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.email-templates.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.email-templates.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Email Templates
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.email-logs.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.email-logs.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.email-logs.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Email Logs
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.campaigns.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.campaigns.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.campaigns.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6" />
                                        </svg>
                                        Campaigns
                                    </a>
                                </li>
                                
                                <!-- Language Management Section -->
                                <li class="pt-6">
                                    <div class="text-xs font-semibold text-action/60 uppercase tracking-wider">Language Management</div>
                                </li>
                                <li>
                                    <a href="{{ route('admin.languages.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.languages.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.languages.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                        </svg>
                                        Languages
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.translations.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.translations.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.translations.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        Translations
                                    </a>
                                </li>
                                
                                <!-- Security & Settings Section -->
                                @if(auth('admin')->user()->hasPermission('settings.manage') || auth('admin')->user()->hasPermission('security.manage'))
                                <li class="pt-6">
                                    <div class="text-xs font-semibold text-action/60 uppercase tracking-wider">Security & Settings</div>
                                </li>
                                @endif
                                
                                @if(auth('admin')->user()->hasPermission('settings.manage'))
                                <li>
                                    <a href="{{ route('admin.settings.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Settings
                                    </a>
                                </li>
                                @endif
                                
                                @if(auth('admin')->user()->hasPermission('security.manage'))
                                <li>
                                    <a href="{{ route('admin.security.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.security.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.security.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                        Security
                                    </a>
                                </li>
                                @endif
                                
                                @if(auth('admin')->user()->hasPermission('roles.manage'))
                                <li>
                                    <a href="{{ route('admin.roles.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.roles.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.roles.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        Roles
                                    </a>
                                </li>
                                @endif
                                
                                @if(auth('admin')->user()->hasPermission('api_keys.manage'))
                                <li>
                                    <a href="{{ route('admin.api-keys.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.api-keys.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.api-keys.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1.001.43-1.563A6 6 0 1121.75 8.25z" />
                                        </svg>
                                        API Keys
                                    </a>
                                </li>
                                @endif
                                
                                @if(auth('admin')->user()->hasPermission('sessions.manage'))
                                <li>
                                    <a href="{{ route('admin.sessions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.sessions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.sessions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15m-12 0l3-3m0 0l3 3m-3-3V9" />
                                        </svg>
                                        Sessions
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        <li class="mt-auto">
                            <div class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-white">
                                <div class="flex items-center gap-x-4">
                                    <div class="w-8 h-8 bg-action rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-primary">{{ substr(auth()->user()->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-action/80">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Mobile menu button -->
        <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-primary px-4 py-4 shadow-sm sm:px-6 lg:hidden">
            <button type="button" class="-m-2.5 p-2.5 text-action lg:hidden" id="mobile-menu-button">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <div class="flex-1 text-sm font-semibold leading-6 text-action">NetOnYou Admin</div>
            <div class="flex items-center gap-x-4">
                <div class="w-8 h-8 bg-action rounded-full flex items-center justify-center">
                    <span class="text-xs font-medium text-primary">{{ substr(auth()->user()->name, 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div class="lg:hidden" id="mobile-sidebar" style="display: none;">
            <div class="fixed inset-0 z-50">
                <div class="fixed inset-0 bg-gray-900/80" id="mobile-sidebar-backdrop"></div>
                <div class="fixed inset-y-0 left-0 z-50 w-full overflow-y-auto bg-primary px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-action/20">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-action">NetOnYou Admin</h2>
                        <button type="button" class="-m-2.5 rounded-md p-2.5 text-action" id="mobile-menu-close">
                            <span class="sr-only">Close menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <nav class="mt-6">
                        <ul role="list" class="space-y-2">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    Users
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.magazines.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.magazines.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.magazines.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    Magazines
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.transactions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.transactions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.transactions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                    </svg>
                                    Transactions
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.subscriptions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.subscriptions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.subscriptions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Subscriptions
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.referrals.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.referrals.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.referrals.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Referrals
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.commissions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.commissions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.commissions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    Commissions
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.payouts.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.payouts.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.payouts.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Payouts
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.analytics.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.analytics.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.analytics.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Analytics
                                </a>
                            </li>
                            
                            <!-- Email & Communication Section -->
                            <li class="pt-6">
                                <div class="text-xs font-semibold text-action/60 uppercase tracking-wider">Communication</div>
                            </li>
                            <li>
                                <a href="{{ route('admin.email-templates.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.email-templates.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.email-templates.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Email Templates
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.email-logs.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.email-logs.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.email-logs.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Email Logs
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.campaigns.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.campaigns.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.campaigns.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6M5.882 19.24a1.76 1.76 0 01-3.417-.592l2.147-6.15M18 13a3 3 0 100-6" />
                                    </svg>
                                    Campaigns
                                </a>
                            </li>
                            
                            <!-- Language Management Section -->
                            <li class="pt-6">
                                <div class="text-xs font-semibold text-action/60 uppercase tracking-wider">Language Management</div>
                            </li>
                            <li>
                                <a href="{{ route('admin.languages.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.languages.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.languages.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                    </svg>
                                    Languages
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.translations.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.translations.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.translations.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                    Translations
                                </a>
                            </li>
                            
                            <!-- Security & Settings Section -->
                            @if(auth('admin')->user()->hasPermission('settings.manage') || auth('admin')->user()->hasPermission('security.manage'))
                            <li class="pt-6">
                                <div class="text-xs font-semibold text-action/60 uppercase tracking-wider">Security & Settings</div>
                            </li>
                            @endif
                            
                            @if(auth('admin')->user()->hasPermission('settings.manage'))
                            <li>
                                <a href="{{ route('admin.settings.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Settings
                                </a>
                            </li>
                            @endif
                            
                            @if(auth('admin')->user()->hasPermission('security.manage'))
                            <li>
                                <a href="{{ route('admin.security.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.security.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.security.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                    Security
                                </a>
                            </li>
                            @endif
                            
                            @if(auth('admin')->user()->hasPermission('roles.manage'))
                            <li>
                                <a href="{{ route('admin.roles.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.roles.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.roles.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    Roles
                                </a>
                            </li>
                            @endif
                            
                            @if(auth('admin')->user()->hasPermission('api_keys.manage'))
                            <li>
                                <a href="{{ route('admin.api-keys.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.api-keys.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.api-keys.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1.001.43-1.563A6 6 0 1121.75 8.25z" />
                                    </svg>
                                    API Keys
                                </a>
                            </li>
                            @endif
                            
                            @if(auth('admin')->user()->hasPermission('sessions.manage'))
                            <li>
                                <a href="{{ route('admin.sessions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.sessions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.sessions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15m-12 0l3-3m0 0l3 3m-3-3V9" />
                                    </svg>
                                    Sessions
                                </a>
                            </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Admin Header with Language Switcher -->
            <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-xl font-semibold text-gray-900">{{ t('admin_panel', [], 'admin') }}</h2>
                        <span class="text-sm text-gray-500">{{ t('welcome_back', [], 'admin') }}, {{ auth('admin')->user()->name }}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Custom Language Widget -->
                        @include('components.custom-language-widget')
                            

                        
                        <!-- Admin Info -->
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ auth('admin')->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth('admin')->user()->role }}</p>
                            </div>
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <span class="text-sm font-bold text-action">{{ substr(auth('admin')->user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        
                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-danger hover:bg-danger/90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                {{ t('logout', [], 'auth') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <main class="py-10">
                @if(session('success'))
                    <div class="mb-4 mx-4 sm:mx-6 lg:mx-8">
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 mx-4 sm:mx-6 lg:mx-8">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileSidebarBackdrop = document.getElementById('mobile-sidebar-backdrop');

            function openMobileMenu() {
                mobileSidebar.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

            function closeMobileMenu() {
                mobileSidebar.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            mobileMenuButton.addEventListener('click', openMobileMenu);
            mobileMenuClose.addEventListener('click', closeMobileMenu);
            mobileSidebarBackdrop.addEventListener('click', closeMobileMenu);

            // Close mobile menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                }
            });
        });
    </script>

    @stack('scripts')
    
    <!-- Footer for Admin Layout - Show on all admin pages -->
    <x-footer />
</body>
</html>
