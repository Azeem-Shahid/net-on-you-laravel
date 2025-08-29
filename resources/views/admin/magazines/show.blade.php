@extends('admin.layouts.app')

@section('title', 'Magazine Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.magazines.index') }}">Magazines</a></li>
                        <li class="breadcrumb-item active">Magazine Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Magazine Details: {{ $magazine->title }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Magazine Info Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-light text-primary rounded">
                                <i class="mdi mdi-file-pdf-box font-24"></i>
                            </div>
                        </div>
                        <h4 class="mb-1">{{ $magazine->title }}</h4>
                        <p class="text-muted">{{ Str::limit($magazine->description, 100) }}</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-{{ $magazine->status === 'active' ? 'success' : 'secondary' }} fs-6">
                                {{ ucfirst($magazine->status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-3">
                        <h6 class="text-uppercase">File Information</h6>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1 text-muted">File Name</p>
                                <p class="mb-3 text-break">{{ $magazine->file_name }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-muted">File Size</p>
                                <p class="mb-3">{{ formatBytes($magazine->file_size) }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1 text-muted">File Type</p>
                                <p class="mb-3">{{ $magazine->mime_type }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-muted">Upload Date</p>
                                <p class="mb-3">{{ $magazine->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        @if($magazine->published_at)
                        <div class="row">
                            <div class="col-12">
                                <p class="mb-1 text-muted">Published Date</p>
                                <p class="mb-3">{{ $magazine->published_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <p class="mb-1 text-muted">Uploaded By</p>
                                <p class="mb-3">{{ $magazine->admin->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.magazines.edit', $magazine) }}" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="mdi mdi-pencil"></i> Edit Magazine
                        </a>
                        
                        <button type="button" class="btn btn-{{ $magazine->status === 'active' ? 'warning' : 'success' }} btn-sm w-100 mb-2" 
                                onclick="toggleStatus({{ $magazine->id }})">
                            <i class="mdi mdi-{{ $magazine->status === 'active' ? 'pause' : 'play' }}"></i>
                            {{ $magazine->status === 'active' ? 'Deactivate' : 'Activate' }}
                        </button>

                        <a href="{{ route('admin.magazines.download', $magazine) }}" class="btn btn-info btn-sm w-100 mb-2">
                            <i class="mdi mdi-download"></i> Download File
                        </a>

                        <button type="button" class="btn btn-danger btn-sm w-100" 
                                onclick="deleteMagazine({{ $magazine->id }})">
                            <i class="mdi mdi-delete"></i> Delete Magazine
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Magazine Statistics & Access -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered" id="magazineTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="access-tab" data-bs-toggle="tab" data-bs-target="#access" type="button" role="tab">
                                Access Statistics ({{ $magazine->entitlements->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                                Users with Access
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">
                                Recent Activity
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="magazineTabsContent">
                        <!-- Access Statistics Tab -->
                        <div class="tab-pane fade show active" id="access" role="tabpanel">
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h3 class="text-primary mb-1">{{ $magazine->entitlements->count() }}</h3>
                                            <p class="text-muted mb-0">Total Access</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h3 class="text-success mb-1">{{ $magazine->entitlements->where('expires_at', '>', now())->count() }}</h3>
                                            <p class="text-muted mb-0">Active Access</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h3 class="text-warning mb-1">{{ $magazine->entitlements->where('expires_at', '<=', now())->count() }}</h3>
                                            <p class="text-muted mb-0">Expired Access</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h3 class="text-info mb-1">{{ $magazine->entitlements->where('expires_at', null)->count() }}</h3>
                                            <p class="text-muted mb-0">Lifetime Access</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6>Access Timeline</h6>
                                <div class="table-responsive">
                                    <table class="table table-centered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>New Access</th>
                                                <th>Expired Access</th>
                                                <th>Net Change</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $months = collect();
                                                for ($i = 5; $i >= 0; $i--) {
                                                    $date = now()->subMonths($i);
                                                    $months->push($date);
                                                }
                                            @endphp
                                            
                                            @foreach($months as $month)
                                            @php
                                                $newAccess = $magazine->entitlements->where('created_at', '>=', $month->startOfMonth())->where('created_at', '<', $month->copy()->addMonth()->startOfMonth())->count();
                                                $expiredAccess = $magazine->entitlements->where('expires_at', '>=', $month->startOfMonth())->where('expires_at', '<', $month->copy()->addMonth()->startOfMonth())->count();
                                            @endphp
                                            <tr>
                                                <td>{{ $month->format('M Y') }}</td>
                                                <td><span class="badge bg-success">{{ $newAccess }}</span></td>
                                                <td><span class="badge bg-warning">{{ $expiredAccess }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ ($newAccess - $expiredAccess) >= 0 ? 'info' : 'danger' }}">
                                                        {{ ($newAccess - $expiredAccess) >= 0 ? '+' : '' }}{{ $newAccess - $expiredAccess }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Users with Access Tab -->
                        <div class="tab-pane fade" id="users" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-centered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Access Date</th>
                                            <th>Expiry Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($magazine->entitlements as $entitlement)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.users.show', $entitlement->user) }}" class="text-decoration-none">
                                                    {{ $entitlement->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $entitlement->user->email }}</td>
                                            <td>{{ $entitlement->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($entitlement->expires_at)
                                                    {{ $entitlement->expires_at->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($entitlement->expires_at && $entitlement->expires_at <= now())
                                                    <span class="badge bg-danger">Expired</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="revokeAccess({{ $entitlement->id }})">
                                                    <i class="mdi mdi-close"></i> Revoke
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No users have access to this magazine</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Recent Activity Tab -->
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            <div class="mt-3">
                                <div class="timeline-alt pb-0">
                                    @php
                                        $activities = collect([
                                            ['type' => 'created', 'date' => $magazine->created_at, 'description' => 'Magazine created and uploaded'],
                                            ['type' => 'status_changed', 'date' => $magazine->updated_at, 'description' => 'Magazine status updated'],
                                        ]);
                                        
                                        if ($magazine->entitlements->count() > 0) {
                                            $activities->push([
                                                'type' => 'access_granted',
                                                'date' => $magazine->entitlements->max('created_at'),
                                                'description' => 'First user access granted'
                                            ]);
                                        }
                                    @endphp
                                    
                                    @foreach($activities->sortByDesc('date') as $activity)
                                    <div class="timeline-item">
                                        <i class="mdi mdi-circle bg-info-lighten text-info timeline-icon"></i>
                                        <div class="timeline-item-info">
                                            <a href="#" class="text-info fw-bold mb-1 d-block">{{ $activity['description'] }}</a>
                                            <small class="text-muted">{{ $activity['date']->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleStatus(magazineId) {
    if (confirm('Are you sure you want to change this magazine\'s status?')) {
        fetch(`/admin/magazines/${magazineId}/toggle-status`, {
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

function deleteMagazine(magazineId) {
    if (confirm('Are you sure you want to delete this magazine? This action cannot be undone.')) {
        fetch(`/admin/magazines/${magazineId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.magazines.index") }}';
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

function revokeAccess(entitlementId) {
    if (confirm('Are you sure you want to revoke this user\'s access to the magazine?')) {
        // This would need a new route and controller method
        alert('Access revocation functionality needs to be implemented.');
    }
}
</script>
@endpush

@php
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
@endphp
