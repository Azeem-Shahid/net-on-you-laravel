@extends('admin.layouts.app')

@section('title', 'Create Email Template')

@php
function getLanguageDisplayName($code) {
    $names = [
        'en' => 'English',
        'ur' => 'Urdu',
        'fr' => 'French',
        'es' => 'Spanish',
        'ar' => 'Arabic',
        'hi' => 'Hindi',
        'bn' => 'Bengali',
        'pt' => 'Portuguese',
        'ru' => 'Russian',
        'zh' => 'Chinese'
    ];
    return $names[$code] ?? strtoupper($code);
}
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Email Template</h1>
                    <p class="text-sm text-gray-600">Create a new email template for your users</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.email-templates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Templates
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-8">
                <form method="POST" action="{{ route('admin.email-templates.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Template Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="e.g., welcome_email, password_reset" required>
                            <p class="mt-1 text-sm text-gray-500">Unique identifier for this template</p>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                Language <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('language') border-red-300 @enderror" 
                                    id="language" name="language" required>
                                <option value="">Select Language</option>
                                @foreach($languages as $lang)
                                    <option value="{{ $lang }}" {{ old('language') == $lang ? 'selected' : '' }}>
                                        {{ strtoupper($lang) }} - {{ getLanguageDisplayName($lang) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('language')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Subject <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('subject') border-red-300 @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" 
                               placeholder="Welcome to Net On You!" required>
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Body <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('body') border-red-300 @enderror" 
                                  id="body" name="body" rows="12" 
                                  placeholder="Write your email content here..." required>{{ old('body') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            You can use HTML tags and variables like {name}, {email}, etc.
                        </p>
                        @error('body')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Template Variables</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($commonVariables as $variable)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                           id="var_{{ $variable }}" 
                                           name="variables[]" 
                                           value="{{ $variable }}"
                                           {{ in_array($variable, old('variables', [])) ? 'checked' : '' }}>
                                    <label for="var_{{ $variable }}" class="ml-2 text-sm text-gray-700">
                                        {{ $variable }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Select the variables that can be used in this template. Users can customize these values when sending emails.
                        </p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.email-templates.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md text-sm font-medium">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Template
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
// Auto-save draft functionality
let autoSaveTimer;
const form = document.querySelector('form');

form.addEventListener('input', function() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(function() {
        saveDraft();
    }, 2000); // Save draft after 2 seconds of inactivity
});

function saveDraft() {
    const formData = new FormData(form);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("admin.email-templates.store") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Draft saved successfully');
        }
    })
    .catch(error => {
        console.error('Error saving draft:', error);
    });
}

// Variable insertion helper
function insertVariable(variable) {
    const bodyField = document.getElementById('body');
    const cursorPos = bodyField.selectionStart;
    const textBefore = bodyField.value.substring(0, cursorPos);
    const textAfter = bodyField.value.substring(cursorPos);
    
    bodyField.value = textBefore + '{' + variable + '}' + textAfter;
    bodyField.focus();
    bodyField.setSelectionRange(cursorPos + variable.length + 2, cursorPos + variable.length + 2);
}
</script>
@endpush
