@extends('admin.layouts.app')

@section('title', 'Add Translation')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.translations.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Translations
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Add New Translation</h1>
            <p class="text-gray-600 mt-1">Create a new translation key and value</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.translations.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="language_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Language <span class="text-red-500">*</span>
                        </label>
                        <select name="language_code" 
                                id="language_code"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]"
                                required>
                            <option value="">Select Language</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->code }}" {{ old('language_code') == $language->code ? 'selected' : '' }}>
                                    {{ $language->name }} ({{ $language->code }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Select the language for this translation</p>
                        @error('language_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="key" class="block text-sm font-medium text-gray-700 mb-2">
                            Translation Key <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="key" 
                               id="key" 
                               value="{{ old('key') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]"
                               placeholder="e.g., welcome_message, login_button, etc."
                               maxlength="191"
                               required>
                        <p class="text-sm text-gray-500 mt-1">Unique identifier for this translation (use underscores, no spaces)</p>
                        @error('key')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                            Translation Value <span class="text-red-500">*</span>
                        </label>
                        <textarea name="value" 
                                  id="value" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]"
                                  placeholder="Enter the translated text..."
                                  required>{{ old('value') }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">The actual translated text that will be displayed</p>
                        @error('value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="module" class="block text-sm font-medium text-gray-700 mb-2">
                            Module (Optional)
                        </label>
                        <select name="module" 
                                id="module"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]">
                            <option value="">No Module</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" {{ old('module') == $module ? 'selected' : '' }}>
                                    {{ ucfirst($module) }}
                                </option>
                            @endforeach
                            <option value="custom" {{ old('module') == 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Group translations by module for better organization</p>
                        @error('module')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="customModuleField" class="hidden">
                        <label for="custom_module" class="block text-sm font-medium text-gray-700 mb-2">
                            Custom Module Name
                        </label>
                        <input type="text" 
                               name="custom_module" 
                               id="custom_module" 
                               value="{{ old('custom_module') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]"
                               placeholder="e.g., newsletter, blog, etc."
                               maxlength="50">
                        <p class="text-sm text-gray-500 mt-1">Enter a custom module name</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Translation Tips</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Use descriptive, consistent key names</li>
                                        <li>Group related translations by module</li>
                                        <li>Consider context when translating</li>
                                        <li>Test translations in the target language</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-8">
                    <button type="submit" 
                            class="flex-1 bg-[#1d003f] text-white px-4 py-2 rounded-md hover:bg-[#2a0057] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1d003f] transition-colors">
                        Create Translation
                    </button>
                    <a href="{{ route('admin.translations.index') }}" 
                       class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('module').addEventListener('change', function() {
    const customField = document.getElementById('customModuleField');
    const customInput = document.getElementById('custom_module');
    
    if (this.value === 'custom') {
        customField.classList.remove('hidden');
        customInput.setAttribute('required', 'required');
    } else {
        customField.classList.add('hidden');
        customInput.removeAttribute('required');
        customInput.value = '';
    }
});

// Handle form submission for custom module
document.querySelector('form').addEventListener('submit', function(e) {
    const moduleSelect = document.getElementById('module');
    const customModule = document.getElementById('custom_module');
    
    if (moduleSelect.value === 'custom' && customModule.value.trim()) {
        // Set the custom module value to the module field
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'module';
        hiddenInput.value = customModule.value.trim();
        this.appendChild(hiddenInput);
    }
});
</script>
@endsection
