@extends('admin.layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">Dashboard</a></li>
                    <li><svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                    <li><a href="{{ route('admin.transactions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Transactions</a></li>
                    <li><svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                    <li class="text-sm text-gray-900">Transaction Details</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Transaction Details: #{{ $transaction->id }}</h1>
        </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Transaction Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-center mb-3">
                        <div class="w-16 h-16 mx-auto mb-3">
                            <div class="w-full h-full bg-{{ $transaction->status === 'completed' ? 'green' : ($transaction->status === 'pending' ? 'yellow' : 'red') }}-500 text-white rounded-full flex items-center justify-center">
                                <i class="mdi mdi-{{ $transaction->status === 'completed' ? 'check-circle' : ($transaction->status === 'pending' ? 'clock' : 'close-circle') }} text-2xl"></i>
                            </div>
                        </div>
                        <h4 class="mb-1 text-xl font-semibold text-gray-900">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</h4>
                        <p class="text-gray-500">Transaction #{{ $transaction->id }}</p>
                        
                        <div class="mb-3">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $transaction->status === 'completed' ? 'green' : ($transaction->status === 'pending' ? 'yellow' : 'red') }}-100 text-{{ $transaction->status === 'completed' ? 'green' : ($transaction->status === 'pending' ? 'yellow' : 'red') }}-800">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mt-3">
                        <h6 class="text-sm font-medium text-gray-900 uppercase tracking-wide mb-4">Transaction Information</h6>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Gateway</p>
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst($transaction->gateway) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Currency</p>
                                <p class="text-sm font-medium text-gray-900">{{ strtoupper($transaction->currency) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Transaction Hash</p>
                                <p class="text-sm font-mono text-gray-900 break-all">{{ $transaction->transaction_hash ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Reference ID</p>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->reference_id ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Created Date</p>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Updated Date</p>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($transaction->completed_at)
                        <div class="mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Completed Date</p>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->completed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($transaction->reviewed_at)
                        <div class="mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Reviewed Date</p>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->reviewed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center" 
                                onclick="showStatusUpdateModal()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Update Status
                        </button>
                    </div>
                </div>
            </div>

        <!-- User Information Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h5 class="text-lg font-medium text-gray-900 mb-4">User Information</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h6 class="text-sm font-medium text-gray-700 mb-3">Customer Details</h6>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">User ID</p>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->user->id }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h6 class="text-sm font-medium text-gray-700 mb-3">Subscription Details</h6>
                        <div class="space-y-3">
                            @if($transaction->subscription)
                                <div>
                                    <p class="text-sm text-gray-500">Plan</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->subscription->plan->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $transaction->subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->subscription->status === 'expired' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($transaction->subscription->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Expires</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->subscription->expires_at ? $transaction->subscription->expires_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No subscription associated</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Payment Details -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h5 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h5>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($transaction->gateway) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Amount</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Fee</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transaction->currency }} {{ number_format($transaction->fee ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Net Amount</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transaction->currency }} {{ number_format(($transaction->amount - ($transaction->fee ?? 0)), 2) }}</p>
                    </div>
                </div>
                
                @if($transaction->metadata)
                <div>
                    <p class="text-sm text-gray-500 mb-2">Additional Data</p>
                    <div class="bg-gray-50 p-3 rounded-md">
                        <pre class="text-xs text-gray-700">{{ json_encode($transaction->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Subscription Status -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h6 class="text-lg font-medium text-gray-900 mb-4">Subscription Status</h6>
            
            @if($transaction->subscription)
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Current Status</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $transaction->subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                               ($transaction->subscription->status === 'expired' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($transaction->subscription->status) }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Plan</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transaction->subscription->plan->name ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Started</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transaction->subscription->created_at->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Expires</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transaction->subscription->expires_at ? $transaction->subscription->expires_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    
                    @if($transaction->subscription->trial_ends_at)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Trial Ends</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transaction->subscription->trial_ends_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-500">No subscription associated with this transaction.</p>
            @endif
        </div>
    </div>

    <!-- Transaction History -->
    @if($transaction->relatedTransactions && $transaction->relatedTransactions->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h5 class="text-lg font-medium text-gray-900 mb-4">Related Transactions</h5>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaction->relatedTransactions as $relatedTransaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $relatedTransaction->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $relatedTransaction->currency }} {{ number_format($relatedTransaction->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $relatedTransaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($relatedTransaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($relatedTransaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $relatedTransaction->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.transactions.show', $relatedTransaction->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Status Update Modal -->
<div id="statusUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Update Transaction Status</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('statusUpdateModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="statusUpdateForm" method="POST" action="{{ route('admin.transactions.update-status', $transaction->id) }}">
            @csrf
            @method('PATCH')
            
            <div class="space-y-4">
                <div>
                    <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                    <select name="status" id="newStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select Status</option>
                        <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ $transaction->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label for="statusReason" class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                    <textarea name="reason" id="statusReason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter reason for status change..."></textarea>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('statusUpdateModal')">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">Update Status</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showStatusUpdateModal() {
    document.getElementById('statusUpdateModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});

// Handle form submission
document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Status updated successfully!');
            // Reload page to show updated status
            window.location.reload();
        } else {
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
    });
});
</script>
@endpush
