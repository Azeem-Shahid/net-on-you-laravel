@extends('layouts.app')

@section('title', 'My Referrals')

@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 overflow-hidden">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">My Referrals</h1>
            <p class="text-white/80">Track your referral network and earnings</p>
        </div>

        <!-- Referral Overview Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Referral Overview</h2>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-600 mb-2">Your Referral Link</label>
                <div class="flex">
                    <input type="text" value="{{ $user->getReferralLink() }}" readonly 
                           class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 bg-gray-50 text-gray-600">
                    <button onclick="copyToClipboard('{{ $user->getReferralLink() }}')" 
                            class="bg-action text-primary px-4 py-2 rounded-r-lg hover:bg-action/90 transition-colors font-medium">
                        Copy
                    </button>
                </div>
            </div>
            
            <!-- Referral Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                @for($level = 1; $level <= 6; $level++)
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-primary">{{ $referralStats[$level] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Level {{ $level }}</div>
                    </div>
                @endfor
            </div>

            <!-- Commission Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">${{ number_format($commissionEarnings['monthly'], 2) }}</div>
                    <div class="text-sm text-green-700">This Month</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">${{ number_format($commissionEarnings['total'], 2) }}</div>
                    <div class="text-sm text-blue-700">Total Earned</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600">${{ number_format($commissionEarnings['pending'], 2) }}</div>
                    <div class="text-sm text-orange-700">Pending Payout</div>
                </div>
            </div>
        </div>

        <!-- Recent Referrals Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Recent Referrals</h2>
                <a href="{{ route('referrals.details') }}" class="text-action hover:text-action/80 font-medium">
                    View All Details →
                </a>
            </div>
            
            @if($recentReferrals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentReferrals as $referral)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                                    <span class="text-white font-medium text-sm">
                                                        {{ strtoupper(substr($referral->referredUser->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $referral->referredUser->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $referral->referredUser->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">
                                            Level {{ $referral->level }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $referral->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($referral->referredUser->status === 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($referral->referredUser->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No referrals yet</h3>
                    <p class="text-gray-500 mb-4">Start sharing your referral link to build your network and earn commissions.</p>
                    <button onclick="copyToClipboard('{{ $user->getReferralLink() }}')" 
                            class="bg-action text-primary px-6 py-2 rounded-lg hover:bg-action/90 transition-colors font-medium">
                        Copy Referral Link
                    </button>
                </div>
            @endif
        </div>

        <!-- Commission Breakdown Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Commission Breakdown</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-3">This Month</h3>
                    @if(isset($commissionBreakdown['current_month']) && count($commissionBreakdown['current_month']) > 0)
                        <div class="space-y-2">
                            @foreach($commissionBreakdown['current_month'] as $level => $amount)
                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                    <span class="text-sm text-gray-600">Level {{ $level }}</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($amount, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No commissions earned this month yet.</p>
                    @endif
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-3">Commission Rates</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600">Level 1</span>
                            <span class="text-sm font-medium text-gray-900">$15.00</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600">Level 2</span>
                            <span class="text-sm font-medium text-gray-900">$10.00</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600">Level 3</span>
                            <span class="text-sm font-medium text-gray-900">$5.00</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600">Level 4-6</span>
                            <span class="text-sm font-medium text-gray-900">$1.00</span>
                        </div>
                    </div>
                </div>
            </div>
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
        button.classList.add('bg-green-500');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-500');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection
