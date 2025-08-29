@extends('admin.layouts.app')

@section('title', 'Referral Management - Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Referral Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Referrals</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.referrals.export') }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Export CSV
                </a>
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
                            <h4 class="mb-0">{{ $stats['total_referrals'] }}</h4>
                            <p class="text-muted mb-0">Total Referrals</p>
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
                            <h4 class="mb-0">{{ $stats['level_1_count'] }}</h4>
                            <p class="text-muted mb-0">Level 1</p>
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
                            <h4 class="mb-0">{{ $stats['level_2_count'] }}</h4>
                            <p class="text-muted mb-0">Level 2</p>
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
                            <h4 class="mb-0">{{ $stats['level_3_count'] }}</h4>
                            <p class="text-muted mb-0">Level 3</p>
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
                            <h4 class="mb-0">{{ $stats['level_4_count'] }}</h4>
                            <p class="text-muted mb-0">Level 4</p>
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
                            <h4 class="mb-0">{{ $stats['level_5_count'] + $stats['level_6_count'] }}</h4>
                            <p class="text-muted mb-0">Level 5-6</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.referrals.index') }}" class="row">
                <div class="col-md-2">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select">
                        <option value="">All Levels</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ request('level') == $i ? 'selected' : '' }}>Level {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Referrer ID</label>
                    <input type="number" name="referrer_id" class="form-control" value="{{ request('referrer_id') }}" placeholder="Referrer ID">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Referred User ID</label>
                    <input type="number" name="referred_user_id" class="form-control" value="{{ request('referred_user_id') }}" placeholder="Referred User ID">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.referrals.index') }}" class="btn btn-secondary d-block w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Referrals Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Referrer</th>
                            <th>Referred User</th>
                            <th>Level</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $referral)
                            <tr>
                                <td>{{ $referral->id }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $referral->referrer->name }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $referral->referrer->id }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $referral->referredUser->name }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $referral->referredUser->id }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">Level {{ $referral->level }}</span>
                                </td>
                                <td>{{ $referral->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.referrals.show', $referral->referrer) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View Tree
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No referrals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $referrals->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
