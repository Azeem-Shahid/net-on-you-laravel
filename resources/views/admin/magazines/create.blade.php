@extends('admin.layouts.app')

@section('title', t('create_magazine', [], 'admin'))

@push('styles')
<style>
    /* Custom styling for file input */
    input[type="file"]::-webkit-file-upload-button {
        background-color: #3b82f6;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        margin-right: 0.5rem;
        transition: background-color 0.2s ease-in-out;
    }
    
    input[type="file"]::-webkit-file-upload-button:hover {
        background-color: #2563eb;
    }
    
    /* Enhanced focus states */
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Hover effects */
    input:hover, select:hover, textarea:hover {
        border-color: #9ca3af;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ t('create_magazine', [], 'admin') }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ t('create_magazine_description', [], 'admin') }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.magazines.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ t('back_to_magazines', [], 'admin') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-8">
                <form id="magazineForm" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('title', [], 'common') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                   id="title" name="title" required 
                                   placeholder="Enter magazine title">
                            <div class="hidden text-sm text-red-600 mt-1" id="title-error"></div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('status', [], 'common') }} <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                    id="status" name="status" required>
                                <option value="">{{ t('select_status', [], 'admin') }}</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="draft">Draft</option>
                            </select>
                            <div class="hidden text-sm text-red-600 mt-1" id="status-error"></div>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                    id="category" name="category">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                                @endforeach
                                <option value="new">+ Add New Category</option>
                            </select>
                            <div class="hidden text-sm text-red-600 mt-1" id="category-error"></div>
                        </div>

                        <div>
                            <label for="language_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Language <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                    id="language_code" name="language_code" required>
                                <option value="">Select Language</option>
                                {{-- @foreach($languages as $language)
                                    <option value="{{ $language }}">{{ strtoupper($language) }}</option>
                                @endforeach --}}
                                <option value="en">EN</option>
                                <option value="de">DE</option>
                                <option value="new">+ Add New Language</option>
                            </select>
                            <div class="hidden text-sm text-red-600 mt-1" id="language_code-error"></div>
                        </div>

                        <div>
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Published Date</label>
                            <input type="date" 
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                   id="published_at" name="published_at">
                            <div class="hidden text-sm text-red-600 mt-1" id="published_at-error"></div>
                        </div>

                        <div>
                            <label for="magazine_file" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('magazine_file', [], 'admin') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-gray-50" 
                                   id="magazine_file" name="magazine_file" accept=".pdf" required>
                            <div class="hidden text-sm text-red-600 mt-1" id="magazine_file-error"></div>
                            <p class="text-sm text-gray-600 mt-1">{{ t('pdf_file_requirements', [], 'admin') }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">{{ t('description', [], 'common') }}</label>
                        <textarea class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Enter magazine description (optional)"></textarea>
                        <div class="hidden text-sm text-red-600 mt-1" id="description-error"></div>
                    </div>

                    <!-- New Category Input (Hidden by default) -->
                    <div id="newCategoryInput" class="mt-6 hidden">
                        <label for="new_category" class="block text-sm font-medium text-gray-700 mb-2">New Category Name</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                               id="new_category" name="new_category" 
                               placeholder="Enter new category name">
                        <div class="hidden text-sm text-red-600 mt-1" id="new_category-error"></div>
                    </div>

                    <!-- New Language Input (Hidden by default) -->
                    <div id="newLanguageInput" class="mt-6 hidden">
                        <label for="new_language" class="block text-sm font-medium text-gray-700 mb-2">New Language Code</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                               id="new_language" name="new_language" 
                               placeholder="e.g., en, es, fr, de">
                        <div class="hidden text-sm text-red-600 mt-1" id="new_language-error"></div>
                    </div>

                    <!-- Upload Progress -->
                    <div id="uploadProgress" class="mt-6 hidden">
                        <div class="bg-gray-100 rounded-full h-2">
                            <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="progressText" class="text-sm text-gray-600 mt-2">Preparing upload...</p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.magazines.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md text-sm font-medium">
                                Cancel
                            </a>
                            <button type="submit" 
                                    id="submitBtn"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                {{ t('upload_magazine', [], 'admin') }}
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
// Show/hide new category input
document.getElementById('category').addEventListener('change', function() {
    const newCategoryInput = document.getElementById('newCategoryInput');
    if (this.value === 'new') {
        newCategoryInput.classList.remove('hidden');
        document.getElementById('new_category').required = true;
    } else {
        newCategoryInput.classList.add('hidden');
        document.getElementById('new_category').required = false;
    }
});

// Show/hide new language input
document.getElementById('language_code').addEventListener('change', function() {
    const newLanguageInput = document.getElementById('newLanguageInput');
    if (this.value === 'new') {
        newLanguageInput.classList.remove('hidden');
        document.getElementById('new_language').required = true;
    } else {
        newLanguageInput.classList.add('hidden');
        document.getElementById('new_language').required = false;
    }
});

// Handle form submission
document.getElementById('magazineForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Reset previous errors
    document.querySelectorAll('[id$="-error"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    
    // Show upload progress
    document.getElementById('uploadProgress').classList.remove('hidden');
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Uploading...';
    
    const formData = new FormData(this);
    
    // Create XMLHttpRequest for progress tracking
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            document.getElementById('progressBar').style.width = percentComplete + '%';
            document.getElementById('progressText').textContent = `Uploading... ${Math.round(percentComplete)}%`;
        }
    });
    
    xhr.addEventListener('load', function() {
        try {
            const response = JSON.parse(xhr.responseText);
            
            if (xhr.status === 200 && response.success) {
                // Success
                alert('Magazine uploaded successfully!');
                window.location.href = response.redirect || '{{ route("admin.magazines.index") }}';
            } else {
                // Show validation errors
                if (response.errors) {
                    Object.keys(response.errors).forEach(field => {
                        const errorElement = document.getElementById(field + '-error');
                        if (errorElement) {
                            errorElement.textContent = response.errors[field][0];
                            errorElement.classList.remove('hidden');
                        }
                    });
                } else {
                    alert('Error: ' + (response.message || 'An error occurred during upload'));
                }
            }
        } catch (e) {
            alert('Error: An unexpected error occurred');
        }
        
        // Reset form state
        document.getElementById('uploadProgress').classList.add('hidden');
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('submitBtn').innerHTML = '<svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>{{ t('upload_magazine', [], 'admin') }}';
    });
    
    xhr.addEventListener('error', function() {
        alert('Error: Upload failed. Please try again.');
        document.getElementById('uploadProgress').classList.add('hidden');
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('submitBtn').innerHTML = '<svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>{{ t('upload_magazine', [], 'admin') }}';
    });
    
    xhr.open('POST', '{{ route("admin.magazines.store") }}');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.send(formData);
});
</script>
@endpush
