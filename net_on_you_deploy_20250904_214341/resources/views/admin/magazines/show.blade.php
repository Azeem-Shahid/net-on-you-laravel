@extends('admin.layouts.app')

@section('title', 'Magazine Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">Dashboard</a></li>
                    <li><svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                    <li><a href="{{ route('admin.magazines.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Magazines</a></li>
                    <li><svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                    <li class="text-sm text-gray-900">Magazine Details</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Magazine Details: {{ $magazine->title }}</h1>
        </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Magazine Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $magazine->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($magazine->description, 100) }}</p>
                        
                        <div class="mb-4">
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                @if($magazine->status === 'active') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($magazine->status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-6">
                        <h6 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">File Information</h6>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">File Name</p>
                                <p class="text-sm text-gray-900 break-all">{{ $magazine->file_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">File Size</p>
                                <p class="text-sm text-gray-900">{{ formatBytes($magazine->file_size) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">File Type</p>
                                <p class="text-sm text-gray-900">{{ $magazine->mime_type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Upload Date</p>
                                <p class="text-sm text-gray-900">{{ $magazine->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        @if($magazine->published_at)
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="col-12">
                                <p class="mb-1 text-muted">Published Date</p>
                                <p class="mb-3">{{ $magazine->published_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="col-12">
                                <p class="mb-1 text-muted">Uploaded By</p>
                                <p class="mb-3">{{ $magazine->admin->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('admin.magazines.edit', $magazine) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Magazine
                        </a>
                        
                        <button type="button" class="w-full px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center
                                @if($magazine->status === 'active') bg-yellow-600 hover:bg-yellow-700 text-white
                                @else bg-green-600 hover:bg-green-700 text-white
                                @endif" 
                                onclick="toggleStatus({{ $magazine->id }})">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($magazine->status === 'active')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @endif
                            </svg>
                            {{ $magazine->status === 'active' ? 'Deactivate' : 'Activate' }}
                        </button>

                        <a href="{{ route('admin.magazines.download', $magazine) }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download File
                        </a>

                        <button type="button" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center" 
                                onclick="deleteMagazine({{ $magazine->id }})">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Magazine
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Magazine Statistics & Access -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button class="border-b-2 border-blue-500 text-blue-600 whitespace-nowrap py-2 px-1 text-sm font-medium" id="access-tab" onclick="switchTab('access')">
                            Access Statistics ({{ $magazine->entitlements->count() }})
                        </button>
                        <button class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 text-sm font-medium" id="users-tab" onclick="switchTab('users')">
                            Users with Access
                        </button>
                        <button class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 text-sm font-medium" id="activity-tab" onclick="switchTab('activity')">
                            Recent Activity
                        </button>
                    </nav>
                </div>

                    <div class="tab-content" id="magazineTabsContent">
                        <!-- Access Statistics Tab -->
                        <div class="tab-pane active" id="access" role="tabpanel">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <h3 class="text-2xl font-bold text-blue-600 mb-1">{{ $magazine->entitlements->count() }}</h3>
                                    <p class="text-sm text-gray-600">Total Access</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <h3 class="text-2xl font-bold text-green-600 mb-1">{{ $magazine->entitlements->where('expires_at', '>', now())->count() }}</h3>
                                    <p class="text-sm text-gray-600">Active Access</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <h3 class="text-2xl font-bold text-yellow-600 mb-1">{{ $magazine->entitlements->where('expires_at', '<=', now())->count() }}</h3>
                                    <p class="text-sm text-gray-600">Expired Access</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <h3 class="text-2xl font-bold text-blue-500 mb-1">{{ $magazine->entitlements->where('expires_at', null)->count() }}</h3>
                                    <p class="text-sm text-gray-600">Lifetime Access</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h6 class="text-lg font-medium text-gray-900 mb-4">Access Timeline</h6>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Access</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired Access</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Change</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
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
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month->format('M Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $newAccess }}</span></td>
                                                <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $expiredAccess }}</span></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                        @if(($newAccess - $expiredAccess) >= 0) bg-blue-100 text-blue-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
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
                        <div class="tab-pane hidden" id="users" role="tabpanel">
                            <div class="overflow-x-auto mt-3">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Access Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($magazine->entitlements as $entitlement)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('admin.users.show', $entitlement->user) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                                    {{ $entitlement->user->name }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entitlement->user->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entitlement->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($entitlement->expires_at)
                                                    {{ $entitlement->expires_at->format('M d, Y') }}
                                                @else
                                                    <span class="text-gray-400">Never</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($entitlement->expires_at && $entitlement->expires_at <= now())
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button type="button" class="text-red-600 hover:text-red-900 transition-colors duration-200" 
                                                        onclick="revokeAccess({{ $entitlement->id }})">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Revoke
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
                                            <small class="text-gray-500">{{ $activity['date']->diffForHumans() }}</small>
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
