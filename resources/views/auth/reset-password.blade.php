@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-action">
                Set New Password
            </h2>
            <p class="mt-2 text-sm text-white/80">
                Enter your new password below
            </p>
        </div>

        <!-- Custom Language Widget -->
        <div class="flex justify-center">
            @include('components.custom-language-widget')
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.update') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white">
                        Email Address
                    </label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="Enter your email address"
                           value="{{ $email ?? old('email') }}">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        New Password
                    </label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="Enter new password">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white">
                        Confirm New Password
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="Confirm new password">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-primary bg-action hover:bg-action/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action transition-all transform hover:scale-105">
                    Reset Password
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-white/80">
                    Remember your password? 
                    <a href="{{ route('login') }}" class="font-medium text-action hover:text-action/80 transition-colors">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
