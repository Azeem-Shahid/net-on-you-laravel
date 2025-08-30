@extends('admin.layouts.app')

@section('title', 'Email Logs')

@section('content')
<div class="container-fluid">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Email Logs</h1>
                <div class="d-flex gap-2">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition-colors"  onclick="showClearLogsModal()">
                        <i class="fas fa-trash"></i> Clear Old Logs
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="p-6">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                    <small>Total Emails</small>
                                </div>
                                <i class="fas fa-envelope fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="p-6">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['sent'] }}</h4>
                                    <small>Sent</small>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="p-6">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['failed'] }}</h4>
                                    <small>Failed</small>
                                </div>
                                <i class="fas fa-times-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-secondary text-white">
                        <div class="p-6">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['queued'] }}</h4>
                                    <small>Queued</small>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.email-logs.index') }}" class="row g-3">
                        <div class="col-md-2 col-sm-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="status" name="status">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="template" class="form-label">Template</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="template" name="template">
                                <option value="">All Templates</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template }}" {{ request('template') == $template ? 'selected' : '' }}>
                                        {{ $template }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="user_id" class="form-label">User</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="user_id" name="user_id">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="start_date" name="start_date" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="end_date" name="end_date" 
                                   value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2 col-sm-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-secondary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.email-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-transparent border border-gray-500 text-gray-600 font-medium rounded-lg hover:bg-gray-500 hover:text-white transition-colors">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Email Logs Table -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Template</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Sent At</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $log->template_name }}</span>
                                        </td>
                                        <td>
                                            @if($log->user)
                                                <a href="{{ route('admin.users.show', $log->user) }}">
                                                    {{ $log->user->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->email }}</td>
                                        <td>{{ Str::limit($log->subject, 50) }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $log->status_color }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->sent_at)
                                                {{ $log->sent_at->format('M d, H:i') }}
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                        <td>
                                            <div class="flex space-x-2" class="flex space-x-2">
                                                <a href="{{ route('admin.email-logs.show', $log) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-transparent border border-action text-action text-sm font-medium rounded hover:bg-action hover:text-primary transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($log->status === 'failed')
                                                    <form method="POST" action="{{ route('admin.email-logs.retry', $log) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-transparent border border-yellow-500 text-yellow-600 text-sm font-medium rounded hover:bg-yellow-500 hover:text-white transition-colors" 
                                                                onclick="return confirm('Retry this failed email?')">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                            <h5>No Email Logs Found</h5>
                                            <p class="text-gray-500">No emails have been sent yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clear Old Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear Old Email Logs</h5>
                <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-gray-700 hover:text-gray-900" onclick="this.parentElement.remove()"></button>
            </div>
            <form method="POST" action="{{ route('admin.email-logs.clear-old') }}">
                @csrf
                <div class="modal-body">
                    <p>This will permanently delete email logs older than the specified number of days.</p>
                    <div class="mb-3">
                        <label for="days" class="form-label">Delete logs older than (days)</label>
                        <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="days" name="days" 
                               min="30" max="365" value="90" required>
                        <div class="form-text">Minimum 30 days, maximum 365 days</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors" onclick="this.parentElement.remove()">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition-colors" 
                            onclick="return confirm('Are you sure? This action cannot be undone.')">
                        Clear Old Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
