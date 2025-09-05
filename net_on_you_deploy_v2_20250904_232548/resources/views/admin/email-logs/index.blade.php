@extends('admin.layouts.app')

@section('title', 'Email Logs')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">Email Logs</h1>
            <div class="flex gap-2">
                <button type="button" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition-colors" onclick="showClearLogsModal()">
                    <i class="fas fa-trash"></i> Clear Old Logs
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-primary text-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-2xl font-bold mb-1">{{ $stats['total'] }}</h4>
                            <p class="text-sm opacity-75">Total Emails</p>
                        </div>
                        <i class="fas fa-envelope text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="bg-green-600 text-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-2xl font-bold mb-1">{{ $stats['sent'] }}</h4>
                            <p class="text-sm opacity-75">Sent</p>
                        </div>
                        <i class="fas fa-check-circle text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="bg-red-600 text-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-2xl font-bold mb-1">{{ $stats['failed'] }}</h4>
                            <p class="text-sm opacity-75">Failed</p>
                        </div>
                        <i class="fas fa-times-circle text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-600 text-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-2xl font-bold mb-1">{{ $stats['queued'] }}</h4>
                            <p class="text-sm opacity-75">Queued</p>
                        </div>
                        <i class="fas fa-clock text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.email-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="status" name="status">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="template" class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                        <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="template" name="template">
                            <option value="">All Templates</option>
                            @foreach($templates as $template)
                                <option value="{{ $template }}" {{ request('template') == $template ? 'selected' : '' }}>
                                    {{ $template }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="user_id" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="start_date" name="start_date" 
                               value="{{ request('start_date') }}">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="end_date" name="end_date" 
                               value="{{ request('end_date') }}">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
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
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $log->template_name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->user)
                                            <a href="{{ route('admin.users.show', $log->user) }}" class="text-action hover:text-action/80">
                                                {{ $log->user->name }}
                                            </a>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ Str::limit($log->subject, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: {{ $log->status_color }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($log->sent_at)
                                            {{ $log->sent_at->format('M d, H:i') }}
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->created_at->format('M d, H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.email-logs.show', $log) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-transparent border border-action text-action text-sm font-medium rounded hover:bg-action hover:text-primary transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($log->status === 'failed')
                                                <form method="POST" action="{{ route('admin.email-logs.retry', $log) }}" class="inline">
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
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <i class="fas fa-inbox text-4xl text-gray-400 mb-3"></i>
                                        <h5 class="text-lg font-medium text-gray-900 mb-2">No Email Logs Found</h5>
                                        <p class="text-gray-500">No emails have been sent yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="flex justify-center mt-6">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Clear Old Logs Modal -->
<div id="clearLogsModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST" action="{{ route('admin.email-logs.clear-old') }}">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Clear Old Email Logs
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    This will permanently delete email logs older than the specified number of days.
                                </p>
                                <div class="mt-4">
                                    <label for="days" class="block text-sm font-medium text-gray-700">Delete logs older than (days)</label>
                                    <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm" id="days" name="days" 
                                           min="30" max="365" value="90" required>
                                    <p class="mt-1 text-xs text-gray-500">Minimum 30 days, maximum 365 days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm" 
                            onclick="return confirm('Are you sure? This action cannot be undone.')">
                        Clear Old Logs
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-action sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('clearLogsModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showClearLogsModal() {
    document.getElementById('clearLogsModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('clearLogsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('clearLogsModal');
    }
});
</script>
@endsection
