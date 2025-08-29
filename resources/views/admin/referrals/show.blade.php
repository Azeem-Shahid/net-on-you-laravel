@extends('admin.layouts.app')

@section('title', 'Referral Tree - Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Referral Tree for {{ $user->name }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.referrals.index') }}">Referrals</a></li>
                    <li class="breadcrumb-item active">Referral Tree</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>User ID:</strong> {{ $user->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Referrer ID:</strong> {{ $user->referrer_id ?: 'None' }}</p>
                            <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Commission Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Total Earned:</strong> ${{ number_format($commissionStats['total_earned'], 2) }}</p>
                            <p><strong>Eligible:</strong> ${{ number_format($commissionStats['eligible_earned'], 2) }}</p>
                            <p><strong>Ineligible:</strong> ${{ number_format($commissionStats['ineligible_earned'], 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Pending Payout:</strong> ${{ number_format($commissionStats['pending_payout'], 2) }}</p>
                            <p><strong>Paid Out:</strong> ${{ number_format($commissionStats['paid_out'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Tree -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Referral Tree (6 Levels Down)</h5>
        </div>
        <div class="card-body">
            @for($level = 1; $level <= 6; $level++)
                <div class="mb-4">
                    <h6 class="text-primary mb-3">Level {{ $level }}</h6>
                    @if(isset($referralTree[$level]) && $referralTree[$level]->count() > 0)
                        <div class="row">
                            @foreach($referralTree[$level] as $referredUser)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-medium">
                                                            {{ substr($referredUser->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">{{ $referredUser->name }}</h6>
                                                    <p class="text-muted mb-1 small">ID: {{ $referredUser->id }}</p>
                                                    <p class="text-muted mb-0 small">{{ $referredUser->email }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No users at this level.</p>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Monthly Commission Breakdown</h5>
        </div>
        <div class="card-body">
            @if($monthlyBreakdown->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Amount</th>
                                <th>Commission Count</th>
                                <th>Average per Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyBreakdown as $breakdown)
                                <tr>
                                    <td>{{ $breakdown->month }}</td>
                                    <td>${{ number_format($breakdown->total_amount, 2) }}</td>
                                    <td>{{ $breakdown->count }}</td>
                                    <td>${{ number_format($breakdown->total_amount / $breakdown->count, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center">No commission data available.</p>
            @endif
        </div>
    </div>
</div>
@endsection
