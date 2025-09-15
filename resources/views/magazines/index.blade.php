@extends('layouts.app')

@section('title', 'Magazines')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">Magazines</h1>
                <p class="mt-2 text-sm text-gray-600">Access to premium content with your subscription</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search and Filters -->
        <div class="mb-8">
            <form method="GET" action="{{ route('magazines.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Search magazines by title or description...">
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" id="category" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Language Filter -->
                    <div>
                        <label for="language_code" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                        <select name="language_code" id="language_code" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Languages</option>
                            @foreach($languages as $language)
                                <option value="{{ $language }}" {{ request('language_code') == $language ? 'selected' : '' }}>
                                    {{ strtoupper($language) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Clear Filters -->
                @if(request('search') || request('category') || request('language_code'))
                    <div class="text-center">
                        <a href="{{ route('magazines.index') }}" 
                           class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                            Clear all filters
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Results Count -->
        <div class="mb-6">
            <p class="text-sm text-gray-600">
                Showing {{ $magazines->firstItem() ?? 0 }} to {{ $magazines->lastItem() ?? 0 }} 
                of {{ $magazines->total() }} magazines
            </p>
        </div>

        <!-- Magazines Grid -->
        @if($magazines->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($magazines as $magazine)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Cover Image -->
                        <div class="aspect-[3/4] bg-gray-100 overflow-hidden relative">
                            <img src="{{ $magazine->getCoverImageUrlOrDefault() }}" 
                                 alt="{{ $magazine->title }}"
                                 class="w-full h-full object-cover object-center">
                            <!-- Loading placeholder -->
                            <div class="absolute inset-0 bg-gray-200 animate-pulse hidden" id="loading-{{ $magazine->id }}"></div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <!-- Category Badge -->
                            @if($magazine->category)
                                <div class="mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($magazine->category) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Title -->
                            <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-2">
                                {{ $magazine->title }}
                            </h3>

                            <!-- Description -->
                            @if($magazine->description)
                                <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                                    {{ $magazine->description }}
                                </p>
                            @endif

                            <!-- Meta Info -->
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                <span>{{ $magazine->getFormattedFileSize() }}</span>
                                <span>{{ $magazine->language_code ? strtoupper($magazine->language_code) : 'EN' }}</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('magazines.show', $magazine) }}" 
                                   class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-center px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                    View Details
                                </a>
                                <a href="{{ route('magazines.download', $magazine) }}" 
                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($magazines->hasPages())
                <div class="mt-8">
                    {{ $magazines->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No magazines found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request('search') || request('category') || request('language_code'))
                        Try adjusting your search criteria.
                    @else
                        No magazines are currently available.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Improved responsive magazine grid */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

/* Better image centering and loading */
.magazine-cover {
    position: relative;
    overflow: hidden;
}

.magazine-cover img {
    transition: opacity 0.3s ease;
}

.magazine-cover img.loading {
    opacity: 0.5;
}

/* Ensure images are properly centered */
.object-cover {
    object-position: center;
}
</style>

<script>
// Handle image loading states
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.magazine-cover img');
    
    images.forEach(img => {
        const loadingDiv = document.getElementById('loading-' + img.closest('[id*="loading-"]')?.id.split('-')[1]);
        
        img.addEventListener('load', function() {
            if (loadingDiv) {
                loadingDiv.style.display = 'none';
            }
            this.classList.remove('loading');
        });
        
        img.addEventListener('error', function() {
            if (loadingDiv) {
                loadingDiv.style.display = 'none';
            }
            // If image fails to load, it will show the placeholder
        });
        
        // Show loading state if image is not cached
        if (!img.complete) {
            if (loadingDiv) {
                loadingDiv.style.display = 'block';
            }
            img.classList.add('loading');
        }
    });
});
</script>
@endsection
