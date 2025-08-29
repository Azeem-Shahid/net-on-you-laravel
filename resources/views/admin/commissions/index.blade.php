@extends('admin.layouts.app')

@section('title', 'Commission Management - Admin')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Commission Management</h1>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">Dashboard</a>
                            </li>
                            <li>
                                <svg class="flex-shrink-0 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </li>
                            <li class="text-sm text-gray-900">Commissions</li>
                        </ol>
                    </nav>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200" 
                            onclick="showCreatePayoutBatchModal()">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Payout Batch
                    </button>
                    <a href="{{ route('admin.commissions.export') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h4 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total_commissions'] }}</h4>
                        <p class="text-sm text-gray-600">Total Commissions</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h4 class="text-2xl font-bold text-gray-900 mb-1">${{ number_format($stats['total_amount'], 2) }}</h4>
                        <p class="text-sm text-gray-600">Total Amount</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h4 class="text-2xl font-bold text-gray-900 mb-1">${{ number_format($stats['eligible_amount'], 2) }}</h4>
                        <p class="text-sm text-gray-600">Eligible</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h4 class="text-2xl font-bold text-gray-900 mb-1">${{ number_format($stats['pending_payout'], 2) }}</h4>
                        <p class="text-sm text-gray-600">Pending Payout</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h4 class="text-2xl font-bold text-gray-900 mb-1">${{ number_format($stats['paid_amount'], 2) }}</h4>
                        <p class="text-sm text-gray-600">Paid</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h4 class="text-2xl font-bold text-gray-900 mb-1">${{ number_format($stats['void_amount'], 2) }}</h4>
                        <p class="text-sm text-gray-600">Void</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filters</h3>
            </div>
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('admin.commissions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                        <input type="month" 
                               name="month" 
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                               value="{{ request('month') }}">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" 
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="eligible" {{ request('status') === 'eligible' ? 'selected' : '' }}>Eligible</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="void" {{ request('status') === 'void' ? 'selected' : '' }}>Void</option>
                        </select>
                    </div>
                    <div>
                        <label for="earner_id" class="block text-sm font-medium text-gray-700 mb-2">Earner ID</label>
                        <input type="number" 
                               name="earner_id" 
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                               value="{{ request('earner_id') }}" 
                               placeholder="Earner ID">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Commissions Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Commissions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($commissions as $commission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $commission->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">{{ substr($commission->user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $commission->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $commission->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($commission->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($commission->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($commission->status === 'eligible') bg-green-100 text-green-800
                                    @elseif($commission->status === 'paid') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($commission->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $commission->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.commissions.show', $commission) }}" 
                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                        View
                                    </a>
                                    @if($commission->status === 'eligible')
                                    <button type="button" 
                                            class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200" 
                                            onclick="showAdjustModal({{ $commission->id }}, {{ $commission->amount }})">
                                        Adjust
                                    </button>
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200" 
                                            onclick="showVoidModal({{ $commission->id }})">
                                        Void
                                    </button>
                                    @elseif($commission->status === 'void')
                                    <button type="button" 
                                            class="text-green-600 hover:text-green-900 transition-colors duration-200" 
                                            onclick="showRestoreModal({{ $commission->id }})">
                                        Restore
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $commissions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Create Payout Batch Modal -->
<div id="createPayoutBatchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Create Payout Batch</h3>
                <button type="button" 
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200" 
                        onclick="hideCreatePayoutBatchModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.commissions.create-payout-batch') }}">
                @csrf
                <div class="mb-4">
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                    <input type="month" 
                           name="month" 
                           class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                           required>
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" 
                              class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                              rows="3"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm font-medium transition-colors duration-200" 
                            onclick="hideCreatePayoutBatchModal()">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition-colors duration-200">
                        Create Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adjust Commission Modal -->
<div id="adjustCommissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Adjust Commission</h3>
                <button type="button" 
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200" 
                        onclick="hideAdjustModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" id="adjustCommissionForm">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="new_amount" class="block text-sm font-medium text-gray-700 mb-2">New Amount</label>
                    <input type="number" 
                           name="new_amount" 
                           step="0.01" 
                           min="0" 
                           class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                           required>
                </div>
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea name="reason" 
                              class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                              rows="3" 
                              required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm font-medium transition-colors duration-200" 
                            onclick="hideAdjustModal()">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm font-medium transition-colors duration-200">
                        Adjust
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Void Commission Modal -->
<div id="voidCommissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Void Commission</h3>
                <button type="button" 
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200" 
                        onclick="hideVoidModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" id="voidCommissionForm">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea name="reason" 
                              class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                              rows="3" 
                              required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm font-medium transition-colors duration-200" 
                            onclick="hideVoidModal()">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition-colors duration-200">
                        Void
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Commission Modal -->
<div id="restoreCommissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Restore Commission</h3>
                <button type="button" 
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200" 
                        onclick="hideRestoreModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" id="restoreCommissionForm">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea name="reason" 
                              class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                              rows="3" 
                              required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm font-medium transition-colors duration-200" 
                            onclick="hideRestoreModal()">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition-colors duration-200">
                        Restore
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCreatePayoutBatchModal() {
    document.getElementById('createPayoutBatchModal').classList.remove('hidden');
}

function hideCreatePayoutBatchModal() {
    document.getElementById('createPayoutBatchModal').classList.add('hidden');
}

function showAdjustModal(commissionId, currentAmount) {
    document.getElementById('adjustCommissionForm').action = `/admin/commissions/${commissionId}/adjust`;
    document.querySelector('#adjustCommissionForm input[name="new_amount"]').value = currentAmount;
    document.getElementById('adjustCommissionModal').classList.remove('hidden');
}

function hideAdjustModal() {
    document.getElementById('adjustCommissionModal').classList.add('hidden');
}

function showVoidModal(commissionId) {
    document.getElementById('voidCommissionForm').action = `/admin/commissions/${commissionId}/void`;
    document.getElementById('voidCommissionModal').classList.remove('hidden');
}

function hideVoidModal() {
    document.getElementById('voidCommissionModal').classList.add('hidden');
}

function showRestoreModal(commissionId) {
    document.getElementById('restoreCommissionForm').action = `/admin/commissions/${commissionId}/restore`;
    document.getElementById('restoreCommissionModal').classList.remove('hidden');
}

function hideRestoreModal() {
    document.getElementById('restoreCommissionModal').classList.add('hidden');
}
</script>
@endpush
