@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex flex-col bg-primary">
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-action">
                    {{ t('welcome_back', [], 'auth') }}
                </h2>
                <p class="mt-2 text-sm text-white/80">
                    {{ t('sign_in_to_continue', [], 'auth') }}
                </p>
            </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white">
                        {{ t('email', [], 'common') }}
                    </label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_email', [], 'auth') }}"
                           value="{{ old('email') }}">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        {{ t('password', [], 'common') }}
                    </label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_password', [], 'auth') }}">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 text-action focus:ring-action border-action/30 rounded bg-primary/50">
                        <label for="remember" class="ml-2 block text-sm text-white">
                            {{ t('remember_me', [], 'auth') }}
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-action hover:text-action/80 transition-colors">
                            {{ t('forgot_password', [], 'auth') }}
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-primary bg-action hover:bg-action/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action transition-all transform hover:scale-105">
                    {{ t('sign_in', [], 'auth') }}
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-white/80">
                    {{ t('dont_have_account', [], 'auth') }} 
                    <a href="{{ route('register') }}" class="font-medium text-action hover:text-action/80 transition-colors">
                        {{ t('register_here', [], 'auth') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
