@extends('admin.layouts.app')

@section('title', 'Edit Magazine')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Magazine</h1>
                    <p class="text-sm text-gray-600">Update magazine information and settings</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.magazines.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Magazines
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-8">
                <form id="magazineForm" method="POST" action="{{ route('admin.magazines.update', $magazine) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                   id="title" name="title" 
                                   value="{{ old('title', $magazine->title) }}" required>
                            <div class="hidden text-sm text-red-600 mt-1" id="title-error"></div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                    id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', $magazine->status) === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ old('status', $magazine->status) === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                                <option value="archived" {{ old('status', $magazine->status) === 'archived' ? 'selected' : '' }}>
                                    Archived
                                </option>
                            </select>
                            <div class="hidden text-sm text-red-600 mt-1" id="status-error"></div>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                    id="category" name="category">
                                <option value="">Select Category</option>
                                <option value="technology" {{ old('category', $magazine->category) === 'technology' ? 'selected' : '' }}>Technology</option>
                                <option value="business" {{ old('category', $magazine->category) === 'business' ? 'selected' : '' }}>Business</option>
                                <option value="lifestyle" {{ old('category', $magazine->category) === 'lifestyle' ? 'selected' : '' }}>Lifestyle</option>
                                <option value="health" {{ old('category', $magazine->category) === 'health' ? 'selected' : '' }}>Health</option>
                                <option value="education" {{ old('category', $magazine->category) === 'education' ? 'selected' : '' }}>Education</option>
                            </select>
                        </div>

                        <div>
                            <label for="language_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Language <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                    id="language_code" name="language_code" required>
                                <option value="">Select Language</option>
                                <option value="en" {{ old('language_code', $magazine->language_code) === 'en' ? 'selected' : '' }}>EN</option>
                                <option value="de" {{ old('language_code', $magazine->language_code) === 'de' ? 'selected' : '' }}>DE</option>
                            </select>
                        </div>

                        <div>
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Published Date</label>
                            <input type="date" 
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                   id="published_at" name="published_at" 
                                   value="{{ old('published_at', $magazine->published_at ? $magazine->published_at->format('Y-m-d') : '') }}">
                            <div class="hidden text-sm text-red-600 mt-1" id="published_at-error"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current File</label>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-md">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $magazine->file_name }}</p>
                                    <p class="text-sm text-gray-500">{{ formatBytes($magazine->file_size) }}</p>
                                    <p class="text-xs text-gray-400">{{ $magazine->mime_type }}</p>
                                </div>
                                <a href="{{ route('admin.magazines.download', $magazine) }}" 
                                   class="flex-shrink-0 bg-blue-100 hover:bg-blue-200 text-blue-600 px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                  id="description" name="description" rows="4">{{ old('description', $magazine->description) }}</textarea>
                        <div class="hidden text-sm text-red-600 mt-1" id="description-error"></div>
                    </div>

                    <hr class="my-8 border-gray-200">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Information</label>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-md">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Uploaded By</p>
                                    <p class="text-sm text-gray-500">{{ $magazine->admin->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400">{{ $magazine->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Access Statistics</label>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-md">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Total Views</p>
                                    <p class="text-sm text-gray-500">{{ $magazine->views_count ?? 0 }} views</p>
                                    <p class="text-xs text-gray-400">Last viewed: {{ $magazine->last_viewed_at ? $magazine->last_viewed_at->diffForHumans() : 'Never' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.magazines.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Magazine
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Handle form submission
document.getElementById('magazineForm').addEventListener('submit', function(e) {
    // Reset previous errors
    document.querySelectorAll('[id$="-error"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    
    // Form validation can be added here
});
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
