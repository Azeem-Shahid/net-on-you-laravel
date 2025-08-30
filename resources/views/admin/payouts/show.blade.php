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
                <a href="{{ route('admin.payouts.export', $payoutBatch) }}" class="inline-flex items-center px-4 py-2 bg-action text-primary font-medium rounded-lg hover:bg-action/90 transition-colors">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Batch Information -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="card-header">
                    <h5 class="card-title">Batch Information</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <p><strong>Batch ID:</strong> {{ $payoutBatch->id }}</p>
                            <p><strong>Period:</strong> {{ $payoutBatch->period }}</p>
                            <p><strong>Status:</strong> 
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
                            <p><strong>Total Amount:</strong> ${{ number_format($payoutBatch->total_amount, 2) }}</p>
                            <p><strong>Items Count:</strong> {{ $payoutBatch->items->count() }}</p>
                            <p><strong>Created:</strong> {{ $payoutBatch->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @if($payoutBatch->notes)
                        <div class="mt-3">
                            <p><strong>Notes:</strong></p>
                            <p class="text-gray-500">{{ $payoutBatch->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="lg:col-span-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="card-header">
                    <h5 class="card-title">Status Summary</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <p><strong>Queued:</strong> {{ $payoutBatch->items->where('status', 'queued')->count() }}</p>
                            <p><strong>Sent:</strong> {{ $payoutBatch->items->where('status', 'sent')->count() }}</p>
                        </div>
                        <div class="lg:col-span-6">
                            <p><strong>Paid:</strong> {{ $payoutBatch->items->where('status', 'paid')->count() }}</p>
                            <p><strong>Failed:</strong> {{ $payoutBatch->items->where('status', 'failed')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Items -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="card-header">
            <h5 class="card-title">Payout Items</h5>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
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
                                        <small class="text-gray-500">ID: {{ $item->earner->id }}</small>
                                    </div>
                                </td>
                                <td>${{ number_format($item->amount, 2) }}</td>
                                <td>
                                    @if($item->status === 'queued')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Queued</span>
                                    @elseif($item->status === 'sent')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Sent</span>
                                    @elseif($item->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                    @endif
                                </td>
                                <td>{{ count($item->commission_ids) }}</td>
                                <td>{{ $item->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="flex space-x-2" class="flex space-x-2">
                                        @if($item->status === 'queued')
                                            <form method="POST" action="{{ route('admin.payouts.mark-sent', $item) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors">
                                                    <i class="fas fa-paper-plane"></i> Mark Sent
                                                </button>
                                            </form>
                                        @elseif($item->status === 'sent')
                                            <form method="POST" action="{{ route('admin.payouts.mark-paid', $item) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded hover:bg-green-600 transition-colors">
                                                    <i class="fas fa-check"></i> Mark Paid
                                                </button>
                                            </form>
                                            <button type="button" class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded hover:bg-red-600 transition-colors" onclick="showMarkFailedModal({{ $item->id }})">
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
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-gray-700 hover:text-gray-900" onclick="this.parentElement.remove()"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Failure</label>
                        <textarea name="reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors" onclick="this.parentElement.remove()">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors">Mark Failed</button>
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
