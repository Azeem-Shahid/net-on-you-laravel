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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="card-header">
                    <h5 class="card-title">Commission Information</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <p><strong>Commission ID:</strong> {{ $commission->id }}</p>
                            <p><strong>Level:</strong> <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">Level {{ $commission->level }}</span></p>
                            <p><strong>Amount:</strong> ${{ number_format($commission->amount, 2) }}</p>
                            <p><strong>Month:</strong> {{ $commission->month }}</p>
                        </div>
                        <div class="lg:col-span-6">
                            <p><strong>Eligibility:</strong> 
                                @if($commission->eligibility === 'eligible')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Eligible</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Ineligible</span>
                                @endif
                            </p>
                            <p><strong>Payout Status:</strong> 
                                @if($commission->payout_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($commission->payout_status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Void</span>
                                @endif
                            </p>
                            <p><strong>Created:</strong> {{ $commission->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="card-header">
                    <h5 class="card-title">User Information</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-6">
                            <p><strong>Earner:</strong></p>
                            <p class="mb-2">{{ $commission->earner->name }}</p>
                            <p class="text-muted small">ID: {{ $commission->earner->id }}</p>
                            <p class="text-muted small">{{ $commission->earner->email }}</p>
                        </div>
                        <div class="lg:col-span-6">
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
    <div class="bg-white rounded-lg shadow-md">
        <div class="card-header">
            <h5 class="card-title">Transaction Information</h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-6">
                    <p><strong>Transaction ID:</strong> {{ $commission->transaction_id }}</p>
                    <p><strong>Amount:</strong> ${{ number_format($commission->transaction->amount, 2) }}</p>
                    <p><strong>Currency:</strong> {{ $commission->transaction->currency }}</p>
                </div>
                <div class="lg:col-span-6">
                    <p><strong>Gateway:</strong> {{ $commission->transaction->gateway }}</p>
                    <p><strong>Status:</strong> 
                        @if($commission->transaction->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                        @elseif($commission->transaction->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($commission->transaction->status) }}</span>
                        @endif
                    </p>
                    <p><strong>Date:</strong> {{ $commission->transaction->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Actions -->
    @if($commission->payout_status === 'pending')
        <div class="bg-white rounded-lg shadow-md">
            <div class="card-header">
                <h5 class="card-title">Commission Actions</h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-4">
                        <button type="button" class="btn btn-warning w-100" onclick="showAdjustModal({{ $commission->id }}, {{ $commission->amount }})">
                            <i class="fas fa-edit"></i> Adjust Amount
                        </button>
                    </div>
                    <div class="lg:col-span-4">
                        <button type="button" class="btn btn-danger w-100" onclick="showVoidModal({{ $commission->id }})">
                            <i class="fas fa-ban"></i> Void Commission
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($commission->payout_status === 'void')
        <div class="bg-white rounded-lg shadow-md">
            <div class="card-header">
                <h5 class="card-title">Commission Actions</h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-4">
                        <button type="button" class="btn btn-success w-100" onclick="showRestoreModal({{ $commission->id }})">
                            <i class="fas fa-undo"></i> Restore Commission
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Audit History -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="card-header">
            <h5 class="card-title">Audit History</h5>
        </div>
        <div class="p-6">
            @if($commission->audits->count() > 0)
                <div class="overflow-x-auto">
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
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Adjust</span>
                                        @elseif($audit->action === 'void')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Void</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Restore</span>
                                        @endif
                                    </td>
                                    <td>{{ $audit->reason }}</td>
                                    <td>
                                        @if($audit->action === 'adjust')
                                            <small class="text-gray-500">
                                                Amount: ${{ number_format($audit->before_payload['amount'], 2) }} → 
                                                ${{ number_format($audit->after_payload['amount'], 2) }}
                                            </small>
                                        @elseif($audit->action === 'void')
                                            <small class="text-gray-500">
                                                Status: {{ ucfirst($audit->before_payload['payout_status']) }} → 
                                                {{ ucfirst($audit->after_payload['payout_status']) }}
                                            </small>
                                        @else
                                            <small class="text-gray-500">
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
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-gray-700 hover:text-gray-900" onclick="this.parentElement.remove()"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">New Amount</label>
                        <input type="number" name="new_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors" onclick="this.parentElement.remove()">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition-colors">Adjust</button>
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
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-gray-700 hover:text-gray-900" onclick="this.parentElement.remove()"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors" onclick="this.parentElement.remove()">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors">Void</button>
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
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-gray-700 hover:text-gray-900" onclick="this.parentElement.remove()"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors" onclick="this.parentElement.remove()">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition-colors">Restore</button>
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
