@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-primary">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-action/20">
                <svg class="h-8 w-8 text-action" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-bold text-action">
                Verify Your Email
            </h2>
            <p class="mt-2 text-sm text-white/80">
                Before proceeding, please check your email for a verification link.
            </p>
                    </div>

            <!-- Custom Language Widget -->
            <div class="flex justify-center">
                @include('components.custom-language-widget')
            </div>

        <div class="bg-primary border border-action/30 rounded-lg p-6">
            <p class="text-white text-sm text-center">
                We've sent a verification link to <strong>{{ auth()->user()->email }}</strong>
            </p>
        </div>

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-primary bg-action hover:bg-action/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action transition-all transform hover:scale-105">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-action/30 text-sm font-medium rounded-lg text-action bg-transparent hover:bg-action/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action transition-all">
                    Logout
                </button>
            </form>
        </div>

        <div class="text-center">
            <p class="text-xs text-white/60">
                Didn't receive the email? Check your spam folder or 
                <button type="button" onclick="document.querySelector('form[action*=\"verification.send\"] button').click()" 
                        class="text-action hover:text-action/80 transition-colors underline">
                    click here to resend
                </button>
            </p>
        </div>
    </div>
</div>
@endsection
