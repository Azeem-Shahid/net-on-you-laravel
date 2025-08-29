@extends('admin.layouts.app')

@section('title', 'User Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
                    <p class="text-sm text-gray-600">View and manage user information</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-8">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-indigo-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <span class="text-3xl font-bold text-indigo-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-1">{{ $user->name }}</h3>
                            <p class="text-gray-600 mb-3">{{ $user->email }}</p>
                            
                            <div class="mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($user->status === 'active') bg-green-100 text-green-800
                                    @elseif($user->status === 'blocked') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $user->referrals->count() }}</h4>
                                    <p class="text-sm text-gray-600">Referrals</p>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $user->transactions->count() }}</h4>
                                    <p class="text-sm text-gray-600">Transactions</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 mt-6 pt-6">
                            <h5 class="text-sm font-medium text-gray-900 uppercase tracking-wide mb-4">Profile Information</h5>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Language</p>
                                        <p class="text-sm text-gray-900">{{ $user->language ?? 'Not set' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Wallet Address</p>
                                        <p class="text-sm text-gray-900 break-all">{{ $user->wallet_address ?? 'Not set' }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Subscription Start</p>
                                        <p class="text-sm text-gray-900">{{ $user->subscription_start_date ? $user->subscription_start_date->format('M d, Y') : 'Not set' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Subscription End</p>
                                        <p class="text-sm text-gray-900">{{ $user->subscription_end_date ? $user->subscription_end_date->format('M d, Y') : 'Not set' }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Referrer</p>
                                        <p class="text-sm text-gray-900">
                                            @if($user->referrer)
                                                <a href="{{ route('admin.users.show', $user->referrer) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">{{ $user->referrer->name }}</a>
                                            @else
                                                None
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Joined</p>
                                        <p class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium text-center block">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit User
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details and Activities -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Subscription Status -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Subscription Status</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($user->subscription_end_date && $user->subscription_end_date->isFuture()) bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($user->subscription_end_date && $user->subscription_end_date->isFuture())
                                        Active
                                    @else
                                        Expired
                                    @endif
                                </span>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Start Date</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $user->subscription_start_date ? $user->subscription_start_date->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">End Date</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $user->subscription_end_date ? $user->subscription_end_date->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
                    </div>
                    <div class="px-6 py-4">
                        @if($user->transactions->count() > 0)
                            <div class="space-y-3">
                                @foreach($user->transactions->take(5) as $transaction)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $transaction->description ?? 'Transaction' }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">${{ number_format($transaction->amount, 2) }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($transaction->status === 'completed') bg-green-100 text-green-800
                                                @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($user->transactions->count() > 5)
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View all {{ $user->transactions->count() }} transactions
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500 text-center py-4">No transactions found</p>
                        @endif
                    </div>
                </div>

                <!-- Referral Information -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Referral Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Total Referrals</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $user->referrals->count() }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Commissions</p>
                                <p class="text-2xl font-bold text-green-600">${{ number_format($user->commissionsEarned->sum('amount'), 2) }}</p>
                            </div>
                        </div>
                        
                        @if($user->referrals->count() > 0)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Recent Referrals</h4>
                                <div class="space-y-2">
                                    @foreach($user->referrals->take(3) as $referral)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-xs font-medium text-gray-600">{{ substr($referral->name, 0, 2) }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $referral->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $referral->email }}</p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $referral->created_at->format('M d, Y') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
