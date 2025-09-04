@extends('layouts.app')

@section('title', $magazine->title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('magazines.index') }}" class="text-gray-400 hover:text-gray-500">
                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('magazines.index') }}" class="text-gray-500 hover:text-gray-700">Magazines</a>
                        </li>
                        <li>
                            <span class="text-gray-400">/</span>
                        </li>
                        <li>
                            <span class="text-gray-900 font-medium">{{ $magazine->title }}</span>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Cover Image and Actions -->
            <div class="lg:col-span-1">
                <!-- Cover Image -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="aspect-[3/4] bg-gray-100 overflow-hidden">
                        <img src="{{ $magazine->getCoverImageUrlOrDefault() }}" 
                             alt="{{ $magazine->title }}"
                             class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="p-6 space-y-3">
                        <a href="{{ route('magazines.download', $magazine) }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download Magazine
                        </a>
                        
                        <a href="{{ route('magazines.index') }}" 
                           class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-center px-4 py-3 rounded-lg font-medium transition-colors">
                            Back to Magazines
                        </a>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Info</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">File Size</dt>
                            <dd class="text-sm text-gray-900">{{ $magazine->getFormattedFileSize() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Language</dt>
                            <dd class="text-sm text-gray-900">{{ strtoupper($magazine->language_code ?? 'EN') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Published</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $magazine->published_at ? $magazine->published_at->format('M d, Y') : 'Not published' }}
                            </dd>
                        </div>
                        @if($magazine->category)
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="text-sm text-gray-900">{{ ucfirst($magazine->category) }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Right Column - Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Header -->
                    <div class="mb-6">
                        @if($magazine->category)
                            <div class="mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($magazine->category) }}
                                </span>
                            </div>
                        @endif
                        
                        <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $magazine->title }}</h1>
                        
                        @if($magazine->description)
                            <p class="text-lg text-gray-600 leading-relaxed">{{ $magazine->description }}</p>
                        @endif
                    </div>

                    <!-- File Information -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">File Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">File Name</p>
                                        <p class="text-sm text-gray-500">{{ $magazine->file_name }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">File Type</p>
                                        <p class="text-sm text-gray-500">{{ strtoupper($magazine->mime_type) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Download Instructions -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">How to Download</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Click the "Download Magazine" button above to download this file to your device. 
                                        The file will be saved with its original filename.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
