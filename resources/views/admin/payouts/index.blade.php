@extends('admin.layouts.app')

@section('title', 'Payout Management - Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Payout Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payouts</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['total_batches'] }}</h4>
                            <p class="text-muted mb-0">Total Batches</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['open_batches'] }}</h4>
                            <p class="text-muted mb-0">Open</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['processing_batches'] }}</h4>
                            <p class="text-muted mb-0">Processing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['closed_batches'] }}</h4>
                            <p class="text-muted mb-0">Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">${{ number_format($stats['total_paid'], 2) }}</h4>
                            <p class="text-muted mb-0">Total Paid Out</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Batches Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Items Count</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payoutBatches as $batch)
                            <tr>
                                <td>{{ $batch->id }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $batch->period }}</span>
                                </td>
                                <td>
                                    @if($batch->status === 'open')
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($batch->status === 'processing')
                                        <span class="badge bg-info">Processing</span>
                                    @else
                                        <span class="badge bg-success">Closed</span>
                                    @endif
                                </td>
                                <td>${{ number_format($batch->total_amount, 2) }}</td>
                                <td>{{ $batch->items->count() }}</td>
                                <td>{{ $batch->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.payouts.show', $batch) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($batch->status === 'open')
                                            <form method="POST" action="{{ route('admin.payouts.start-processing', $batch) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-play"></i> Start
                                                </button>
                                            </form>
                                        @elseif($batch->status === 'processing')
                                            <form method="POST" action="{{ route('admin.payouts.close', $batch) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Close
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.payouts.export', $batch) }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-download"></i> Export
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No payout batches found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $payoutBatches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
