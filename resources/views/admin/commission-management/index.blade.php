@extends('admin.layouts.app')

@section('title', 'Commission Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Commission Management Dashboard</h3>
                    <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/80 transition-colors" onclick="openProcessEligibilityModal()">
                            <i class="fas fa-calculator mr-2"></i> Process Monthly Eligibility
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors" onclick="openCreatePayoutModal()">
                            <i class="fas fa-money-bill-wave mr-2"></i> Create Payout Batch
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Monthly Stats -->
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
                                    <h4 class="text-2xl font-bold mb-1">${{ number_format($totalStats['total_commissions'] ?? 0, 2) }}</h4>
                                    <p class="text-sm opacity-75">Total Commissions</p>
                                </div>
                                <i class="fas fa-dollar-sign text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">${{ number_format($pendingPayouts->sum('amount') ?? 0, 2) }}</h4>
                                    <p class="text-sm opacity-75">Pending Payouts</p>
                                </div>
                                <i class="fas fa-clock text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-600 text-white rounded-lg shadow-md">
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

                <!-- Monthly Breakdown -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-900">Monthly Commission Breakdown</h5>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eligible</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ineligible</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ now()->format('M Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($currentMonthStats['eligible_commissions'] ?? 0, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($currentMonthStats['ineligible_commissions'] ?? 0, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format(($currentMonthStats['eligible_commissions'] ?? 0) + ($currentMonthStats['ineligible_commissions'] ?? 0), 2) }}</td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ now()->subMonth()->format('M Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($previousMonthStats['eligible_commissions'] ?? 0, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($previousMonthStats['ineligible_commissions'] ?? 0, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format(($previousMonthStats['eligible_commissions'] ?? 0) + ($previousMonthStats['ineligible_commissions'] ?? 0), 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-900">Recent Commissions</h5>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @if($recentCommissions->count() > 0)
                                            @foreach($recentCommissions as $commission)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $commission->id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $commission->eligibility == 'eligible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($commission->eligibility) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($commission->amount, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $commission->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No commissions found.</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Eligibility Modal -->
<div id="processEligibilityModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.commission-management.process-eligibility') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-calculator text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Process Monthly Eligibility
                            </h3>
                            <div class="mt-4">
                                <div class="mb-4">
                                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                    <input type="month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="month" name="month" value="{{ date('Y-m') }}" required>
                                    <p class="mt-1 text-xs text-gray-500">Select the month to process eligibility for.</p>
                                </div>
                                <div class="mb-4">
                                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                    <textarea class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="reason" name="reason" rows="3" required></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Provide a reason for processing eligibility.</p>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        This will process the 1-sale-per-month rule for all users and update commission eligibility.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Process Eligibility
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('processEligibilityModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Payout Modal -->
<div id="createPayoutModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.commission-management.create-payout-batch') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-money-bill-wave text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Create Payout Batch
                            </h3>
                            <div class="mt-4">
                                <div class="mb-4">
                                    <label for="payout_month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                    <input type="month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="payout_month" name="month" value="{{ date('Y-m') }}" required>
                                    <p class="mt-1 text-xs text-gray-500">Select the month to create payouts for.</p>
                                </div>
                                <div class="mb-4">
                                    <label for="payout_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                    <textarea class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="payout_reason" name="reason" rows="3" required></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Provide a reason for creating the payout batch.</p>
                                </div>
                                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        This will create a payout batch for all eligible commissions in the selected month.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Create Payout Batch
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('createPayoutModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openProcessEligibilityModal() {
    document.getElementById('processEligibilityModal').classList.remove('hidden');
}

function openCreatePayoutModal() {
    document.getElementById('createPayoutModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('processEligibilityModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('processEligibilityModal');
    }
});

document.getElementById('createPayoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('createPayoutModal');
    }
});
</script>
@endsection
