@extends('admin.layouts.app')

@section('title', 'Create Email Campaign')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Email Campaign</h1>
                    <p class="text-sm text-gray-600">Set up and configure a new email campaign</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.campaigns.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Campaigns
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-6 border-b border-gray-200">
                        <h5 class="text-lg font-medium text-gray-900">Campaign Details</h5>
                    </div>
                    <div class="px-6 py-6">
                        <form method="POST" action="{{ route('admin.campaigns.store') }}" id="campaignForm">
                            @csrf
                            
                            <div class="mb-6">
                                <label for="template_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Template <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                        id="template_id" name="template_id" required>
                                    <option value="">Select Template</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" 
                                                data-variables="{{ json_encode($template->variables) }}">
                                            {{ $template->name }} ({{ strtoupper($template->language) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('template_id')
                                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                    Campaign Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                       id="subject" name="subject" value="{{ old('subject') }}" 
                                       placeholder="Enter campaign subject..." required>
                                @error('subject')
                                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Recipient Selection <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                               type="radio" name="recipient_type" 
                                               id="all_users" value="all_users" 
                                               {{ old('recipient_type') == 'all_users' ? 'checked' : '' }} required>
                                        <span class="ml-3 text-sm text-gray-700">
                                            <strong>All Users</strong> ({{ $userCount }} users)
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                               type="radio" name="recipient_type" 
                                               id="marketing_opt_in" value="marketing_opt_in" 
                                               {{ old('recipient_type') == 'marketing_opt_in' ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm text-gray-700">
                                            <strong>Marketing Opt-in Only</strong> ({{ $marketingOptInCount }} users)
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                               type="radio" name="recipient_type" 
                                               id="custom_selection" value="custom_selection" 
                                               {{ old('recipient_type') == 'custom_selection' ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm text-gray-700">
                                            <strong>Custom Selection</strong>
                                        </span>
                                    </label>
                                </div>
                                @error('recipient_type')
                                    <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Custom User Selection (initially hidden) -->
                            <div id="customUserSelection" class="mb-6 hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Users</label>
                                <div class="flex space-x-2">
                                    <input type="text" 
                                           class="flex-1 px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                           id="userSearch" 
                                           placeholder="Search users by name or email...">
                                    <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-medium transition-colors duration-200" 
                                            type="button" onclick="searchUsers()">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Search
                                    </button>
                                </div>
                                <div id="userSearchResults" class="mt-3"></div>
                                <div id="selectedUsers" class="mt-3"></div>
                            </div>

                            <div class="mb-6">
                                <label for="test_email" class="block text-sm font-medium text-gray-700 mb-2">Test Email (Optional)</label>
                                <div class="flex space-x-2">
                                    <input type="email" 
                                           class="flex-1 px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                           id="test_email" name="test_email" 
                                           placeholder="Enter test email address...">
                                    <button type="button" 
                                            class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md text-sm font-medium transition-colors duration-200" 
                                            onclick="sendTestEmail()">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Send Test
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('admin.campaigns.index') }}" 
                                   class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm font-medium transition-colors duration-200">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Create Campaign
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-6 border-b border-gray-200">
                        <h5 class="text-lg font-medium text-gray-900">Campaign Preview</h5>
                    </div>
                    <div class="px-6 py-6">
                        <div id="campaignPreview" class="text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm">Select a template to preview your campaign</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show/hide custom user selection
document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const customSelection = document.getElementById('customUserSelection');
        if (this.value === 'custom_selection') {
            customSelection.classList.remove('hidden');
        } else {
            customSelection.classList.add('hidden');
        }
    });
});

// Template selection change
document.getElementById('template_id').addEventListener('change', function() {
    const templateId = this.value;
    if (templateId) {
        // Load template preview
        loadTemplatePreview(templateId);
    }
});

function loadTemplatePreview(templateId) {
    // Implementation for loading template preview
    console.log('Loading template preview for:', templateId);
}

function searchUsers() {
    const searchTerm = document.getElementById('userSearch').value;
    if (!searchTerm) return;
    
    // Implementation for user search
    console.log('Searching users:', searchTerm);
}

function sendTestEmail() {
    const testEmail = document.getElementById('test_email').value;
    if (!testEmail) {
        alert('Please enter a test email address');
        return;
    }
    
    // Implementation for sending test email
    console.log('Sending test email to:', testEmail);
}

// Form submission
document.getElementById('campaignForm').addEventListener('submit', function(e) {
    // Form validation can be added here
    console.log('Submitting campaign form');
});
</script>
@endpush
