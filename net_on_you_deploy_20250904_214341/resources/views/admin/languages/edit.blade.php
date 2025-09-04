@extends('admin.layouts.app')

@section('title', 'Edit Language')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.languages.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Languages
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Language</h1>
            <p class="text-gray-600 mt-1">Update language information</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.languages.update', $language) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Language Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="code" 
                               id="code" 
                               value="{{ old('code', $language->code) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]"
                               placeholder="e.g., en, ur, fr"
                               maxlength="10"
                               required>
                        <p class="text-sm text-gray-500 mt-1">Short language code (2-10 characters)</p>
                        @error('code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Language Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $language->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]"
                               placeholder="e.g., English, Urdu, French"
                               maxlength="50"
                               required>
                        <p class="text-sm text-gray-500 mt-1">Full language name</p>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]"
                                required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status', $language->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $language->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Language availability status</p>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($language->is_default)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Default Language</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>This is currently the default language. You cannot deactivate it. To change the default, set another language as default first.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Language Information</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p><strong>Created:</strong> {{ $language->created_at->format('M d, Y H:i') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $language->updated_at->format('M d, Y H:i') }}</p>
                                    <p><strong>Translations:</strong> {{ $language->translations()->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-8">
                    <button type="submit" 
                            class="flex-1 bg-[#1d003f] text-white px-4 py-2 rounded-md hover:bg-[#2a0057] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1d003f] transition-colors">
                        Update Language
                    </button>
                    <a href="{{ route('admin.languages.index') }}" 
                       class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
