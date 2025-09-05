@extends('admin.layouts.app')

@section('title', 'Payout Batch Details - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Payout Batch #{{ $payoutBatch->id }}</h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('admin.payouts.index') }}" class="text-gray-500 hover:text-gray-700">Payouts</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700 font-medium">Batch Details</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('admin.payouts.export', $payoutBatch) }}" class="inline-flex items-center px-4 py-2 bg-action text-primary font-medium rounded-lg hover:bg-action/90 transition-colors">
                    <i class="fas fa-download mr-2"></i> Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Batch Information -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <div class="lg:col-span-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-medium text-gray-900">Batch Information</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <p class="mb-2"><span class="font-medium text-gray-700">Batch ID:</span> {{ $payoutBatch->id }}</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Period:</span> {{ $payoutBatch->period }}</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Status:</span> 
                                @if($payoutBatch->status === 'open')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Open</span>
                                @elseif($payoutBatch->status === 'processing')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Processing</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Closed</span>
                                @endif
                            </p>
                        </div>
                        <div class="lg:col-span-6">
                            <p class="mb-2"><span class="font-medium text-gray-700">Total Amount:</span> ${{ number_format($payoutBatch->total_amount, 2) }}</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Items Count:</span> {{ $payoutBatch->items->count() }}</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Created:</span> {{ $payoutBatch->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @if($payoutBatch->notes)
                        <div class="mt-4">
                            <p class="font-medium text-gray-700 mb-2">Notes:</p>
                            <p class="text-gray-500">{{ $payoutBatch->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="lg:col-span-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-medium text-gray-900">Status Summary</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <p class="mb-2"><span class="font-medium text-gray-700">Queued:</span> {{ $payoutBatch->items->where('status', 'queued')->count() }}</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Sent:</span> {{ $payoutBatch->items->where('status', 'sent')->count() }}</p>
                        </div>
                        <div class="lg:col-span-6">
                            <p class="mb-2"><span class="font-medium text-gray-700">Paid:</span> {{ $payoutBatch->items->where('status', 'paid')->count() }}</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Failed:</span> {{ $payoutBatch->items->where('status', 'failed')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Items -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-medium text-gray-900">Payout Items</h5>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Earner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payoutBatch->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ strtoupper(substr($item->earner->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->earner->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->earner->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($item->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $item->status === 'queued' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($item->status === 'sent' ? 'bg-blue-100 text-blue-800' : 
                                       ($item->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->commissions->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-900" onclick="viewPayoutItem({{ $item->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($item->status === 'failed')
                                    <button class="text-red-600 hover:text-red-900" onclick="markFailed({{ $item->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No payout items found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payout Item Details Modal -->
<div id="payoutItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Payout Item Details</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('payoutItemModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="payoutItemContent">
            <!-- Content will be loaded here -->
        </div>
        
        <div class="flex items-center justify-end pt-6 border-t border-gray-200">
            <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('payoutItemModal')">Close</button>
        </div>
    </div>
</div>

<!-- Mark Failed Modal -->
<div id="markFailedModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Mark as Failed</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('markFailedModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="markFailedForm">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="failureReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Failure</label>
                    <textarea name="reason" id="failureReason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" required></textarea>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('markFailedModal')">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors duration-200">Mark as Failed</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewPayoutItem(itemId) {
    // Load payout item details via AJAX
    fetch(`/admin/payout-items/${itemId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('payoutItemContent').innerHTML = data.html;
                document.getElementById('payoutItemModal').classList.remove('hidden');
            } else {
                alert('Error loading payout item details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading payout item details');
        });
}

function markFailed(itemId) {
    document.getElementById('markFailedForm').action = `/admin/payout-items/${itemId}/mark-failed`;
    document.getElementById('markFailedModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Handle mark failed form submission
document.getElementById('markFailedForm').addEventListener('submit', function(e) {
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
            alert('Payout item marked as failed successfully');
            closeModal('markFailedModal');
            window.location.reload();
        } else {
            alert('Error marking payout item as failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking payout item as failed');
    });
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});
</script>
@endpush
