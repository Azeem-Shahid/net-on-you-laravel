@extends('admin.layouts.app')

@section('title', 'Create Admin User')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Create Admin User Without Payment</h3>
                    <div class="mt-4 sm:mt-0">
                        <a href="{{ route('admin.subscription-management.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.subscription-management.store-admin-user') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm @error('name') border-red-500 @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="lg:col-span-6">
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm @error('email') border-red-500 @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <div class="mb-4">
                                <label for="wallet_address" class="block text-sm font-medium text-gray-700 mb-1">Wallet Address</label>
                                <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm @error('wallet_address') border-red-500 @enderror" 
                                       id="wallet_address" name="wallet_address" value="{{ old('wallet_address') }}">
                                @error('wallet_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="lg:col-span-6">
                            <div class="mb-4">
                                <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Language <span class="text-red-500">*</span></label>
                                <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm @error('language') border-red-500 @enderror" 
                                        id="language" name="language" required>
                                    <option value="">Select Language</option>
                                    <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>German</option>
                                    <option value="it" {{ old('language') == 'it' ? 'selected' : '' }}>Italian</option>
                                </select>
                                @error('language')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <div class="mb-4">
                                <label for="subscription_duration" class="block text-sm font-medium text-gray-700 mb-1">Subscription Duration (Months) <span class="text-red-500">*</span></label>
                                <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm @error('subscription_duration') border-red-500 @enderror" 
                                        id="subscription_duration" name="subscription_duration" required>
                                    <option value="">Select Duration</option>
                                    <option value="1" {{ old('subscription_duration') == '1' ? 'selected' : '' }}>1 Month</option>
                                    <option value="3" {{ old('subscription_duration') == '3' ? 'selected' : '' }}>3 Months</option>
                                    <option value="6" {{ old('subscription_duration') == '6' ? 'selected' : '' }}>6 Months</option>
                                    <option value="12" {{ old('subscription_duration') == '12' ? 'selected' : '' }}>12 Months</option>
                                    <option value="24" {{ old('subscription_duration') == '24' ? 'selected' : '' }}>24 Months</option>
                                </select>
                                @error('subscription_duration')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="lg:col-span-6">
                            <div class="mb-4">
                                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Creation <span class="text-red-500">*</span></label>
                                <textarea class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm @error('reason') border-red-500 @enderror" 
                                          id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 mt-8">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/80 transition-colors">
                            <i class="fas fa-save mr-2"></i> Create User
                        </button>
                        <a href="{{ route('admin.subscription-management.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check required fields
            const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
</script>
@endpush

