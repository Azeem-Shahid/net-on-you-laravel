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
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }} text-white rounded">
                                <i class="mdi mdi-{{ $transaction->status === 'completed' ? 'check-circle' : ($transaction->status === 'pending' ? 'clock' : 'close-circle') }} font-24"></i>
                            </div>
                        </div>
                        <h4 class="mb-1">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</h4>
                        <p class="text-gray-500">Transaction #{{ $transaction->id }}</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }} fs-6">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-3">
                        <h6 class="text-uppercase">Transaction Information</h6>
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="col-6">
                                <p class="mb-1 text-muted">Gateway</p>
                                <p class="mb-3">{{ ucfirst($transaction->gateway) }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-muted">Currency</p>
                                <p class="mb-3">{{ strtoupper($transaction->currency) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="col-6">
                                <p class="mb-1 text-muted">Transaction Hash</p>
                                <p class="mb-3 text-break font-monospace small">{{ $transaction->transaction_hash ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-muted">Reference ID</p>
                                <p class="mb-3">{{ $transaction->reference_id ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="col-6">
                                <p class="mb-1 text-muted">Created Date</p>
                                <p class="mb-3">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-muted">Updated Date</p>
                                <p class="mb-3">{{ $transaction->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($transaction->completed_at)
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="col-12">
                                <p class="mb-1 text-muted">Completed Date</p>
                                <p class="mb-3">{{ $transaction->completed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($transaction->reviewed_at)
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="col-12">
                                <p class="mb-1 text-muted">Reviewed Date</p>
                                <p class="mb-3">{{ $transaction->reviewed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center" 
                                onclick="showStatusUpdateModal()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Update Status
                        </button>
                        
                        @if(!$transaction->reviewed_at)
                        <button type="button" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center" 
                                onclick="markReviewed({{ $transaction->id }})">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark as Reviewed
                        </button>
                        @endif

                        <a href="{{ route('admin.transactions.index') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User & Commission Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button class="border-b-2 border-blue-500 text-blue-600 whitespace-nowrap py-2 px-1 text-sm font-medium" id="user-tab" onclick="switchTab('user')">
                            User Information
                        </button>
                        <button class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 text-sm font-medium" id="commissions-tab" onclick="switchTab('commissions')">
                            Commissions ({{ $transaction->commissions->count() }})
                        </button>
                        <button class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 text-sm font-medium" id="history-tab" onclick="switchTab('history')">
                            Transaction History
                        </button>
                    </nav>

                    <div class="tab-content" id="transactionTabsContent">
                        <!-- User Information Tab -->
                        <div class="tab-pane active" id="user" role="tabpanel">
                            <div class="mt-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h6 class="text-lg font-medium text-gray-900 mb-4">User Details</h6>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="avatar-sm me-3">
                                                        <div class="avatar-title bg-primary rounded-circle text-white">
                                                            {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $transaction->user->name }}</h6>
                                                        <p class="text-muted mb-0">{{ $transaction->user->email }}</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                                    <div class="col-6">
                                                        <p class="mb-1 text-muted">Status</p>
                                                        <span class="badge bg-{{ $transaction->user->status === 'active' ? 'success' : ($transaction->user->status === 'blocked' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($transaction->user->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-1 text-muted">Joined</p>
                                                        <p class="mb-0">{{ $transaction->user->created_at->format('M d, Y') }}</p>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <a href="{{ route('admin.users.show', $transaction->user) }}" class="inline-flex items-center px-3 py-1.5 bg-transparent border border-action text-action text-sm font-medium rounded hover:bg-action hover:text-primary transition-colors">
                                                        <i class="mdi mdi-eye"></i> View User Profile
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lg:col-span-6">
                                        <div class="card border-0 bg-light">
                                            <div class="p-6">
                                                <h6 class="card-title">Subscription Status</h6>
                                                @if($transaction->user->subscription_end_date)
                                                    @if($transaction->user->subscription_end_date > now())
                                                        <div class="text-success mb-2">
                                                            <i class="mdi mdi-check-circle"></i> Active Subscription
                                                        </div>
                                                        <p class="mb-1 text-muted">Expires: {{ $transaction->user->subscription_end_date->format('M d, Y') }}</p>
                                                    @else
                                                        <div class="text-danger mb-2">
                                                            <i class="mdi mdi-close-circle"></i> Expired Subscription
                                                        </div>
                                                        <p class="mb-1 text-muted">Expired: {{ $transaction->user->subscription_end_date->format('M d, Y') }}</p>
                                                    @endif
                                                @else
                                                    <div class="text-muted mb-2">
                                                        <i class="mdi mdi-information"></i> No Subscription
                                                    </div>
                                                @endif

                                                <div class="mt-3">
                                                    <p class="mb-1 text-muted">Wallet Address</p>
                                                    <p class="mb-0 font-monospace small">{{ $transaction->user->wallet_address ?? 'Not set' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commissions Tab -->
                        <div class="tab-pane fade" id="commissions" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-centered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transaction->commissions as $commission)
                                        <tr>
                                            <td>{{ $commission->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $commission->user) }}" class="text-decoration-none">
                                                    {{ $commission->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $commission->amount }}</td>
                                            <td>{{ ucfirst($commission->type) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $commission->payout_status === 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($commission->payout_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $commission->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No commissions generated from this transaction</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Transaction History Tab -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <div class="mt-3">
                                <div class="timeline-alt pb-0">
                                    <div class="timeline-item">
                                        <i class="mdi mdi-circle bg-primary-lighten text-primary timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-primary fw-bold mb-1 d-block">Transaction Created</a>
                                            <small class="text-gray-500">{{ $transaction->created_at->diffForHumans() }}</small>
                                            <p class="text-muted mb-0">Transaction initiated with {{ $transaction->gateway }} gateway</p>
                                        </div>
                                    </div>

                                    @if($transaction->status !== 'pending')
                                    <div class="timeline-item">
                                        <i class="mdi mdi-circle bg-{{ $transaction->status === 'completed' ? 'success' : 'danger' }}-lighten text-{{ $transaction->status === 'completed' ? 'success' : 'danger' }} timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-{{ $transaction->status === 'completed' ? 'success' : 'danger' }} fw-bold mb-1 d-block">
                                                Status Updated to {{ ucfirst($transaction->status) }}
                                            </a>
                                            <small class="text-gray-500">{{ $transaction->updated_at->diffForHumans() }}</small>
                                            @if($transaction->status === 'completed')
                                                <p class="text-muted mb-0">Payment successfully processed</p>
                                            @else
                                                <p class="text-muted mb-0">Transaction failed or was cancelled</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->completed_at)
                                    <div class="timeline-item">
                                        <i class="mdi mdi-circle bg-success-lighten text-success timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-success fw-bold mb-1 d-block">Transaction Completed</a>
                                            <small class="text-gray-500">{{ $transaction->completed_at->diffForHumans() }}</small>
                                            <p class="text-muted mb-0">Payment confirmed and processed</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->reviewed_at)
                                    <div class="timeline-item">
                                        <i class="mdi mdi-circle bg-info-lighten text-info timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-info fw-bold mb-1 d-block">Marked as Reviewed</a>
                                            <small class="text-gray-500">{{ $transaction->reviewed_at->diffForHumans() }}</small>
                                            <p class="text-muted mb-0">Admin reviewed this transaction</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Transaction Status</h5>
                <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-gray-700 hover:text-gray-900" onclick="this.parentElement.remove()"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $transaction->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors" onclick="this.parentElement.remove()">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-action text-primary font-medium rounded-lg hover:bg-action/90 transition-colors">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function switchTab(tabName) {
    // Hide all tab panes
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('nav button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab pane
    document.getElementById(tabName).classList.remove('hidden');
    
    // Activate selected tab
    document.getElementById(tabName + '-tab').classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(tabName + '-tab').classList.add('border-blue-500', 'text-blue-600');
}
</script>
<script>
function showStatusUpdateModal() {
    const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    modal.show();
}

function markReviewed(transactionId) {
    if (confirm('Mark this transaction as reviewed?')) {
        fetch(`/admin/transactions/${transactionId}/mark-reviewed`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
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

// Status update form submission
document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const transactionId = {{ $transaction->id }};
    
    fetch(`/admin/transactions/${transactionId}/status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request.');
    });
});
</script>
@endpush
