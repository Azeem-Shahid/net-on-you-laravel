@extends('admin.layouts.app')

@section('title', 'Referral Tree - Admin')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center">
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900">Referral Tree for {{ $user->name }}</h3>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-action">
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                    <a href="{{ route('admin.referrals.index') }}" class="text-sm font-medium text-gray-700 hover:text-action">
                                        Referrals
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                    <span class="text-sm font-medium text-gray-500">Referral Tree</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
            <div class="lg:col-span-6">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-medium text-gray-900">User Information</h5>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="lg:col-span-6">
                                <p class="mb-2"><strong class="text-gray-700">Name:</strong> {{ $user->name }}</p>
                                <p class="mb-2"><strong class="text-gray-700">Email:</strong> {{ $user->email }}</p>
                                <p class="mb-2"><strong class="text-gray-700">User ID:</strong> {{ $user->id }}</p>
                            </div>
                            <div class="lg:col-span-6">
                                <p class="mb-2"><strong class="text-gray-700">Referrer ID:</strong> {{ $user->referrer_id ?: 'None' }}</p>
                                <p class="mb-2"><strong class="text-gray-700">Joined:</strong> {{ $user->created_at ? $user->created_at->format('M d, Y') : 'Unknown' }}</p>
                                <p class="mb-2"><strong class="text-gray-700">Status:</strong> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($user->status ?: 'unknown') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-6">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-medium text-gray-900">Commission Statistics</h5>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="lg:col-span-6">
                                <p class="mb-2"><strong class="text-gray-700">Total Earned:</strong> ${{ number_format($commissionStats['total_earned'], 2) }}</p>
                                <p class="mb-2"><strong class="text-gray-700">Eligible:</strong> ${{ number_format($commissionStats['eligible_earned'], 2) }}</p>
                                <p class="mb-2"><strong class="text-gray-700">Ineligible:</strong> ${{ number_format($commissionStats['ineligible_earned'], 2) }}</p>
                            </div>
                            <div class="lg:col-span-6">
                                <p class="mb-2"><strong class="text-gray-700">Pending Payout:</strong> ${{ number_format($commissionStats['pending_payout'], 2) }}</p>
                                <p class="mb-2"><strong class="text-gray-700">Paid Out:</strong> ${{ number_format($commissionStats['paid_out'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Tree -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-medium text-gray-900">Referral Tree (6 Levels Down)</h5>
            </div>
            <div class="p-6">
                @for($level = 1; $level <= 6; $level++)
                    <div class="mb-6">
                        <h6 class="text-primary font-medium mb-3">Level {{ $level }}</h6>
                        @if(isset($referralTree[$level]) && $referralTree[$level] && $referralTree[$level]->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($referralTree[$level] as $referredUser)
                                    <div class="bg-white border border-primary rounded-lg shadow-sm">
                                        <div class="p-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-medium">
                                                            {{ substr($referredUser->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3 flex-1 min-w-0">
                                                    <h6 class="text-sm font-medium text-gray-900 truncate mb-1">{{ $referredUser->name }}</h6>
                                                    <p class="text-xs text-gray-500 mb-1">ID: {{ $referredUser->id }}</p>
                                                    <p class="text-xs text-gray-500 truncate">{{ $referredUser->email }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No users at this level.</p>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-medium text-gray-900">Monthly Commission Breakdown</h5>
            </div>
            <div class="p-6">
                @if($monthlyBreakdown && $monthlyBreakdown->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average per Commission</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($monthlyBreakdown as $breakdown)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $breakdown->month }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($breakdown->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $breakdown->count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($breakdown->total_amount / $breakdown->count, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center text-sm">No commission data available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
