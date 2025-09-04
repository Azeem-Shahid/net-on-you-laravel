@extends('admin.layouts.app')

@section('title', 'Edit Email Template')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Edit Email Template</h1>
                <a href="{{ route('admin.email-templates.show', $emailTemplate) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                    Back to Template
                </a>
            </div>

            <form method="POST" action="{{ route('admin.email-templates.update', $emailTemplate) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Template Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror" 
                               id="name" name="name" value="{{ old('name', $emailTemplate->name) }}" required>
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
                            @foreach($languages as $lang)
                                <option value="{{ $lang }}" {{ old('language', $emailTemplate->language) == $lang ? 'selected' : '' }}>
                                    {{ strtoupper($lang) }}
                                </option>
                            @endforeach
                        </select>
                        @error('language')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('subject') border-red-300 @enderror" 
                           id="subject" name="subject" value="{{ old('subject', $emailTemplate->subject) }}" required>
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Body <span class="text-red-500">*</span>
                    </label>
                    <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('body') border-red-300 @enderror" 
                              id="body" name="body" rows="12" required>{{ old('body', $emailTemplate->body) }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Template Variables</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($commonVariables as $variable)
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                       id="var_{{ $variable }}" 
                                       name="variables[]" 
                                       value="{{ $variable }}"
                                       {{ in_array($variable, old('variables', $emailTemplate->variables ?? [])) ? 'checked' : '' }}>
                                <label for="var_{{ $variable }}" class="ml-2 text-sm text-gray-700">
                                    {{ $variable }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.email-templates.show', $emailTemplate) }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                        Update Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
