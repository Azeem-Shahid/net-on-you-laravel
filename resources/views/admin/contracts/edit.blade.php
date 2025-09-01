@extends('admin.layouts.app')

@section('title', 'Edit Contract')

@section('content')
<div class="container mx-auto px-4">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Edit Contract</h3>
                <a href="{{ route('admin.contracts.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Contracts
                </a>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.contracts.update', $contract) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Contract Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $contract->title) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="version" class="block text-sm font-medium text-gray-700 mb-2">Version</label>
                        <input type="text" id="version" name="version" value="{{ old('version', $contract->version) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('version') border-red-500 @enderror">
                        @error('version')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                        <select id="language" name="language" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('language') border-red-500 @enderror">
                            <option value="">Select Language</option>
                            <option value="en" {{ old('language', $contract->language) == 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ old('language', $contract->language) == 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="fr" {{ old('language', $contract->language) == 'fr' ? 'selected' : '' }}>French</option>
                            <option value="de" {{ old('language', $contract->language) == 'de' ? 'selected' : '' }}>German</option>
                            <option value="it" {{ old('language', $contract->language) == 'it' ? 'selected' : '' }}>Italian</option>
                            <option value="pt" {{ old('language', $contract->language) == 'pt' ? 'selected' : '' }}>Portuguese</option>
                            <option value="ru" {{ old('language', $contract->language) == 'ru' ? 'selected' : '' }}>Russian</option>
                            <option value="ar" {{ old('language', $contract->language) == 'ar' ? 'selected' : '' }}>Arabic</option>
                            <option value="zh" {{ old('language', $contract->language) == 'zh' ? 'selected' : '' }}>Chinese</option>
                            <option value="ja" {{ old('language', $contract->language) == 'ja' ? 'selected' : '' }}>Japanese</option>
                            <option value="ko" {{ old('language', $contract->language) == 'ko' ? 'selected' : '' }}>Korean</option>
                            <option value="hi" {{ old('language', $contract->language) == 'hi' ? 'selected' : '' }}>Hindi</option>
                            <option value="ur" {{ old('language', $contract->language) == 'ur' ? 'selected' : '' }}>Urdu</option>
                        </select>
                        @error('language')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">Effective Date</label>
                        <input type="date" id="effective_date" name="effective_date" value="{{ old('effective_date', $contract->effective_date->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('effective_date') border-red-500 @enderror">
                        @error('effective_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Contract Content</label>
                    <textarea id="content" name="content" rows="10" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-500 @enderror">{{ old('content', $contract->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                

                
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $contract->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Activate this contract immediately
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.contracts.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium inline-flex items-center">
                        <i class="fas fa-save mr-2"></i> Update Contract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
