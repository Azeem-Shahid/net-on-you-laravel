<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - NetOnYou</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
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
</head>
<body class="bg-primary min-h-screen">

<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-action">
                Admin Access
            </h2>
            <p class="mt-2 text-sm text-white/80">
                Sign in to access admin panel
            </p>
        </div>

        <!-- Custom Language Widget -->
        <div class="flex justify-center">
            @include('components.custom-language-widget')
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Success Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('admin.login.submit') }}" id="adminLoginForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white">
                        Admin Email
                    </label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="Enter admin email address"
                           value="{{ old('email') }}">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        Admin Password
                    </label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="Enter admin password">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-action focus:ring-action border-action/30 rounded bg-primary/50">
                    <label for="remember" class="ml-2 block text-sm text-white">
                        Remember me
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-primary bg-action hover:bg-action/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action transition-all transform hover:scale-105">
                    Access Admin Panel
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-white/80">
                    Regular user? 
                    <a href="{{ route('login') }}" class="font-medium text-action hover:text-action/80 transition-colors">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
    console.log('Form submitted');
    console.log('Email:', document.getElementById('email').value);
    console.log('Password:', document.getElementById('password').value);
    console.log('CSRF Token:', document.querySelector('input[name="_token"]').value);
    
    // Don't prevent default - let the form submit normally
});

// Also log when the page loads
console.log('Admin login page loaded');
console.log('CSRF Token:', document.querySelector('input[name="_token"]').value);
</script>
</body>
</html>
