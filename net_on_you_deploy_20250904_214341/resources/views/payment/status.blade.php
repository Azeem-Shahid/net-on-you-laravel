@extends('layouts.app')

@section('title', 'Payment Status')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Status</h1>
            <p class="text-gray-600">Transaction #{{ $transaction->id }}</p>
        </div>

        <!-- Transaction Details -->
        <div class="bg-white rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Transaction Details</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($transaction->status === 'completed') bg-green-100 text-green-800
                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($transaction->status === 'failed') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($transaction->status) }}
                </span>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-medium text-gray-900">${{ number_format($transaction->amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Currency:</span>
                    <span class="font-medium text-gray-900">{{ $transaction->currency }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Gateway:</span>
                    <span class="font-medium text-gray-900">{{ ucfirst($transaction->gateway) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Created:</span>
                    <span class="font-medium text-gray-900">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                </div>
                @if($transaction->notes)
                <div class="pt-3 border-t border-gray-200">
                    <span class="text-gray-600">Notes:</span>
                    <p class="text-gray-900 mt-1">{{ $transaction->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($transaction->status === 'pending')
            @if($transaction->gateway === 'coinpayments')
                <!-- Crypto Payment Instructions -->
                <div class="bg-blue-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Complete Your Payment</h3>
                    
                    @if(isset($transaction->meta['payment_address']))
                    <div class="bg-white rounded-lg p-4 mb-4">
                        <div class="text-sm text-gray-600 mb-2">Send exactly this amount to:</div>
                        <div class="font-mono text-lg font-bold text-blue-600 break-all">
                            {{ $transaction->meta['payment_address'] }}
                        </div>
                    </div>
                    @endif

                    @if(isset($transaction->meta['payment_url']))
                    <a href="{{ $transaction->meta['payment_url'] }}" target="_blank" 
                       class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium text-center block hover:bg-blue-700 transition-colors duration-200">
                        Pay with Crypto
                    </a>
                    @endif

                    <div class="mt-4 text-sm text-blue-700">
                        <p class="mb-2">⚠️ Important:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Send the exact amount shown above</li>
                            <li>Use only USDT (TRC20/ERC20) or BTC</li>
                            <li>Payment will be confirmed automatically via CoinPayments</li>
                            <li>Your subscription will activate once confirmed</li>
                        </ul>
                    </div>
                </div>
            @elseif($transaction->gateway === 'nowpayments')
                <!-- Legacy NowPayments Payment Instructions -->
                <div class="bg-blue-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Complete Your Payment</h3>
                    
                    @if(isset($transaction->meta['payment_address']))
                    <div class="bg-white rounded-lg p-4 mb-4">
                        <div class="text-sm text-gray-600 mb-2">Send exactly this amount to:</div>
                        <div class="font-mono text-lg font-bold text-blue-600 break-all">
                            {{ $transaction->meta['payment_address'] }}
                        </div>
                    </div>
                    @endif

                    @if(isset($transaction->meta['payment_url']))
                    <a href="{{ $transaction->meta['payment_url'] }}" target="_blank" 
                       class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium text-center block hover:bg-blue-700 transition-colors duration-200">
                        Pay with Crypto
                    </a>
                    @endif

                    <div class="mt-4 text-sm text-blue-700">
                        <p class="mb-2">⚠️ Important:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Send the exact amount shown above</li>
                            <li>Use only USDT or BTC</li>
                            <li>Payment will be confirmed automatically</li>
                            <li>Your subscription will activate once confirmed</li>
                        </ul>
                    </div>
                </div>
            @else
                <!-- Manual Payment Instructions -->
                <div class="bg-yellow-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-4">Manual Payment</h3>
                    <p class="text-yellow-800 mb-4">
                        Please upload your payment proof (screenshot, receipt, or transaction details) for admin review.
                    </p>
                    <a href="{{ route('payment.manual', $transaction) }}" 
                       class="w-full bg-yellow-600 text-white py-3 px-4 rounded-lg font-medium text-center block hover:bg-yellow-700 transition-colors duration-200">
                        Upload Payment Proof
                    </a>
                </div>
            @endif
        @endif

        @if($transaction->status === 'completed')
            <!-- Success Message -->
            <div class="bg-green-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-green-900">Payment Successful!</h3>
                        <p class="text-green-700">Your subscription is now active</p>
                    </div>
                </div>
                
                @if(isset($transaction->meta['subscription_activated']))
                <div class="bg-white rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-2">Subscription Details:</div>
                    <div class="font-medium text-gray-900">
                        {{ ucfirst($transaction->meta['plan'] ?? 'monthly') }} Plan
                    </div>
                    <div class="text-sm text-gray-500">
                        Activated on {{ \Carbon\Carbon::parse($transaction->meta['subscription_activated_at'])->format('M d, Y H:i') }}
                    </div>
                </div>
                @endif
            </div>
        @endif

        @if($transaction->status === 'failed')
            <!-- Failed Payment -->
            <div class="bg-red-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-red-900">Payment Failed</h3>
                        <p class="text-red-700">Your payment could not be processed</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('payment.checkout') }}" 
                       class="w-full bg-red-600 text-white py-3 px-4 rounded-lg font-medium text-center block hover:bg-red-700 transition-colors duration-200">
                        Try Again
                    </a>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="space-y-3">
            <a href="{{ route('dashboard') }}" 
               class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-medium text-center block hover:bg-gray-700 transition-colors duration-200">
                Back to Dashboard
            </a>
            
            <a href="{{ route('payment.history') }}" 
               class="w-full bg-white text-gray-700 py-3 px-4 rounded-lg font-medium text-center block border border-gray-300 hover:bg-gray-50 transition-colors duration-200">
                View Payment History
            </a>
        </div>

        <!-- Help Section -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 mb-2">Need help with your payment?</p>
            <a href="mailto:support@example.com" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Contact Support
            </a>
        </div>
    </div>
</div>

<!-- Auto-refresh for pending payments -->
@if($transaction->status === 'pending')
<script>
setTimeout(function() {
    window.location.reload();
}, 30000); // Refresh every 30 seconds for pending payments
</script>
@endif
@endsection
