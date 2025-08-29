@extends('admin.layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">Transactions</a></li>
                        <li class="breadcrumb-item active">Transaction Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Transaction Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaction Info Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }} text-white rounded">
                                <i class="mdi mdi-{{ $transaction->status === 'completed' ? 'check-circle' : ($transaction->status === 'pending' ? 'clock' : 'close-circle') }} font-24"></i>
                            </div>
                        </div>
                        <h4 class="mb-1">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</h4>
                        <p class="text-muted">Transaction #{{ $transaction->id }}</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }} fs-6">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-3">
                        <h6 class="text-uppercase">Transaction Information</h6>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1 text-muted">Gateway</p>
                                <p class="mb-3">{{ ucfirst($transaction->gateway) }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-muted">Currency</p>
                                <p class="mb-3">{{ strtoupper($transaction->currency) }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1 text-muted">Transaction Hash</p>
                                <p class="mb-3 text-break font-monospace small">{{ $transaction->transaction_hash ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-muted">Reference ID</p>
                                <p class="mb-3">{{ $transaction->reference_id ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row">
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
                        <div class="row">
                            <div class="col-12">
                                <p class="mb-1 text-muted">Completed Date</p>
                                <p class="mb-3">{{ $transaction->completed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($transaction->reviewed_at)
                        <div class="row">
                            <div class="col-12">
                                <p class="mb-1 text-muted">Reviewed Date</p>
                                <p class="mb-3">{{ $transaction->reviewed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary btn-sm w-100 mb-2" 
                                onclick="showStatusUpdateModal()">
                            <i class="mdi mdi-pencil"></i> Update Status
                        </button>
                        
                        @if(!$transaction->reviewed_at)
                        <button type="button" class="btn btn-info btn-sm w-100 mb-2" 
                                onclick="markReviewed({{ $transaction->id }})">
                            <i class="mdi mdi-check"></i> Mark as Reviewed
                        </button>
                        @endif

                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary btn-sm w-100">
                            <i class="mdi mdi-arrow-left"></i> Back to Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User & Commission Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered" id="transactionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab">
                                User Information
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="commissions-tab" data-bs-toggle="tab" data-bs-target="#commissions" type="button" role="tab">
                                Commissions ({{ $transaction->commissions->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                Transaction History
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="transactionTabsContent">
                        <!-- User Information Tab -->
                        <div class="tab-pane fade show active" id="user" role="tabpanel">
                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">User Details</h6>
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
                                                
                                                <div class="row">
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
                                                    <a href="{{ route('admin.users.show', $transaction->user) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-eye"></i> View User Profile
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
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
                                            <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
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
                                            <small class="text-muted">{{ $transaction->updated_at->diffForHumans() }}</small>
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
                                            <small class="text-muted">{{ $transaction->completed_at->diffForHumans() }}</small>
                                            <p class="text-muted mb-0">Payment confirmed and processed</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->reviewed_at)
                                    <div class="timeline-item">
                                        <i class="mdi mdi-circle bg-info-lighten text-info timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-info fw-bold mb-1 d-block">Marked as Reviewed</a>
                                            <small class="text-muted">{{ $transaction->reviewed_at->diffForHumans() }}</small>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $transaction->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
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
