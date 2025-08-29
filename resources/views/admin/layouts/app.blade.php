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
                                        {{ t('dashboard', [], 'admin') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        {{ t('users', [], 'admin') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.magazines.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.magazines.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.magazines.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        {{ t('magazines', [], 'admin') }}
                                    </a>
                                </li>
                                                            <li>
                                <a href="{{ route('admin.transactions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.transactions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.transactions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                    </svg>
                                    {{ t('transactions', [], 'admin') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.subscriptions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.subscriptions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.subscriptions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ t('subscriptions', [], 'admin') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.referrals.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.referrals.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.referrals.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    {{ t('referrals', [], 'admin') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.commissions.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.commissions.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h6 w-6 shrink-0 {{ request()->routeIs('admin.commissions.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1"></path>
                                    </svg>
                                    {{ t('commissions', [], 'admin') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.payouts.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.payouts.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.payouts.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    {{ t('payouts', [], 'admin') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.analytics.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.analytics.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.analytics.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    {{ t('analytics', [], 'admin') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.settings.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-action/20 text-action' : 'text-white hover:text-action hover:bg-action/10' }}">
                                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-action' : 'text-white group-hover:text-action' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ t('settings', [], 'admin') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="lg:pl-64 flex-1 flex flex-col">
        <!-- Page Content -->
        <main class="flex-1 py-8">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
