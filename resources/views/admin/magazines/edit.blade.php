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

                        <div>
                            <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Cover Image (Thumbnail)
                            </label>
                            
                            <!-- Current Cover Image -->
                            @if($magazine->cover_image_path)
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-2">Current Cover Image:</label>
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $magazine->getCoverImageUrl() }}" 
                                             alt="Current cover" 
                                             class="w-20 h-24 object-cover rounded-lg border border-gray-300">
                                        <div>
                                            <p class="text-sm text-gray-600">Current thumbnail</p>
                                            <p class="text-xs text-gray-400">Click "Choose File" to replace</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <input type="file" 
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-gray-50" 
                                   id="cover_image" name="cover_image" accept="image/*"
                                   title="Select a new cover image for your magazine (JPG, PNG, GIF - Max 2MB)">
                            <div class="hidden text-sm text-red-600 mt-1" id="cover_image-error"></div>
                            
                            <!-- Detailed Instructions -->
                            <div class="mt-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Cover Image Instructions</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li><strong>File Format:</strong> JPG, PNG, or GIF only</li>
                                                <li><strong>File Size:</strong> Maximum 2MB</li>
                                                <li><strong>Recommended Size:</strong> 300x400 pixels (3:4 aspect ratio)</li>
                                                <li><strong>Quality:</strong> High resolution for best display</li>
                                                <li><strong>Content:</strong> Magazine cover, title, or relevant image</li>
                                            </ul>
                                            <p class="mt-2 text-xs text-blue-600">
                                                <strong>Note:</strong> Uploading a new image will replace the current cover image.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3 hidden">
                                <div class="flex items-center space-x-4">
                                    <img id="previewImg" src="" alt="Preview" class="w-20 h-24 object-cover rounded-lg border border-gray-300">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">New Preview</p>
                                        <p class="text-xs text-gray-500">This is how your new cover will appear</p>
                                    </div>
                                </div>
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
// Handle cover image preview
document.getElementById('cover_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const errorDiv = document.getElementById('cover_image-error');
    
    // Reset error state
    if (errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
    }
    
    if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showError('cover_image', 'Please select a valid image file (JPG, PNG, or GIF)');
            return;
        }
        
        // Validate file size (2MB = 2 * 1024 * 1024 bytes)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            showError('cover_image', 'File size must be less than 2MB');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
            
            // Show file info
            const fileInfo = document.createElement('div');
            fileInfo.className = 'text-xs text-gray-500 mt-1';
            fileInfo.innerHTML = `File: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            
            // Remove existing file info if any
            const existingInfo = preview.querySelector('.file-info');
            if (existingInfo) {
                existingInfo.remove();
            }
            
            fileInfo.className += ' file-info';
            preview.appendChild(fileInfo);
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});

// Handle form submission
document.getElementById('magazineForm').addEventListener('submit', function(e) {
    // Reset previous errors
    document.querySelectorAll('[id$="-error"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    
    // Basic form validation
    const title = document.getElementById('title').value.trim();
    const status = document.getElementById('status').value;
    const languageCode = document.getElementById('language_code').value;
    
    if (!title) {
        e.preventDefault();
        showError('title', 'Title is required');
        return;
    }
    
    if (!status) {
        e.preventDefault();
        showError('status', 'Status is required');
        return;
    }
    
    if (!languageCode) {
        e.preventDefault();
        showError('language_code', 'Language is required');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Updating...';
});

function showError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + '-error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
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
