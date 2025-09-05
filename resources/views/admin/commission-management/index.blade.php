@extends('admin.layouts.app')

@section('title', 'Enhanced Commission Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Enhanced Commission Management</h3>
                        <p class="text-sm text-gray-600 mt-1">Comprehensive commission tracking, payment status, and admin override tools</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/80 transition-colors" onclick="openProcessEligibilityModal()">
                            <i class="fas fa-calculator mr-2"></i> Process Monthly Eligibility
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors" onclick="openCreatePayoutModal()">
                            <i class="fas fa-money-bill-wave mr-2"></i> Create Payout Batch
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors" onclick="openOverrideModal()">
                            <i class="fas fa-user-cog mr-2"></i> Admin Override
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Monthly Payment Summary -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg shadow-md mb-6">
                    <div class="p-6">
                        <h4 class="text-xl font-bold mb-4">Monthly Payment Summary - {{ $monthlyPaymentSummary['month'] ?? now()->format('Y-m') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold">${{ number_format($monthlyPaymentSummary['total_balance'] ?? 0, 2) }}</div>
                                <div class="text-sm opacity-75">Total Balance to Pay</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold">${{ number_format($monthlyPaymentSummary['company_earnings'] ?? 0, 2) }}</div>
                                <div class="text-sm opacity-75">Company Earnings</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $monthlyPaymentSummary['eligible_users_count'] ?? 0 }}</div>
                                <div class="text-sm opacity-75">Eligible Users</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $monthlyPaymentSummary['ineligible_users_count'] ?? 0 }}</div>
                                <div class="text-sm opacity-75">Ineligible Users</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commission Status Overview -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-blue-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $totalStats['total_users'] ?? 0 }}</h4>
                                    <p class="text-sm opacity-75">Total Users</p>
                                </div>
                                <i class="fas fa-users text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">${{ number_format($totalStats['eligible'] ?? 0, 2) }}</h4>
                                    <p class="text-sm opacity-75">Eligible Commissions</p>
                                </div>
                                <i class="fas fa-check-circle text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">${{ number_format($totalStats['ineligible'] ?? 0, 2) }}</h4>
                                    <p class="text-sm opacity-75">Ineligible Commissions</p>
                                </div>
                                <i class="fas fa-times-circle text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">${{ number_format($totalStats['company_earnings'] ?? 0, 2) }}</h4>
                                    <p class="text-sm opacity-75">Company Earnings</p>
                                </div>
                                <i class="fas fa-building text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commission Eligibility Report -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900">Users with Direct Sales</h4>
                            <p class="text-sm text-gray-600">Users who made sales and qualify for commissions</p>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @forelse($commissionEligibilityReport['users_with_sales'] ?? [] as $userData)
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $userData['user']->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $userData['user']->email }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-green-600">${{ number_format($userData['eligible_commission'], 2) }}</div>
                                        <div class="text-xs text-gray-500">{{ $userData['commission_count'] }} commissions</div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center text-gray-500 py-4">No users with direct sales found</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900">Users without Direct Sales</h4>
                            <p class="text-sm text-gray-600">Users who didn't make sales (commissions go to company)</p>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @forelse($commissionEligibilityReport['users_without_sales'] ?? [] as $userData)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $userData['user']->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $userData['user']->email }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-red-600">${{ number_format($userData['ineligible_commission'], 2) }}</div>
                                        <div class="text-xs text-gray-500">{{ $userData['commission_count'] }} commissions</div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center text-gray-500 py-4">No users without direct sales found</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed User Breakdown -->
                <div class="bg-white rounded-lg shadow-md mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900">Detailed User Breakdown</h4>
                        <p class="text-sm text-gray-600">Complete breakdown of all users with commissions this month</p>
                    </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Commission</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eligible</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ineligible</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Direct Sales</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($monthlyPaymentSummary['user_breakdown'] ?? [] as $userBreakdown)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $userBreakdown['user']->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $userBreakdown['user']->email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($userBreakdown['total_commission'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        ${{ number_format($userBreakdown['eligible_commission'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        ${{ number_format($userBreakdown['ineligible_commission'], 2) }}
                                    </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                        @if($userBreakdown['has_direct_sales'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Yes
                                                    </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                No
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openUserOverrideModal({{ $userBreakdown['user']->id }}, '{{ $monthlyPaymentSummary['month'] }}')" 
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Override
                                        </button>
                                        <button onclick="openUserReportModal({{ $userBreakdown['user']->id }}, '{{ $monthlyPaymentSummary['month'] }}')" 
                                                class="text-blue-600 hover:text-blue-900">
                                            Report
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No commission data found for this month</td>
                                </tr>
                                @endforelse
                                    </tbody>
                                </table>
                    </div>
                </div>

                <!-- Admin Override Modal -->
                <div id="overrideModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Admin Override</h3>
                                <button onclick="closeOverrideModal()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
            </div>
                            <form id="overrideForm" method="POST" action="{{ route('admin.commission-management.override-eligibility') }}">
                @csrf
                                <div class="mb-4">
                                    <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
                                    <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                                        <option value="">Select User</option>
                                        @foreach($monthlyPaymentSummary['user_breakdown'] ?? [] as $userBreakdown)
                                        <option value="{{ $userBreakdown['user']->id }}">{{ $userBreakdown['user']->name }} ({{ $userBreakdown['user']->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                                    <input type="month" id="month" name="month" value="{{ $monthlyPaymentSummary['month'] ?? now()->format('Y-m') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                                </div>
                                <div class="mb-4">
                                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                                    <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" placeholder="Enter reason for override..."></textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeOverrideModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/80">
                                        Override Eligibility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

                <!-- User Report Modal -->
                <div id="userReportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">User Commission Report</h3>
                                <button onclick="closeUserReportModal()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                        </div>
                            <div id="userReportContent">
                                <!-- Report content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
</div>

<script>
function openOverrideModal() {
    document.getElementById('overrideModal').classList.remove('hidden');
}

function closeOverrideModal() {
    document.getElementById('overrideModal').classList.add('hidden');
}

function openUserOverrideModal(userId, month) {
    document.getElementById('user_id').value = userId;
    document.getElementById('month').value = month;
    openOverrideModal();
}

function openUserReportModal(userId, month) {
    document.getElementById('userReportModal').classList.remove('hidden');
    
    // Load user report data
    fetch(`{{ route('admin.commission-management.user-commission-report') }}?user_id=${userId}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('userReportContent');
            content.innerHTML = `
                <div class="mb-4">
                    <h4 class="text-lg font-semibold">Commission Summary for ${data.commissions[0]?.earner?.name || 'User'}</h4>
                    <p class="text-sm text-gray-600">Month: ${data.month}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-100 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600">$${data.summary.total_amount.toFixed(2)}</div>
                        <div class="text-sm text-blue-800">Total Amount</div>
                    </div>
                    <div class="bg-green-100 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600">$${data.summary.eligible_amount.toFixed(2)}</div>
                        <div class="text-sm text-green-800">Eligible</div>
                    </div>
                    <div class="bg-red-100 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-red-600">$${data.summary.ineligible_amount.toFixed(2)}</div>
                        <div class="text-sm text-red-800">Ineligible</div>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-yellow-600">${data.summary.total_count}</div>
                        <div class="text-sm text-yellow-800">Total Count</div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Eligibility</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source User</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.commissions.map(commission => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${new Date(commission.created_at).toLocaleDateString()}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Level ${commission.level}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $${commission.amount.toFixed(2)}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${commission.eligibility === 'eligible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                            ${commission.eligibility}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${commission.source_user?.name || 'N/A'}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading user report:', error);
            document.getElementById('userReportContent').innerHTML = '<p class="text-red-600">Error loading report data.</p>';
        });
}

function closeUserReportModal() {
    document.getElementById('userReportModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const overrideModal = document.getElementById('overrideModal');
    const userReportModal = document.getElementById('userReportModal');
    
    if (event.target === overrideModal) {
        closeOverrideModal();
    }
    if (event.target === userReportModal) {
        closeUserReportModal();
    }
}
</script>
@endsection