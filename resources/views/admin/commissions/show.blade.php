@extends('admin.layouts.app')

@section('title', 'Commission Details - Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Commission #{{ $commission->id }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.commissions.index') }}">Commissions</a></li>
                    <li class="breadcrumb-item active">Commission Details</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Commission Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Commission Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Commission ID:</strong> {{ $commission->id }}</p>
                            <p><strong>Level:</strong> <span class="badge bg-primary">Level {{ $commission->level }}</span></p>
                            <p><strong>Amount:</strong> ${{ number_format($commission->amount, 2) }}</p>
                            <p><strong>Month:</strong> {{ $commission->month }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Eligibility:</strong> 
                                @if($commission->eligibility === 'eligible')
                                    <span class="badge bg-success">Eligible</span>
                                @else
                                    <span class="badge bg-warning">Ineligible</span>
                                @endif
                            </p>
                            <p><strong>Payout Status:</strong> 
                                @if($commission->payout_status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($commission->payout_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-danger">Void</span>
                                @endif
                            </p>
                            <p><strong>Created:</strong> {{ $commission->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Earner:</strong></p>
                            <p class="mb-2">{{ $commission->earner->name }}</p>
                            <p class="text-muted small">ID: {{ $commission->earner->id }}</p>
                            <p class="text-muted small">{{ $commission->earner->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Source User:</strong></p>
                            <p class="mb-2">{{ $commission->sourceUser->name }}</p>
                            <p class="text-muted small">ID: {{ $commission->sourceUser->id }}</p>
                            <p class="text-muted small">{{ $commission->sourceUser->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Information -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Transaction Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Transaction ID:</strong> {{ $commission->transaction_id }}</p>
                    <p><strong>Amount:</strong> ${{ number_format($commission->transaction->amount, 2) }}</p>
                    <p><strong>Currency:</strong> {{ $commission->transaction->currency }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Gateway:</strong> {{ $commission->transaction->gateway }}</p>
                    <p><strong>Status:</strong> 
                        @if($commission->transaction->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($commission->transaction->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @else
                            <span class="badge bg-danger">{{ ucfirst($commission->transaction->status) }}</span>
                        @endif
                    </p>
                    <p><strong>Date:</strong> {{ $commission->transaction->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Actions -->
    @if($commission->payout_status === 'pending')
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Commission Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-warning w-100" onclick="showAdjustModal({{ $commission->id }}, {{ $commission->amount }})">
                            <i class="fas fa-edit"></i> Adjust Amount
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger w-100" onclick="showVoidModal({{ $commission->id }})">
                            <i class="fas fa-ban"></i> Void Commission
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($commission->payout_status === 'void')
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Commission Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-success w-100" onclick="showRestoreModal({{ $commission->id }})">
                            <i class="fas fa-undo"></i> Restore Commission
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Audit History -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Audit History</h5>
        </div>
        <div class="card-body">
            @if($commission->audits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Reason</th>
                                <th>Changes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commission->audits as $audit)
                                <tr>
                                    <td>{{ $audit->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $audit->admin->name }}</td>
                                    <td>
                                        @if($audit->action === 'adjust')
                                            <span class="badge bg-warning">Adjust</span>
                                        @elseif($audit->action === 'void')
                                            <span class="badge bg-danger">Void</span>
                                        @else
                                            <span class="badge bg-success">Restore</span>
                                        @endif
                                    </td>
                                    <td>{{ $audit->reason }}</td>
                                    <td>
                                        @if($audit->action === 'adjust')
                                            <small class="text-muted">
                                                Amount: ${{ number_format($audit->before_payload['amount'], 2) }} → 
                                                ${{ number_format($audit->after_payload['amount'], 2) }}
                                            </small>
                                        @elseif($audit->action === 'void')
                                            <small class="text-muted">
                                                Status: {{ ucfirst($audit->before_payload['payout_status']) }} → 
                                                {{ ucfirst($audit->after_payload['payout_status']) }}
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                Status: {{ ucfirst($audit->before_payload['payout_status']) }} → 
                                                {{ ucfirst($audit->after_payload['payout_status']) }}
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center">No audit history available.</p>
            @endif
        </div>
    </div>
</div>

<!-- Adjust Commission Modal -->
<div class="modal fade" id="adjustCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="adjustCommissionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Commission Amount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">New Amount</label>
                        <input type="number" name="new_amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Adjust</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Void Commission Modal -->
<div class="modal fade" id="voidCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="voidCommissionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Void Commission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Void</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Commission Modal -->
<div class="modal fade" id="restoreCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="restoreCommissionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Restore Commission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Restore</button>
                </div>
            </form>
        </div>
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
</script>
@endpush
