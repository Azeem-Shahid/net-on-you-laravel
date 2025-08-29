@extends('admin.layouts.app')

@section('title', 'Payout Batch Details - Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Payout Batch #{{ $payoutBatch->id }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.payouts.index') }}">Payouts</a></li>
                    <li class="breadcrumb-item active">Batch Details</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.payouts.export', $payoutBatch) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Batch Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Batch Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Batch ID:</strong> {{ $payoutBatch->id }}</p>
                            <p><strong>Period:</strong> {{ $payoutBatch->period }}</p>
                            <p><strong>Status:</strong> 
                                @if($payoutBatch->status === 'open')
                                    <span class="badge bg-warning">Open</span>
                                @elseif($payoutBatch->status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-success">Closed</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Amount:</strong> ${{ number_format($payoutBatch->total_amount, 2) }}</p>
                            <p><strong>Items Count:</strong> {{ $payoutBatch->items->count() }}</p>
                            <p><strong>Created:</strong> {{ $payoutBatch->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @if($payoutBatch->notes)
                        <div class="mt-3">
                            <p><strong>Notes:</strong></p>
                            <p class="text-muted">{{ $payoutBatch->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Status Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Queued:</strong> {{ $payoutBatch->items->where('status', 'queued')->count() }}</p>
                            <p><strong>Sent:</strong> {{ $payoutBatch->items->where('status', 'sent')->count() }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Paid:</strong> {{ $payoutBatch->items->where('status', 'paid')->count() }}</p>
                            <p><strong>Failed:</strong> {{ $payoutBatch->items->where('status', 'failed')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Items -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Payout Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Earner</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Commission Count</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payoutBatch->items as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $item->earner->name }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $item->earner->id }}</small>
                                    </div>
                                </td>
                                <td>${{ number_format($item->amount, 2) }}</td>
                                <td>
                                    @if($item->status === 'queued')
                                        <span class="badge bg-warning">Queued</span>
                                    @elseif($item->status === 'sent')
                                        <span class="badge bg-info">Sent</span>
                                    @elseif($item->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                                <td>{{ count($item->commission_ids) }}</td>
                                <td>{{ $item->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($item->status === 'queued')
                                            <form method="POST" action="{{ route('admin.payouts.mark-sent', $item) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info">
                                                    <i class="fas fa-paper-plane"></i> Mark Sent
                                                </button>
                                            </form>
                                        @elseif($item->status === 'sent')
                                            <form method="POST" action="{{ route('admin.payouts.mark-paid', $item) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Mark Paid
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="showMarkFailedModal({{ $item->id }})">
                                                <i class="fas fa-times"></i> Mark Failed
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No payout items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Mark Failed Modal -->
<div class="modal fade" id="markFailedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="markFailedForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mark Payout as Failed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Failure</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Mark Failed</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showMarkFailedModal(itemId) {
    document.getElementById('markFailedForm').action = `/admin/payouts/items/${itemId}/mark-failed`;
    new bootstrap.Modal(document.getElementById('markFailedModal')).show();
}
</script>
@endpush
