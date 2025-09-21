@extends('layouts.auth')

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

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 bg-danger/20 border border-danger text-danger px-4 py-3 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-danger">
                            {{ t('validation_errors', [], 'auth') }}
                        </h3>
                        <div class="mt-2 text-sm text-danger">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-white">
                        {{ t('full_name', [], 'auth') }}
                    </label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-3 py-3 border @error('name') border-danger @else border-action/30 @enderror rounded-lg bg-primary text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_full_name', [], 'auth') }}"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white">
                        {{ t('email', [], 'common') }}
                    </label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-3 border @error('email') border-danger @else border-action/30 @enderror rounded-lg bg-primary text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_email', [], 'auth') }}"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        {{ t('password', [], 'auth') }}
                    </label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-3 border @error('password') border-danger @else border-action/30 @enderror rounded-lg bg-primary text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_password', [], 'auth') }}">
                    @error('password')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white">
                        {{ t('confirm_password', [], 'auth') }}
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 block w-full px-3 py-3 border @error('password_confirmation') border-danger @else border-action/30 @enderror rounded-lg bg-primary text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('confirm_password_placeholder', [], 'auth') }}">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Wallet Address -->
                <div>
                    <label for="wallet_address" class="block text-sm font-medium text-white">
                        {{ t('wallet_address', [], 'dashboard') }} ({{ t('optional', [], 'common') }})
                    </label>
                    <input id="wallet_address" name="wallet_address" type="text" 
                           class="mt-1 block w-full px-3 py-3 border @error('wallet_address') border-danger @else border-action/30 @enderror rounded-lg bg-primary text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                           placeholder="{{ t('enter_wallet_address', [], 'auth') }}"
                           value="{{ old('wallet_address') }}">
                    @error('wallet_address')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Referral Code -->
                <div>
                    <label for="referral_code" class="block text-sm font-medium text-white">
                        {{ t('referral_code', [], 'auth') }}
                        @if($referralCode ?? request('ref'))
                            <span class="text-action/80">({{ t('required', [], 'common') }})</span>
                        @else
                            <span class="text-white/60">({{ t('optional', [], 'common') }})</span>
                        @endif
                    </label>
                    
                    @if($referralCode ?? request('ref'))
                        <!-- Show referral information -->
                        <div class="mt-2 p-3 bg-action/10 border border-action/30 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-user-friends text-action mr-2"></i>
                                <span class="text-sm text-white/90">{{ t('referred_by', [], 'auth') }}</span>
                            </div>
                            @if(isset($referrer) && $referrer)
                                <p class="mt-1 text-sm text-action font-medium">{{ $referrer->name }}</p>
                                <p class="text-xs text-white/70">{{ t('referral_code_valid', [], 'auth') }}</p>
                            @else
                                <p class="mt-1 text-sm text-danger">{{ t('referral_code_invalid', [], 'auth') }}</p>
                            @endif
                        </div>
                        
                        <!-- Hidden field for referral code -->
                        <input id="referral_code" name="referral_code" type="hidden" 
                               value="{{ old('referral_code') ?? $referralCode ?? request('ref') }}">
                    @else
                        <!-- Editable field when no referral link -->
                        <input id="referral_code" name="referral_code" type="text" 
                               class="mt-1 block w-full px-3 py-3 border @error('referral_code') border-danger @else border-action/30 @enderror rounded-lg bg-primary text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-action focus:border-transparent transition-all"
                               placeholder="{{ t('enter_referral_code', [], 'auth') }}"
                               value="{{ old('referral_code') }}">
                        <p class="mt-1 text-xs text-white/60">
                            <i class="fas fa-info-circle mr-1"></i>{{ t('referral_code_format', [], 'auth') }}
                        </p>
                    @endif
                    @error('referral_code')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
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
