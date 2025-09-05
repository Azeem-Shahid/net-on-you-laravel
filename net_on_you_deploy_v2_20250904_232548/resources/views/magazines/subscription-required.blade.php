@extends('layouts.app')

@section('title', 'Subscription Required')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Icon -->
            <div class="mx-auto h-24 w-24 text-gray-400">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Subscription Required
            </h2>
            
            <p class="mt-2 text-sm text-gray-600">
                You need an active subscription to access our premium magazine collection.
            </p>
        </div>

        <div class="bg-white py-8 px-6 shadow rounded-lg">
            <div class="space-y-6">
                <!-- Benefits -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">What you'll get:</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm text-gray-700">Access to all premium magazines</span>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm text-gray-700">High-quality PDF downloads</span>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm text-gray-700">Regular content updates</span>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="ml-3 text-sm text-gray-700">Multi-language support</span>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    @auth
                        <a href="{{ route('payment.checkout') }}" 
                           class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            Upgrade to Premium
                        </a>
                        
                        <a href="{{ route('dashboard') }}" 
                           class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            Back to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" 
                           class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            Sign Up & Subscribe
                        </a>
                        
                        <a href="{{ route('login') }}" 
                           class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            Sign In
                        </a>
                    @endauth
                </div>

                <!-- Additional Info -->
                <div class="text-center">
                    <p class="text-xs text-gray-500">
                        Already have a subscription? 
                        <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-500">
                            Sign in to access your account
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Pricing Info -->
        <div class="bg-white py-6 px-6 shadow rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Simple Pricing</h3>
            <div class="grid grid-cols-1 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="font-medium text-gray-900">Monthly Plan</h4>
                            <p class="text-sm text-gray-600">Perfect for trying out</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">$9.99</p>
                            <p class="text-sm text-gray-600">per month</p>
                        </div>
                    </div>
                </div>
                
                <div class="border-2 border-purple-500 rounded-lg p-4 bg-purple-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="font-medium text-gray-900">Annual Plan</h4>
                            <p class="text-sm text-gray-600">Best value - Save 17%</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">$99.99</p>
                            <p class="text-sm text-gray-600">per year</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
