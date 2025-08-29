@extends('admin.layouts.app')

@section('title', 'Languages Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">Languages Management</h1>
            <p class="text-gray-600 mt-1">Manage system languages and their status</p>
        </div>
        <a href="{{ route('admin.languages.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-[#1d003f] text-white rounded-lg hover:bg-[#2a0057] transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Language
        </a>
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

    <!-- Mobile Cards View -->
    <div class="lg:hidden space-y-4">
        @forelse($languages as $language)
            <div class="bg-white rounded-lg shadow p-4 border">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="text-lg font-semibold text-gray-900">{{ $language->name }}</span>
                        <span class="text-sm text-gray-500">({{ $language->code }})</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($language->is_default)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#00ff00] text-black">
                                Default
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $language->status === 'active' ? 'bg-[#00ff00] text-black' : 'bg-[#ff0000] text-white' }}">
                            {{ ucfirst($language->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.languages.edit', $language) }}" 
                       class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    
                    @if(!$language->is_default)
                        <form action="{{ route('admin.languages.set-default', $language) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-1 bg-[#00ff00] text-black text-sm rounded hover:bg-[#00cc00] transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Set Default
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.languages.toggle-status', $language) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-1 {{ $language->status === 'active' ? 'bg-[#ff0000] text-white' : 'bg-[#00ff00] text-black' }} text-sm rounded hover:opacity-80 transition-colors">
                                {{ $language->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    @endif
                    
                    @if(!$language->is_default && !$language->translations()->exists())
                        <form action="{{ route('admin.languages.destroy', $language) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this language?')">
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
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No languages found</h3>
                <p class="text-gray-500 mb-4">Get started by adding your first language.</p>
                <a href="{{ route('admin.languages.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-[#1d003f] text-white rounded-lg hover:bg-[#2a0057] transition-colors">
                    Add Language
                </a>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Translations</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($languages as $language)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $language->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $language->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $language->status === 'active' ? 'bg-[#00ff00] text-black' : 'bg-[#ff0000] text-white' }}">
                                {{ ucfirst($language->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($language->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#00ff00] text-black">
                                    Default
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $language->translations()->count() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.languages.edit', $language) }}" 
                                   class="text-blue-600 hover:text-blue-900">Edit</a>
                                
                                @if(!$language->is_default)
                                    <form action="{{ route('admin.languages.set-default', $language) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-[#00ff00] hover:text-[#00cc00]">Set Default</button>
                                    </form>
                                    
                                    <form action="{{ route('admin.languages.toggle-status', $language) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="{{ $language->status === 'active' ? 'text-[#ff0000] hover:text-[#cc0000]' : 'text-[#00ff00] hover:text-[#00cc00]' }}">
                                            {{ $language->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                @endif
                                
                                @if(!$language->is_default && !$language->translations()->exists())
                                    <form action="{{ route('admin.languages.destroy', $language) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this language?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No languages found</h3>
                                <p class="text-gray-500 mb-4">Get started by adding your first language.</p>
                                <a href="{{ route('admin.languages.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-[#1d003f] text-white rounded-lg hover:bg-[#2a0057] transition-colors">
                                    Add Language
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($languages->hasPages())
        <div class="mt-6">
            {{ $languages->links() }}
        </div>
    @endif
</div>
@endsection
