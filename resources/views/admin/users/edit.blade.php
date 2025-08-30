@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit User: {{ $user->name }}</h1>
                    <p class="text-sm text-gray-600">Update user information and settings</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.show', $user) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-8">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-300 @enderror" 
                                    id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                                <option value="blocked" {{ old('status', $user->status) === 'blocked' ? 'selected' : '' }}>
                                    Blocked
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                            <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                                    id="language" name="language">
                                <option value="">Select Language</option>
                                <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>
                                    English
                                </option>
                                <option value="es" {{ old('language', $user->language) === 'es' ? 'selected' : '' }}>
                                    Spanish
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="subscription_start_date" class="block text-sm font-medium text-gray-700 mb-2">Subscription Start Date</label>
                            <input type="date" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('subscription_start_date') border-red-300 @enderror" 
                                   id="subscription_start_date" name="subscription_start_date" 
                                   value="{{ old('subscription_start_date', $user->subscription_start_date ? $user->subscription_start_date->format('Y-m-d') : '') }}">
                            @error('subscription_start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subscription_end_date" class="block text-sm font-medium text-gray-700 mb-2">Subscription End Date</label>
                            <input type="date" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('subscription_end_date') border-red-300 @enderror" 
                                   id="subscription_end_date" name="subscription_end_date" 
                                   value="{{ old('subscription_end_date', $user->subscription_end_date ? $user->subscription_end_date->format('Y-m-d') : '') }}">
                            @error('subscription_end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="wallet_address" class="block text-sm font-medium text-gray-700 mb-2">Wallet Address</label>
                        <input type="text" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                               id="wallet_address" name="wallet_address" 
                               value="{{ old('wallet_address', $user->wallet_address) }}" 
                               placeholder="Enter wallet address">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Verification</label>
                            <div class="flex items-center">
                                @if($user->hasVerifiedEmail())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Verified
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500">{{ $user->email_verified_at->format('M d, Y H:i') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Not Verified
                                    </span>
                                    <button type="button" 
                                            class="ml-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs font-medium" 
                                            onclick="resendVerification({{ $user->id }})">
                                        Resend
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Login</label>
                            <div class="flex items-center">
                                @if($user->last_login_at)
                                    <span class="text-sm text-gray-500">{{ $user->last_login_at->format('M d, Y H:i') }}</span>
                                @else
                                    <span class="text-sm text-gray-500">Never</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Referrer</label>
                            <div class="flex items-center">
                                @if($user->referrer)
                                    <a href="{{ route('admin.users.show', $user->referrer) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        {{ $user->referrer->name }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500">None</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Referrals Count</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $user->referrals->count() }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Transactions</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $user->transactions->count() }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Commissions</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ${{ number_format($user->commissionsEarned->sum('amount'), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between">
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to User
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Update User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function resendVerification(userId) {
    if (confirm('Resend verification email to this user?')) {
        fetch(`/admin/users/${userId}/resend-verification`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Verification email sent successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    }
}

// Date validation
document.getElementById('subscription_end_date').addEventListener('change', function() {
    const startDate = document.getElementById('subscription_start_date').value;
    const endDate = this.value;
    
    if (startDate && endDate && startDate > endDate) {
        alert('Subscription end date must be after start date');
        this.value = '';
    }
});
</script>
@endpush
