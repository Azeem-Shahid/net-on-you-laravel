@extends('layouts.app')

@section('title', 'Dashboard - NetOnYou')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ t('welcome_back', [], 'dashboard') }}, {{ $user->name }}!</h1>
            <p class="text-gray-600 mt-2">{{ t('manage_account_progress', [], 'dashboard') }}</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Profile Summary Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ t('profile_summary', [], 'dashboard') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">{{ t('name', [], 'common') }}</label>
                    <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">{{ t('email', [], 'common') }}</label>
                    <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">{{ t('language', [], 'common') }}</label>
                    <p class="text-gray-900 font-medium">{{ strtoupper($user->language) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">{{ t('wallet_address', [], 'dashboard') }}</label>
                    <p class="text-gray-900 font-medium break-all">{{ $user->wallet_address ?: t('not_set', [], 'common') }}</p>
                </div>
            </div>
        </div>

        <!-- Subscription Status Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ t('subscription_status', [], 'dashboard') }}</h2>
            <div class="flex items-center justify-between">
                <div>
                    @php
                        $status = $user->getSubscriptionStatus();
                        $statusColor = $status === 'active' ? 'text-[#00ff00]' : ($status === 'grace' ? 'text-yellow-600' : 'text-[#ff0000]');
                        $statusText = ucfirst($status);
                    @endphp
                    <span class="text-2xl font-bold {{ $statusColor }}">{{ $statusText }}</span>
                    @if($user->subscription_end_date)
                        <p class="text-gray-600 mt-1">
                            {{ t('expires', [], 'dashboard') }}: {{ $user->subscription_end_date->format('M d, Y') }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    @if($user->subscription_start_date)
                        <p class="text-gray-600">{{ t('started', [], 'dashboard') }}: {{ $user->subscription_start_date->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Referral Overview Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Referral Overview</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-2">Your Referral Link</label>
                <div class="flex">
                    <input type="text" value="{{ $user->getReferralLink() }}" readonly 
                           class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 bg-gray-50 text-gray-600">
                    <button onclick="copyToClipboard('{{ $user->getReferralLink() }}')" 
                            class="bg-[#1d003f] text-white px-4 py-2 rounded-r-lg hover:bg-[#2a0057] transition-colors">
                        Copy
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                @for($level = 1; $level <= 6; $level++)
                    <div class="text-center">
                        <div class="text-2xl font-bold text-[#1d003f]">{{ $referralStats[$level] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Level {{ $level }}</div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Commission Earnings Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Commission Earnings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-[#00ff00]">${{ number_format($commissionEarnings['monthly'], 2) }}</div>
                    <div class="text-gray-600">This Month</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-3xl font-bold text-[#1d003f]">${{ number_format($commissionEarnings['total'], 2) }}</div>
                    <div class="text-gray-600">Total Earnings</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-3xl font-bold text-yellow-600">${{ number_format($commissionEarnings['pending'], 2) }}</div>
                    <div class="text-gray-600">Pending Payout</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-3xl font-bold text-purple-600">{{ $commissionBreakdown['month'] }}</div>
                    <div class="text-gray-600">Current Period</div>
                </div>
            </div>
            
            <!-- Commission Level Breakdown -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-3">RESULTS FOR THE CURRENT MONTH:</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 text-center">
                    @for($level = 1; $level <= 6; $level++)
                        <div class="p-3 bg-white rounded-lg border border-gray-200">
                            <div class="text-lg font-bold text-[#1d003f]">{{ $commissionBreakdown['level_' . $level]['users'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Level {{ $level }} Users</div>
                            <div class="text-lg font-bold text-[#00ff00] mt-1">
                                {{ $commissionBreakdown['level_' . $level]['currency'] ?? 'USDT' }} {{ number_format($commissionBreakdown['level_' . $level]['amount'] ?? 0, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">ACCUMULATED</div>
                        </div>
                    @endfor
                </div>
            </div>
            
            <!-- Commission Status Breakdown -->
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-3">This Month's Status</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-[#00ff00]">${{ number_format($commissionBreakdown['eligible'], 2) }}</div>
                        <div class="text-sm text-gray-600">Eligible</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-red-600">${{ number_format($commissionBreakdown['ineligible'], 2) }}</div>
                        <div class="text-sm text-gray-600">Ineligible</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-yellow-600">${{ number_format($commissionBreakdown['pending'], 2) }}</div>
                        <div class="text-sm text-gray-600">Pending</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600">${{ number_format($commissionBreakdown['paid'], 2) }}</div>
                        <div class="text-sm text-gray-600">Paid</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Access Status Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Access Status</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 mb-2">Contract Status</p>
                    @if($user->hasAcceptedLatestContract())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Contract Accepted
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            Contract Not Accepted - Payment Required
                        </span>
                    @endif
                </div>
                @if(!$user->hasAcceptedLatestContract())
                    <a href="{{ route('payment.checkout') }}" 
                       class="bg-[#00ff00] text-[#1d003f] px-6 py-2 rounded-lg font-medium hover:bg-[#00cc00] transition-colors">
                        Accept Contract
                    </a>
                @endif
            </div>
        </div>

        <!-- Recent Transactions Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Transactions</h2>
            @if($recentTransactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Currency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gateway</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->currency }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->gateway }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColor = $transaction->status === 'completed' ? 'text-[#00ff00]' : 
                                                          ($transaction->status === 'pending' ? 'text-yellow-600' : 'text-[#ff0000]');
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No transactions found.</p>
            @endif
        </div>

        <!-- Magazine Access Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Magazine Access</h2>
            @if($magazineEntitlements->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($magazineEntitlements as $entitlement)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $entitlement->magazine->title ?? 'Magazine #' . $entitlement->magazine_id }}</h3>
                                    <p class="text-sm text-gray-600">{{ $entitlement->granted_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($entitlement->reason) }}</p>
                                </div>
                                <a href="#" class="bg-[#1d003f] text-white px-3 py-2 rounded-lg text-sm hover:bg-[#2a0057] transition-colors">
                                    Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No magazine access available.</p>
            @endif
        </div>
    </div>
    
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('bg-green-600');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-600');
        }, 2000);
    });
}
</script>
@endsection

