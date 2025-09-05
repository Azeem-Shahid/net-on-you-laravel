@extends('admin.layouts.app')

@section('title', 'Contract Details')

@section('content')
<div class="container mx-auto px-4">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Contract Details</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.contracts.edit', $contract) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit Contract
                    </a>
                    <a href="{{ route('admin.contracts.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Contracts
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Contract Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Contract Information</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="text-sm text-gray-900">{{ $contract->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Title</dt>
                            <dd class="text-sm text-gray-900">{{ $contract->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Version</dt>
                            <dd class="text-sm text-gray-900">{{ $contract->version }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Language</dt>
                            <dd class="text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ strtoupper($contract->language) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Effective Date</dt>
                            <dd class="text-sm text-gray-900">{{ $contract->effective_date->format('F j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="text-sm text-gray-900">
                                @if($contract->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="text-sm text-gray-900">{{ $contract->created_at->format('F j, Y \a\t g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $contract->updated_at->format('F j, Y \a\t g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Contract Statistics</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Acceptances</dt>
                            <dd class="text-sm text-gray-900">{{ $contract->acceptances()->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Is Latest Active</dt>
                            <dd class="text-sm text-gray-900">
                                @if($contract->isLatestActive())
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Content Length</dt>
                            <dd class="text-sm text-gray-900">{{ strlen($contract->content) }} characters</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Contract Content -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-medium text-gray-900">Contract Content</h4>
                    <button onclick="copyToClipboard()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium inline-flex items-center">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                    <pre class="text-sm text-gray-900 whitespace-pre-wrap font-sans">{{ $contract->content }}</pre>
                </div>
            </div>
            
            <!-- Recent Acceptances -->
            @if($contract->acceptances()->count() > 0)
            <div class="mt-8 bg-gray-50 rounded-lg p-4">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Recent Acceptances</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accepted At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($contract->acceptances()->latest()->take(10)->get() as $acceptance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $acceptance->user->name ?? 'Unknown User' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $acceptance->created_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $acceptance->ip_address ?? 'N/A' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($contract->acceptances()->count() > 10)
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-500">Showing 10 of {{ $contract->acceptances()->count() }} acceptances</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const content = `{{ addslashes($contract->content) }}`;
    navigator.clipboard.writeText(content).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy content to clipboard');
    });
}
</script>
@endsection
