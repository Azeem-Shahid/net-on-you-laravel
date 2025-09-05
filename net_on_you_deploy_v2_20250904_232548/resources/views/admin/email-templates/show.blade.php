@extends('admin.layouts.app')

@section('title', 'Email Template Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $emailTemplate->name }}</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Edit Template
                    </a>
                    <a href="{{ route('admin.email-templates.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                        Back to Templates
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ strtoupper($emailTemplate->language) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Created</label>
                    <p class="text-sm text-gray-900">{{ $emailTemplate->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                <div class="bg-gray-50 p-3 rounded-md">
                    <p class="text-sm text-gray-900">{{ $emailTemplate->subject }}</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Body</label>
                <div class="bg-gray-50 p-4 rounded-md max-h-96 overflow-y-auto">
                    <div class="prose prose-sm max-w-none">
                        {!! nl2br(e($emailTemplate->body)) !!}
                    </div>
                </div>
            </div>

            @if(!empty($emailTemplate->variables))
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Template Variables</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($emailTemplate->variables as $variable)
                        <div class="bg-gray-50 px-3 py-2 rounded-md">
                            <code class="text-sm text-gray-800">{ {{ $variable }} }</code>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="flex space-x-3">
                    <button onclick="duplicateTemplate({{ $emailTemplate->id }})" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Duplicate Template
                    </button>
                    <button onclick="deleteTemplate({{ $emailTemplate->id }})" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Delete Template
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function duplicateTemplate(templateId) {
    if (confirm('Are you sure you want to duplicate this template?')) {
        window.location.href = `/admin/email-templates/${templateId}/duplicate`;
    }
}

function deleteTemplate(templateId) {
    if (confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
        fetch(`/admin/email-templates/${templateId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.email-templates.index") }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the template.');
        });
    }
}
</script>
@endpush

