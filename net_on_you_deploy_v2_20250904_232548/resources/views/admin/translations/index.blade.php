@extends('admin.layouts.app')

@section('title', 'Translations Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">Translations Management</h1>
            <p class="text-gray-600 mt-1">Manage translation keys and values for all languages</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('admin.translations.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#1d003f] text-white rounded-lg hover:bg-[#2a0057] transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Translation
            </a>
            <button onclick="openBulkImportModal()" 
                    class="inline-flex items-center px-4 py-2 bg-[#00ff00] text-black rounded-lg hover:bg-[#00cc00] transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Bulk Import
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                <select name="language" id="language" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]">
                    <option value="">All Languages</option>
                    @foreach($languages as $language)
                        <option value="{{ $language->code }}" {{ request('language') == $language->code ? 'selected' : '' }}>
                            {{ $language->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="module" class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                <select name="module" id="module" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                            {{ ucfirst($module) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Search keys or values..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-[#1d003f] text-white px-4 py-2 rounded-md hover:bg-[#2a0057] transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Mobile Cards View -->
    <div class="lg:hidden space-y-4">
        @forelse($translations as $translation)
            <div class="bg-white rounded-lg shadow p-4 border">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="text-lg font-semibold text-gray-900">{{ $translation->key }}</span>
                        <span class="text-sm text-gray-500">({{ $translation->language_code }})</span>
                    </div>
                    @if($translation->module)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($translation->module) }}
                        </span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <p class="text-sm text-gray-600">{{ Str::limit($translation->value, 100) }}</p>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.translations.edit', $translation) }}" 
                       class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    
                    <form action="{{ route('admin.translations.destroy', $translation) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to delete this translation?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No translations found</h3>
                <p class="text-gray-500 mb-4">Get started by adding your first translation.</p>
                <a href="{{ route('admin.translations.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-[#1d003f] text-white rounded-lg hover:bg-[#2a0057] transition-colors">
                    Add Translation
                </a>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($translations as $translation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $translation->key }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $translation->value }}">
                                {{ $translation->value }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $translation->language_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($translation->module)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($translation->module) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $translation->updated_at->format('M d, Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.translations.edit', $translation) }}" 
                                   class="text-blue-600 hover:text-blue-900">Edit</a>
                                
                                <form action="{{ route('admin.translations.destroy', $translation) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this translation?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No translations found</h3>
                                <p class="text-gray-500 mb-4">Get started by adding your first translation.</p>
                                <a href="{{ route('admin.translations.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-[#1d003f] text-white rounded-lg hover:bg-[#2a0057] transition-colors">
                                    Add Translation
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($translations->hasPages())
        <div class="mt-6">
            {{ $translations->links() }}
        </div>
    @endif
</div>

<!-- Bulk Import Modal -->
<div id="bulkImportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Import Translations</h3>
            
            <form action="{{ route('admin.translations.bulk-import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label for="import_language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                    <select name="language_code" id="import_language" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]">
                        <option value="">Select Language</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->code }}">{{ $language->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                    <input type="file" name="import_file" id="import_file" accept=".csv" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#1d003f] focus:border-[#1d003f]">
                    <p class="text-xs text-gray-500 mt-1">File should have columns: Key, Value, Module (optional)</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBulkImportModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-[#1d003f] text-white rounded-md hover:bg-[#2a0057] transition-colors">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openBulkImportModal() {
    document.getElementById('bulkImportModal').classList.remove('hidden');
}

function closeBulkImportModal() {
    document.getElementById('bulkImportModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('bulkImportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBulkImportModal();
    }
});
</script>
@endsection
