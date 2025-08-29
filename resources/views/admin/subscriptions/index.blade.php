@extends('admin.layouts.app')

@section('title', 'Subscription Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
            <div>
                    <h1 class="text-2xl font-bold text-gray-900">Subscription Management</h1>
                    <p class="text-sm text-gray-600">Manage all user subscriptions</p>
            </div>
                <div class="flex items-center space-x-4">
                    <button onclick="openBulkActionsModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Bulk Actions
                    </button>
                    <a href="{{ route('admin.subscriptions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Subscription
                </a>
                    <a href="{{ route('admin.subscriptions.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Export CSV
                </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
                <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="User name or email">
                </div>
                <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                        <label for="plan" class="block text-sm font-medium text-gray-700">Plan</label>
                        <select name="plan" id="plan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Plans</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan }}" {{ request('plan') === $plan ? 'selected' : '' }}>{{ $plan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Types</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Subscriptions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($subscriptions as $subscription)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="subscription-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                           value="{{ $subscription->id }}">
                                </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">{{ substr($subscription->user->name, 0, 2) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $subscription->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $subscription->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $subscription->plan_name }}</div>
                                    <div class="text-sm text-gray-500">{{ ucfirst($subscription->subscription_type) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($subscription->status === 'active') bg-green-100 text-green-800
                                        @elseif($subscription->status === 'inactive') bg-yellow-100 text-yellow-800
                                    @elseif($subscription->status === 'expired') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($subscription->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>Start: {{ $subscription->start_date->format('M d, Y') }}</div>
                                    <div>End: {{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <button onclick="toggleSubscriptionStatus({{ $subscription->id }}, '{{ $subscription->status }}')" 
                                                class="text-yellow-600 hover:text-yellow-900">
                                            {{ $subscription->status === 'active' ? 'Disable' : 'Enable' }}
                                        </button>
                                        <button onclick="extendSubscription({{ $subscription->id }})" 
                                                class="text-green-600 hover:text-green-900">Extend</button>
                                        <button onclick="cancelSubscription({{ $subscription->id }})" 
                                                class="text-red-600 hover:text-red-900">Cancel</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No subscriptions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $subscriptions->links() }}
                                </div>
                            </div>
                            </div>
                        </div>

<!-- Bulk Actions Modal -->
<div id="bulkActionsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
            <form id="bulkActionsForm">
                <div class="mb-4">
                    <label for="bulkAction" class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                    <select id="bulkAction" name="action" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Action</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="cancel">Cancel</option>
                        <option value="extend">Extend</option>
                    </select>
                        </div>

                <div id="extensionMonthsDiv" class="mb-4 hidden">
                    <label for="extensionMonths" class="block text-sm font-medium text-gray-700 mb-2">Extension (months)</label>
                    <input type="number" id="extensionMonths" name="extension_months" min="1" max="120" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBulkActionsModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Apply
                    </button>
                </div>
            </form>
                                </div>
                            </div>
                        </div>

<!-- Extension Modal -->
<div id="extensionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Extend Subscription</h3>
            <form id="extensionForm">
                <input type="hidden" id="extensionSubscriptionId">
                <div class="mb-4">
                    <label for="extensionMonths" class="block text-sm font-medium text-gray-700 mb-2">Extension (months)</label>
                    <input type="number" id="extensionMonthsInput" name="extension_months" min="1" max="120" 
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
// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.subscription-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Bulk actions modal
function openBulkActionsModal() {
    const selectedCount = document.querySelectorAll('.subscription-checkbox:checked').length;
    if (selectedCount === 0) {
        alert('Please select at least one subscription');
        return;
    }
    document.getElementById('bulkActionsModal').classList.remove('hidden');
}

function closeBulkActionsModal() {
    document.getElementById('bulkActionsModal').classList.add('hidden');
}

// Show/hide extension months field based on action
document.getElementById('bulkAction').addEventListener('change', function() {
    const extensionDiv = document.getElementById('extensionMonthsDiv');
    if (this.value === 'extend') {
        extensionDiv.classList.remove('hidden');
    } else {
        extensionDiv.classList.add('hidden');
    }
});

// Handle bulk actions form submission
document.getElementById('bulkActionsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedIds = Array.from(document.querySelectorAll('.subscription-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one subscription');
        return;
    }
    
    const formData = new FormData(this);
    formData.append('subscription_ids', JSON.stringify(selectedIds));
    
    fetch('{{ route("admin.subscriptions.bulk-update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            subscription_ids: selectedIds,
            action: formData.get('action'),
            extension_months: formData.get('extension_months')
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
    
    closeBulkActionsModal();
});

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
    const months = document.getElementById('extensionMonthsInput').value;
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
