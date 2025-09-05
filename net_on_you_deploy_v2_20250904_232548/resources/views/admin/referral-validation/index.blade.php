@extends('admin.layouts.app')

@section('title', 'Referral System Validation')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Referral System Validation Dashboard</h3>
                    <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/80 transition-colors" onclick="openValidateSystemModal()">
                            <i class="fas fa-check-circle mr-2"></i> Validate System
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors" onclick="openTestSystemModal()">
                            <i class="fas fa-vial mr-2"></i> Test System
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Referral Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-blue-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $totalUsers }}</h4>
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
                                    <h4 class="text-2xl font-bold mb-1">{{ $totalReferralLinks }}</h4>
                                    <p class="text-sm opacity-75">Referral Links</p>
                                </div>
                                <i class="fas fa-link text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $maxLevel }}</h4>
                                    <p class="text-sm opacity-75">Max Level</p>
                                </div>
                                <i class="fas fa-sitemap text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">${{ number_format($totalCommissions, 2) }}</h4>
                                    <p class="text-sm opacity-75">Total Commissions</p>
                                </div>
                                <i class="fas fa-dollar-sign text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Level Breakdown -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-900">Referral Level Breakdown</h5>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission Rate</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($levelBreakdown as $level => $data)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Level {{ $level }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['users'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['rate'] }}%</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($data['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-900">Recent Referral Activity</h5>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referrer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referred</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($recentReferrals as $referral)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $referral->referrer->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $referral->referred->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Level {{ $referral->level }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $referral->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No recent referrals found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validation Results -->
                @if(isset($validationResults))
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-medium text-gray-900">System Validation Results</h5>
                    </div>
                    <div class="p-6">
                        @if($validationResults['valid'])
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <strong>System Validation Passed!</strong> All referral relationships are correctly configured.
                                </div>
                            </div>
                        @else
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>System Validation Failed!</strong> Issues found in referral relationships.
                                </div>
                            </div>
                            <ul class="space-y-2">
                                @foreach($validationResults['issues'] as $issue)
                                <li class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ $issue }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Validate System Modal -->
<div id="validateSystemModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.referral-validation.validate-system') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-check-circle text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Validate Referral System
                            </h3>
                            <div class="mt-4">
                                <div class="mb-4">
                                    <label for="validation_month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                    <input type="month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="validation_month" name="month" value="{{ date('Y-m') }}" required>
                                    <p class="mt-1 text-xs text-gray-500">Select the month to validate the referral system for.</p>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        This will validate all referral relationships and commission calculations for the selected month.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Validate System
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('validateSystemModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Test System Modal -->
<div id="testSystemModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.referral-validation.test-system') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-vial text-yellow-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Test Referral System
                            </h3>
                            <div class="mt-4">
                                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        This will run a comprehensive test of the referral system with sample data to ensure all calculations are working correctly.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Run Test
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('testSystemModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openValidateSystemModal() {
    document.getElementById('validateSystemModal').classList.remove('hidden');
}

function openTestSystemModal() {
    document.getElementById('testSystemModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('validateSystemModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('validateSystemModal');
    }
});

document.getElementById('testSystemModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('testSystemModal');
    }
});
</script>
@endsection

