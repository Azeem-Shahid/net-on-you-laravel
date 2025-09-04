@extends('admin.layouts.app')

@section('title', 'Monthly Commission Breakdown')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Monthly Commission Breakdown - {{ $month }}</h3>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.commission-management.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
        <div class="p-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600">Eligible Users</p>
                            <p class="text-2xl font-semibold text-green-900">{{ count($eligibleUsers) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-red-600">Ineligible Users</p>
                            <p class="text-2xl font-semibold text-red-900">{{ count($ineligibleUsers) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-yellow-600">Total Commissions</p>
                            <p class="text-2xl font-semibold text-yellow-900">${{ number_format(collect($commissionBreakdown)->sum('total_commission'), 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600">Company Earnings</p>
                            <p class="text-2xl font-semibold text-blue-900">${{ number_format($companyEarnings, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Breakdown Table -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-900">Commission Breakdown by User</h5>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wallet Address</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Commission</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eligibility</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payout Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if(count($commissionBreakdown) > 0)
                                        @foreach($commissionBreakdown as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $item['user_name'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $item['user_email'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($item['wallet_address'])
                                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $item['wallet_address'] }}</code>
                                                @else
                                                    <span class="text-sm text-gray-500">Not set</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">${{ number_format($item['total_commission'], 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item['eligibility'] == 'eligible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($item['eligibility']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item['payout_status'] == 'paid' ? 'bg-green-100 text-green-800' : ($item['payout_status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($item['payout_status']) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No commissions found for this month.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-900">Eligible Users</h5>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @if($eligibleUsers->count() > 0)
                                    @foreach($eligibleUsers as $user)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-center justify-between">
                                            <h6 class="text-sm font-medium text-gray-900">{{ $user->name }}</h6>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Eligible</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
                                        @if($user->wallet_address)
                                            <p class="text-xs text-gray-500 mt-1">Wallet: {{ $user->wallet_address }}</p>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-gray-500 py-4">
                                        No eligible users found.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-900">Ineligible Users</h5>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @if($ineligibleUsers->count() > 0)
                                    @foreach($ineligibleUsers as $user)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-center justify-between">
                                            <h6 class="text-sm font-medium text-gray-900">{{ $user->name }}</h6>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ineligible</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
                                        <p class="text-xs text-gray-500 mt-1">No sales this month</p>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-gray-500 py-4">
                                        No ineligible users found.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize any JavaScript functionality here
        console.log('Monthly breakdown page loaded for {{ $month }}');
    });
</script>
@endpush
