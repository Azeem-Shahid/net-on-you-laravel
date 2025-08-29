@extends('admin.layouts.app')

@section('title', 'Magazine Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Magazine Management</h1>
                    <p class="text-sm text-gray-600">Upload and manage PDF magazines</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.magazines.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Upload Magazine
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.magazines.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Search by title or description">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Magazines Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($magazines as $magazine)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $magazine->title }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($magazine->status === 'active') bg-green-100 text-green-800
                                @elseif($magazine->status === 'inactive') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($magazine->status) }}
                            </span>
                        </div>
                        
                        @if($magazine->description)
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($magazine->description, 100) }}</p>
                        @endif
                        
                        <div class="space-y-2 text-sm text-gray-500 mb-4">
                            <div class="flex justify-between">
                                <span>File Size:</span>
                                <span>{{ $magazine->getFormattedFileSize() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Uploaded:</span>
                                <span>{{ $magazine->created_at->format('M d, Y') }}</span>
                            </div>
                            @if($magazine->published_at)
                                <div class="flex justify-between">
                                    <span>Published:</span>
                                    <span>{{ $magazine->published_at->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.magazines.show', $magazine) }}" class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                                View
                            </a>
                            <a href="{{ route('admin.magazines.edit', $magazine) }}" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                                Edit
                            </a>
                            <button onclick="toggleMagazineStatus({{ $magazine->id }}, '{{ $magazine->status }}')" 
                                    class="flex-1 text-center bg-{{ $magazine->status === 'active' ? 'yellow' : 'green' }}-600 hover:bg-{{ $magazine->status === 'active' ? 'yellow' : 'green' }}-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                                {{ $magazine->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            <a href="{{ route('admin.magazines.download', $magazine) }}" class="flex-1 text-center bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                                Download
                            </a>
                            <button onclick="deleteMagazine({{ $magazine->id }})" class="flex-1 text-center bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <p class="text-gray-500">No magazines found</p>
                        <a href="{{ route('admin.magazines.create') }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Upload Your First Magazine
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($magazines->hasPages())
            <div class="mt-8">
                {{ $magazines->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleMagazineStatus(magazineId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    
    if (confirm(`Are you sure you want to ${action} this magazine?`)) {
        fetch(`/admin/magazines/${magazineId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            alert('An error occurred while updating magazine status');
        });
    }
}

function deleteMagazine(magazineId) {
    if (confirm('Are you sure you want to delete this magazine? This action cannot be undone.')) {
        fetch(`/admin/magazines/${magazineId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            alert('An error occurred while deleting the magazine');
        });
    }
}
</script>
@endsection
