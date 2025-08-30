@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex flex-col">
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-action">
                    {{ t('create_account', [], 'auth') }}
                </h2>
                <p class="mt-2 text-sm text-white/80">
                    {{ t('join_netonyou', [], 'auth') }}
                </p>
            </div>

            <!-- Custom Language Widget -->
            <div class="flex justify-center">
                @include('components.custom-language-widget')
            </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-white">
                        {{ t('full_name', [], 'auth') }}
                    </label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_full_name', [], 'auth') }}"
                           value="{{ old('name') }}">
                </div>

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
                        {{ t('password', [], 'auth') }}
                    </label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_password', [], 'auth') }}">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white">
                        {{ t('confirm_password', [], 'auth') }}
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('confirm_password_placeholder', [], 'auth') }}">
                </div>

                <!-- Wallet Address -->
                <div>
                    <label for="wallet_address" class="block text-sm font-medium text-white">
                        {{ t('wallet_address', [], 'dashboard') }} ({{ t('optional', [], 'common') }})
                    </label>
                    <input id="wallet_address" name="wallet_address" type="text" 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_wallet_address', [], 'auth') }}"
                           value="{{ old('wallet_address') }}">
                </div>

                <!-- Referrer ID -->
                <div>
                    <label for="referrer_id" class="block text-sm font-medium text-white">
                        {{ t('referrer_id', [], 'auth') }} ({{ t('optional', [], 'common') }})
                    </label>
                    <input id="referrer_id" name="referrer_id" type="text" 
                           class="mt-1 block w-full px-3 py-3 border border-action/30 rounded-lg bg-primary/50 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_referrer_id', [], 'auth') }}"
                           value="{{ old('referrer_id') ?? request('ref') }}">
                    @if(request('ref'))
                        <p class="mt-1 text-xs text-action/80">{{ t('you_were_referred', [], 'auth') }}</p>
                    @endif
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-primary bg-action hover:bg-action/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action transition-all transform hover:scale-105">
                    {{ t('create_account', [], 'auth') }}
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-white/80">
                    {{ t('already_have_account', [], 'auth') }} 
                    <a href="{{ route('login') }}" class="font-medium text-action hover:text-action/80 transition-colors">
                        {{ t('sign_in_here', [], 'auth') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
