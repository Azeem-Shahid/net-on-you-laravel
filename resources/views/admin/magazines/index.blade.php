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

        <!-- Cover Image Guidelines -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-blue-900">Cover Image Guidelines</h3>
                    <div class="mt-2 text-sm text-blue-800">
                        <p class="mb-3">For the best magazine display, follow these guidelines when uploading cover images:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-blue-900 mb-2">✅ Recommended:</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    <li>High-quality magazine covers</li>
                                    <li>Clear, readable titles</li>
                                    <li>Good contrast and lighting</li>
                                    <li>3:4 aspect ratio (300x400px)</li>
                                    <li>File size under 2MB</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-red-700 mb-2">❌ Avoid:</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    <li>Blurry or low-resolution images</li>
                                    <li>Text that's too small to read</li>
                                    <li>Dark or poorly lit images</li>
                                    <li>Very wide or very tall images</li>
                                    <li>Images with watermarks</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Magazines Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($magazines as $magazine)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Cover Image -->
                    <div class="aspect-[3/4] bg-gray-100 overflow-hidden relative magazine-cover">
                        <img src="{{ $magazine->getCoverImageUrlOrDefault() }}" 
                             alt="{{ $magazine->title }}"
                             class="w-full h-full object-cover object-center">
                        <!-- Loading placeholder -->
                        <div class="absolute inset-0 bg-gray-200 animate-pulse hidden" id="loading-{{ $magazine->id }}"></div>
                    </div>
                    
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
                                <span>Language:</span>
                                <div class="flex items-center">
                                    <span class="font-medium">{{ strtoupper($magazine->language_code) }}</span>
                                    @if(in_array($magazine->language_code, ['en', 'es', 'pt', 'it']))
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-star mr-1"></i>
                                            Core
                                        </span>
                                    @endif
                                </div>
                            </div>
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
