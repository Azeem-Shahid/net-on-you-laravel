<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Authentication') - {{ config('app.name', 'Net On You') }}</title>

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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-primary">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-action/20 border border-action text-action px-4 py-3 rounded-lg z-50" role="alert">
            {{ session('success') }}
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-action hover:text-action/80" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-danger/20 border border-danger text-danger px-4 py-3 rounded-lg z-50" role="alert">
            {{ session('error') }}
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-danger hover:text-danger/80" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="fixed top-4 right-4 bg-yellow-500/20 border border-yellow-500 text-yellow-400 px-4 py-3 rounded-lg z-50" role="alert">
            {{ session('warning') }}
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-yellow-400 hover:text-yellow-300" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="fixed top-4 right-4 bg-blue-500/20 border border-blue-500 text-blue-400 px-4 py-3 rounded-lg z-50" role="alert">
            {{ session('info') }}
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-blue-400 hover:text-blue-300" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Page Content -->
    @yield('content')
    
    @stack('scripts')
</body>
</html>
