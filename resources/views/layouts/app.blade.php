<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NetOnYou') }} - @yield('title', 'Authentication') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN for development -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1d003f',
                        action: '#00ff00',
                        error: '#ff0000'
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-primary to-[#2a0057] flex flex-col">
        @if(auth()->check())
        <!-- Sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-primary px-6 pb-4 border-r border-action/20">
                <div class="flex h-16 shrink-0 items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-action">
                        NetOnYou
                    </a>
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <a href="{{ route('dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        {{ t('dashboard', [], 'common') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('magazines.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('magazines.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('magazines.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.967 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.967 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        {{ t('magazines', [], 'common') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('payment.history') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('payment.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('payment.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                        </svg>
                                        {{ t('payments', [], 'common') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('transactions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('transactions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('transactions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        {{ t('transactions', [], 'common') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('profile.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('profile.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        {{ t('profile', [], 'common') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('payment.checkout') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('payment.checkout') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('payment.checkout') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.967 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.967 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        {{ t('checkout', [], 'common') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard') }}#referrals" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-.758l1.102-1.101a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.102 1.101m-.758-.758l-1.102-1.101a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" />
                                        </svg>
                                        {{ t('referrals', [], 'common') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard') }}#commissions" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        {{ t('commissions', [], 'common') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="mt-auto">
                            <div class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-white">
                                <div class="flex items-center gap-x-4">
                                    <div class="w-8 h-8 bg-action rounded-full flex items-center justify-center">
                                        <span class="text-primary font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white">{{ auth()->user()->name }}</p>
                                        <p class="text-action/80 text-xs">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full text-left px-6 py-2 text-sm text-action hover:text-action/80 hover:bg-action/10 rounded-md transition-colors">
                                    {{ t('logout', [], 'auth') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:pl-64 flex-1 flex flex-col">
            <!-- Top navigation bar -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-action/20 bg-primary px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button type="button" class="-m-2.5 p-2.5 text-action lg:hidden" id="mobile-menu-button">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1"></div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- GTranslate Widget -->
                        @include('components.custom-language-widget')
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 py-8">
                @yield('content')
            </main>
        </div>

        <!-- Mobile sidebar overlay -->
        <div id="mobile-sidebar-overlay" 
             class="fixed inset-0 z-50 lg:hidden bg-black bg-opacity-50 cursor-pointer hidden">
        </div>

        <!-- Mobile sidebar -->
        <div id="mobile-sidebar" 
             class="fixed inset-y-0 left-0 z-50 w-80 bg-primary shadow-xl lg:hidden transform -translate-x-full transition-transform duration-300 ease-in-out">
            <div class="flex h-full flex-col">
                <!-- Mobile sidebar header -->
                <div class="flex h-16 shrink-0 items-center justify-between px-6 border-b border-action/20">
                    <h1 class="text-xl font-bold text-action">NetOnYou</h1>
                    <button type="button" class="-m-2.5 p-2.5 text-action hover:bg-action/10 rounded-md transition-colors" id="mobile-close-button">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile sidebar navigation -->
                <div class="flex flex-1 flex-col gap-y-5 overflow-y-auto px-6 pb-4">
                    <nav class="flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                            <li>
                                <ul role="list" class="-mx-2 space-y-1">
                                    <li>
                                        <a href="{{ route('dashboard') }}" 
                                           class="mobile-nav-link group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                            </svg>
                                            {{ t('dashboard', [], 'common') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('magazines.index') }}" 
                                           class="mobile-nav-link group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('magazines.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('magazines.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.967 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.967 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                            </svg>
                                            {{ t('magazines', [], 'common') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('payment.checkout') }}" class="mobile-nav-link group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('payment.checkout') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('payment.checkout') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.967 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.967 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                            </svg>
                                            {{ t('checkout', [], 'common') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard') }}#referrals" class="mobile-nav-link group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-.758l1.102-1.101a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.102 1.101m-.758-.758l-1.102-1.101a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" />
                                            </svg>
                                            {{ t('referrals', [], 'common') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard') }}#commissions" class="mobile-nav-link group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                            {{ t('commissions', [], 'common') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('payment.history') }}" 
                                           class="mobile-nav-link group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('payment.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('payment.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                            </svg>
                                            {{ t('payments', [], 'common') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('transactions.index') }}" 
                                           class="mobile-nav-link group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('transactions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('transactions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            {{ t('transactions', [], 'common') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('profile.edit') }}" 
                                           class="mobile-nav-link group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('profile.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('profile.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                            {{ t('profile', [], 'common') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="mt-auto">
                                <div class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-white border-t border-action/20">
                                    <div class="flex items-center gap-x-4">
                                        <div class="w-8 h-8 bg-action rounded-full flex items-center justify-center">
                                            <span class="text-primary font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-white">{{ auth()->user()->name }}</p>
                                            <p class="text-action/80 text-xs">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-6 py-2 text-sm text-action hover:text-action/80 hover:bg-action/10 rounded-md transition-colors">
                                        {{ t('logout', [], 'auth') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        @else
        <!-- Navigation for non-authenticated users -->
        <nav class="bg-primary border-b border-action/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="text-action font-bold text-xl">
                            NetOnYou
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- GTranslate Widget -->
                        @include('components.gtranslate-widget')
                        
                        <a href="{{ route('login') }}" class="text-action hover:text-action/80 transition-colors">
                            {{ t('login', [], 'auth') }}
                        </a>
                        <a href="{{ route('register') }}" class="bg-action text-primary px-4 py-2 rounded-lg font-medium hover:bg-action/80 transition-colors">
                            {{ t('register', [], 'auth') }}
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-1 py-8">
            @yield('content')
        </main>
        @endif

        <!-- Flash Messages with close functionality -->
        @if(session('success'))
            <x-notification type="success" :message="session('success')" />
        @endif

        @if(session('error'))
            <x-notification type="error" :message="session('error')" />
        @endif

        @if($errors->any())
            <x-notification type="error">
                <div>
                    <p class="text-sm font-medium">{{ t('error', [], 'common') }}:</p>
                    <ul class="list-disc list-inside mt-2 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </x-notification>
        @endif

        <!-- Footer for App Layout - Show on all pages -->
        <x-footer />
    </div>

    <!-- Vanilla JavaScript for sidebar functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
            const mobileCloseButton = document.getElementById('mobile-close-button');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
            
            let isSidebarOpen = false;

            // Function to open sidebar
            function openSidebar() {
                mobileSidebar.classList.remove('-translate-x-full');
                mobileSidebarOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                isSidebarOpen = true;
            }

            // Function to close sidebar
            function closeSidebar() {
                mobileSidebar.classList.add('-translate-x-full');
                mobileSidebarOverlay.classList.add('hidden');
                document.body.style.overflow = '';
                isSidebarOpen = false;
            }

            // Event listeners
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    if (isSidebarOpen) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (mobileCloseButton) {
                mobileCloseButton.addEventListener('click', closeSidebar);
            }

            if (mobileSidebarOverlay) {
                mobileSidebarOverlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on navigation links
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', closeSidebar);
            });

            // Close sidebar when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isSidebarOpen) {
                    closeSidebar();
                }
            });

            // Close sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (isSidebarOpen && 
                    !mobileSidebar.contains(e.target) && 
                    !mobileMenuButton.contains(e.target)) {
                    closeSidebar();
                }
            });

            // Close sidebar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024 && isSidebarOpen) {
                    closeSidebar();
                }
            });

            // Force close sidebar on page load for mobile
            if (window.innerWidth < 1024) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
