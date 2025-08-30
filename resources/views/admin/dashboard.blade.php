@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-primary to-primary/90 shadow-lg border-b border-action/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center py-6 space-y-4 sm:space-y-0">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-action truncate">Admin Dashboard</h1>
                    <p class="text-action/80 text-base sm:text-lg truncate">Welcome back, {{ auth('admin')->user()->name }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-action/80 text-xs sm:text-sm whitespace-nowrap">Last login: {{ auth('admin')->user()->last_login_at ? auth('admin')->user()->last_login_at->diffForHumans() : 'Never' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 overflow-hidden">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-primary hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-action" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-primary">{{ number_format($totalUsers) }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-action hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-action rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-3xl font-bold text-action">{{ number_format($activeUsers) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-3xl font-bold text-yellow-600">${{ number_format($subscriptionStats['total_revenue'], 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Subscriptions -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Subscriptions</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($subscriptionStats['active']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg mb-8 border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-primary">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <a href="{{ route('admin.users.create') }}" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-primary/5 hover:border-primary/30 transition-all duration-200 group">
                        <svg class="w-6 h-6 text-primary mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-primary">Add User</span>
                    </a>

                    <a href="{{ route('admin.magazines.create') }}" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-action/5 hover:border-action/30 transition-all duration-200 group">
                        <svg class="w-6 h-6 text-action mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-action">Upload Magazine</span>
                    </a>

                    <a href="{{ route('admin.transactions.index') }}" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-yellow-500/5 hover:border-yellow-500/30 transition-all duration-200 group">
                        <svg class="w-6 h-6 text-yellow-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-yellow-600">View Transactions</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-purple-500/5 hover:border-purple-500/30 transition-all duration-200 group">
                        <svg class="w-6 h-6 text-purple-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-purple-600">Manage Users</span>
                    </a>

                    <a href="{{ route('admin.referrals.index') }}" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-green-500/5 hover:border-green-500/30 transition-all duration-200 group">
                        <svg class="w-6 h-6 text-green-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-green-600">Referral Tree</span>
                    </a>

                    <a href="{{ route('admin.commissions.index') }}" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-blue-500/5 hover:border-blue-500/30 transition-all duration-200 group">
                        <svg class="w-6 h-6 text-blue-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600">Commissions</span>
                    </a>

                    <a href="{{ route('admin.payouts.index') }}" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-indigo-500/5 hover:border-indigo-500/30 transition-all duration-200 group">
                        <svg class="w-6 h-6 text-indigo-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">Payouts</span>
                    </a>

                    <a href="{{ route('admin.analytics.index') }}" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-emerald-500/5 hover:border-emerald-500/30 transition-all duration-200 group">
                        <svg class="w-6 h-6 text-emerald-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 group-hover:text-emerald-600">Analytics & Reports</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Transactions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <!-- Recent Transactions -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-primary">Recent Transactions</h3>
                </div>
                <div class="p-6">
                    @if($recentTransactions->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentTransactions->take(5) as $transaction)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-action">{{ substr($transaction->user->name ?? 'N/A', 0, 2) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->gateway }} • {{ $transaction->currency }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">${{ number_format($transaction->amount, 2) }}</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($transaction->status === 'completed') bg-action/20 text-action
                                            @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-danger/20 text-danger
                                            @endif">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.transactions.index') }}" class="text-sm text-primary hover:text-primary/80 font-medium">View all transactions →</a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No recent transactions</p>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-primary">Recent Activity</h3>
                </div>
                <div class="p-6">
                    @if($recentActivity->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentActivity->take(5) as $activity)
                                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-action rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-primary">{{ substr($activity->admin->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900">
                                            <span class="font-medium text-primary">{{ $activity->admin->name }}</span>
                                            {{ $activity->action }}
                                            @if($activity->target_type && $activity->target_id)
                                                <span class="text-gray-500">{{ $activity->target_type }} #{{ $activity->target_id }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No recent activity</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any JavaScript for dashboard interactions here
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any charts or interactive elements
        console.log('Admin dashboard loaded');
    });
</script>
@endpush

