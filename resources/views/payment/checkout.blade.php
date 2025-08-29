@extends('layouts.app')

@section('title', 'Payment Checkout')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Choose Your Plan</h1>
            <p class="text-gray-600">Get access to all premium magazines</p>
        </div>

        <!-- Plan Selection -->
        <div class="space-y-4 mb-8">
            @foreach($plans as $planKey => $plan)
            <div class="relative">
                <input type="radio" name="plan" id="plan_{{ $planKey }}" value="{{ $planKey }}" 
                       class="sr-only peer" {{ $selectedPlan === $planKey ? 'checked' : '' }}>
                <label for="plan_{{ $planKey }}" 
                       class="block p-6 bg-white border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-100 hover:border-gray-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $plan['name'] }}</h3>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-600">${{ number_format($plan['price'], 2) }}</div>
                            <div class="text-sm text-gray-500">
                                @if($planKey === 'annual')
                                    <span class="text-green-600 font-medium">Save 17%</span>
                                @else
                                    per month
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">{{ $plan['description'] }}</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Access to all magazines
                    </div>
                </label>
            </div>
            @endforeach
        </div>

        <!-- Payment Method Selection -->
        <div class="bg-white rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
            <div class="space-y-3">
                <div class="relative">
                    <input type="radio" name="payment_method" id="crypto" value="crypto" 
                           class="sr-only peer" checked>
                    <label for="crypto" 
                           class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                        <div class="flex-shrink-0 w-5 h-5 border-2 border-gray-300 rounded-full mr-3 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Cryptocurrency (USDT/BTC)</div>
                            <div class="text-sm text-gray-500">Pay with crypto via CoinPayments</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <span class="text-orange-600 text-xs font-bold">₿</span>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="relative">
                    <input type="radio" name="payment_method" id="manual" value="manual" 
                           class="sr-only peer">
                    <label for="manual" 
                           class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                        <div class="flex-shrink-0 w-5 h-5 border-2 border-gray-300 rounded-full mr-3 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Manual Payment</div>
                            <div class="text-sm text-gray-500">Upload payment proof for admin review</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <form action="{{ route('payment.initiate') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="plan" id="selected_plan" value="{{ $selectedPlan }}">
            <input type="hidden" name="payment_method" id="selected_payment_method" value="crypto">

            <!-- Total Summary -->
            <div class="bg-blue-50 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Selected Plan:</span>
                    <span class="font-medium text-gray-900" id="plan_summary">{{ $plans[$selectedPlan]['name'] }}</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-medium text-gray-900" id="duration_summary">{{ $plans[$selectedPlan]['duration'] }} days</span>
                </div>
                <div class="border-t border-blue-200 pt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                        <span class="text-2xl font-bold text-blue-600" id="total_amount">${{ number_format($plans[$selectedPlan]['price'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="button_text">Proceed to Payment</span>
                <div id="loading_spinner" class="hidden">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </div>
            </button>
        </form>

        <!-- Security Notice -->
        <div class="mt-8 text-center">
            <div class="flex items-center justify-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                </svg>
                Secure payment processing
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planInputs = document.querySelectorAll('input[name="plan"]');
    const paymentInputs = document.querySelectorAll('input[name="payment_method"]');
    const selectedPlanInput = document.getElementById('selected_plan');
    const selectedPaymentInput = document.getElementById('selected_payment_method');
    const planSummary = document.getElementById('plan_summary');
    const durationSummary = document.getElementById('duration_summary');
    const totalAmount = document.getElementById('total_amount');
    const buttonText = document.getElementById('button_text');
    const loadingSpinner = document.getElementById('loading_spinner');

    const plans = @json($plans);

    // Update form when plan changes
    planInputs.forEach(input => {
        input.addEventListener('change', function() {
            const plan = plans[this.value];
            selectedPlanInput.value = this.value;
            planSummary.textContent = plan.name;
            durationSummary.textContent = plan.duration + ' days';
            totalAmount.textContent = '$' + plan.price.toFixed(2);
        });
    });

    // Update form when payment method changes
    paymentInputs.forEach(input => {
        input.addEventListener('change', function() {
            selectedPaymentInput.value = this.value;
        });
    });

    // Form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        buttonText.classList.add('hidden');
        loadingSpinner.classList.remove('hidden');
    });
});
</script>
@endsection
