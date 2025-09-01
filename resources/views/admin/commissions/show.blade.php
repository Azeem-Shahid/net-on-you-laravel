@extends('admin.layouts.app')

@section('title', 'Commission Details - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Commission #{{ $commission->id }}</h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('admin.commissions.index') }}" class="text-gray-500 hover:text-gray-700">Commissions</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700 font-medium">Commission Details</span>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Commission Information -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <div class="lg:col-span-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-medium text-gray-900">Commission Information</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <p class="mb-2"><span class="font-medium text-gray-700">Commission ID:</span> {{ $commission->id }}</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Level:</span> <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">Level {{ $commission->level }}</span></p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Amount:</span> ${{ number_format($commission->amount, 2) }}</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Month:</span> {{ $commission->month }}</p>
                        </div>
                        <div class="lg:col-span-6">
                            <p class="mb-2"><span class="font-medium text-gray-700">Eligibility:</span> 
                                @if($commission->eligibility === 'eligible')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Eligible</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Ineligible</span>
                                @endif
                            </p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Payout Status:</span> 
                                @if($commission->payout_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($commission->payout_status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Void</span>
                                @endif
                            </p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Created:</span> {{ $commission->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-medium text-gray-900">User Information</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <p class="font-medium text-gray-700 mb-2">Earner:</p>
                            <p class="mb-2 text-gray-900">{{ $commission->earner->name }}</p>
                            <p class="text-sm text-gray-500">ID: {{ $commission->earner->id }}</p>
                            <p class="text-sm text-gray-500">{{ $commission->earner->email }}</p>
                        </div>
                        <div class="lg:col-span-6">
                            <p class="font-medium text-gray-700 mb-2">Source User:</p>
                            <p class="mb-2 text-gray-900">{{ $commission->sourceUser->name }}</p>
                            <p class="text-sm text-gray-500">ID: {{ $commission->sourceUser->id }}</p>
                            <p class="text-sm text-gray-500">{{ $commission->sourceUser->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-medium text-gray-900">Transaction Information</h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-6">
                    <p class="mb-2"><span class="font-medium text-gray-700">Transaction ID:</span> {{ $commission->transaction_id }}</p>
                    <p class="mb-2"><span class="font-medium text-gray-700">Amount:</span> ${{ number_format($commission->transaction->amount, 2) }}</p>
                    <p class="mb-2"><span class="font-medium text-gray-700">Currency:</span> {{ $commission->transaction->currency }}</p>
                </div>
                <div class="lg:col-span-6">
                    <p class="mb-2"><span class="font-medium text-gray-700">Gateway:</span> {{ $commission->transaction->gateway }}</p>
                    <p class="mb-2"><span class="font-medium text-gray-700">Status:</span> 
                        @if($commission->transaction->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                        @elseif($commission->transaction->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($commission->transaction->status) }}</span>
                        @endif
                    </p>
                    <p class="mb-2"><span class="font-medium text-gray-700">Date:</span> {{ $commission->transaction->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Actions -->
    @if($commission->payout_status === 'pending')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-medium text-gray-900">Commission Actions</h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-4">
                    <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="showAdjustModal({{ $commission->id }}, {{ $commission->amount }})">
                        <i class="fas fa-edit mr-2"></i> Adjust Commission
                    </button>
                </div>
                <div class="lg:col-span-4">
                    <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="showVoidModal({{ $commission->id }})">
                        <i class="fas fa-ban mr-2"></i> Void Commission
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($commission->payout_status === 'void')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-medium text-gray-900">Commission Actions</h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-4">
                    <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="showRestoreModal({{ $commission->id }})">
                        <i class="fas fa-undo mr-2"></i> Restore Commission
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Audit History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-medium text-gray-900">Audit History</h5>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($commission->auditLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $log->action === 'created' ? 'bg-green-100 text-green-800' : 
                                   ($log->action === 'adjusted' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($log->action === 'voided' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->admin->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $log->reason ?? 'No reason provided' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No audit history found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Adjust Commission Modal -->
<div id="adjustCommissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Adjust Commission</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('adjustCommissionModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="adjustCommissionForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="newAmount" class="block text-sm font-medium text-gray-700 mb-2">New Amount</label>
                    <input type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" name="new_amount" id="newAmount" required>
                </div>
                <div>
                    <label for="adjustReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Adjustment</label>
                    <textarea name="reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" rows="3" required></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('adjustCommissionModal')">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition-colors duration-200">Adjust Commission</button>
            </div>
        </form>
    </div>
</div>

<!-- Void Commission Modal -->
<div id="voidCommissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Void Commission</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('voidCommissionModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="voidCommissionForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="voidReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Void</label>
                    <textarea name="reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" rows="3" required></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('voidCommissionModal')">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors duration-200">Void Commission</button>
            </div>
        </form>
    </div>
</div>

<!-- Restore Commission Modal -->
<div id="restoreCommissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Restore Commission</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('restoreCommissionModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="restoreCommissionForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="restoreReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Restoration</label>
                    <textarea name="reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" rows="3" required></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('restoreCommissionModal')">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200">Restore Commission</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showAdjustModal(commissionId, currentAmount) {
    document.getElementById('adjustCommissionForm').action = `/admin/commissions/${commissionId}/adjust`;
    document.querySelector('#adjustCommissionModal input[name="new_amount"]').value = currentAmount;
    new bootstrap.Modal(document.getElementById('adjustCommissionModal')).show();
}

function showVoidModal(commissionId) {
    document.getElementById('voidCommissionForm').action = `/admin/commissions/${commissionId}/void`;
    new bootstrap.Modal(document.getElementById('voidCommissionModal')).show();
}

function showRestoreModal(commissionId) {
    document.getElementById('restoreCommissionForm').action = `/admin/commissions/${commissionId}/restore`;
    new bootstrap.Modal(document.getElementById('restoreCommissionModal')).show();
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.getElementById(modalId).classList.remove('flex');
}
</script>
@endpush
