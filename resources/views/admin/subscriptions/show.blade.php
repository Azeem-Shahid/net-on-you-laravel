@extends('admin.layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Subscription Details</h1>
                    <p class="text-sm text-gray-600">View and manage subscription information</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Subscription
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Subscription Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-8">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-indigo-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-1">{{ $subscription->plan_name }}</h3>
                            <p class="text-gray-600 mb-3">{{ ucfirst($subscription->subscription_type) }}</p>
                            
                            <div class="mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($subscription->status === 'active') bg-green-100 text-green-800
                                    @elseif($subscription->status === 'inactive') bg-yellow-100 text-yellow-800
                                    @elseif($subscription->status === 'expired') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </div>

                            <div class="text-center">
                                <h4 class="text-3xl font-bold text-gray-900">${{ number_format($subscription->amount, 2) }}</h4>
                                <p class="text-sm text-gray-600">Subscription Amount</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 mt-6 pt-6">
                            <h5 class="text-sm font-medium text-gray-900 uppercase tracking-wide mb-4">Subscription Information</h5>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Start Date</p>
                                    <p class="text-sm text-gray-900">{{ $subscription->start_date->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">End Date</p>
                                    <p class="text-sm text-gray-900">{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Created</p>
                                    <p class="text-sm text-gray-900">{{ $subscription->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Last Updated</p>
                                    <p class="text-sm text-gray-900">{{ $subscription->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-2">
                            <button onclick="toggleSubscriptionStatus({{ $subscription->id }}, '{{ $subscription->status }}')" 
                                    class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                {{ $subscription->status === 'active' ? 'Disable' : 'Enable' }} Subscription
                            </button>
                            <button onclick="extendSubscription({{ $subscription->id }})" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Extend Subscription
                            </button>
                            <button onclick="cancelSubscription({{ $subscription->id }})" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Cancel Subscription
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User and Transaction Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Information -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-xl font-medium text-gray-600">{{ substr($subscription->user->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">{{ $subscription->user->name }}</h4>
                                <p class="text-gray-600">{{ $subscription->user->email }}</p>
                                <p class="text-sm text-gray-500">User ID: {{ $subscription->user->id }}</p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">User Status</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($subscription->user->status === 'active') bg-green-100 text-green-800
                                    @elseif($subscription->user->status === 'blocked') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($subscription->user->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Language</p>
                                <p class="text-sm text-gray-900">{{ strtoupper($subscription->user->language ?? 'N/A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($subscription->notes)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $subscription->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Related Transactions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Related Transactions</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-500 text-center py-4">Transaction history not available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extension Modal -->
<div id="extensionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Extend Subscription</h3>
            <form id="extensionForm">
                <input type="hidden" id="extensionSubscriptionId" value="{{ $subscription->id }}">
                <div class="mb-4">
                    <label for="extensionMonths" class="block text-sm font-medium text-gray-700 mb-2">Extension (months)</label>
                    <input type="number" id="extensionMonths" name="extension_months" min="1" max="120" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="extensionNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="extensionNotes" name="notes" rows="3" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeExtensionModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Extend
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle subscription status
function toggleSubscriptionStatus(subscriptionId, currentStatus) {
    if (confirm(`Are you sure you want to ${currentStatus === 'active' ? 'disable' : 'enable'} this subscription?`)) {
        fetch(`/admin/subscriptions/${subscriptionId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    }
}

// Extend subscription
function extendSubscription(subscriptionId) {
    document.getElementById('extensionSubscriptionId').value = subscriptionId;
    document.getElementById('extensionModal').classList.remove('hidden');
}

function closeExtensionModal() {
    document.getElementById('extensionModal').classList.add('hidden');
}

// Handle extension form submission
document.getElementById('extensionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const subscriptionId = document.getElementById('extensionSubscriptionId').value;
    const months = document.getElementById('extensionMonths').value;
    const notes = document.getElementById('extensionNotes').value;
    
    fetch(`/admin/subscriptions/${subscriptionId}/extend`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            extension_months: months,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request.');
    });
    
    closeExtensionModal();
});

// Cancel subscription
function cancelSubscription(subscriptionId) {
    if (confirm('Are you sure you want to cancel this subscription? This action cannot be undone.')) {
        fetch(`/admin/subscriptions/${subscriptionId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    }
}
</script>
@endpush
