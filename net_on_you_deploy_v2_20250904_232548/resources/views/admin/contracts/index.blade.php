@extends('admin.layouts.app')

@section('title', 'Contract Management')

@section('content')
<div class="container mx-auto px-4">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Contract Management</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.contracts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> New Contract
                    </a>
                    <button type="button" onclick="openImportModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                        <i class="fas fa-upload mr-2"></i> Import
                    </button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($contracts as $contract)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contract->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contract->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="font-medium">{{ strtoupper($contract->language) }}</span>
                                    @if(in_array($contract->language, ['en', 'es', 'pt', 'it']))
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-star mr-1"></i>
                                            Core
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contract->effective_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($contract->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a href="{{ route('admin.contracts.show', $contract) }}" class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium rounded-md">
                                    <i class="fas fa-eye mr-1"></i>
                                </a>
                                <a href="{{ route('admin.contracts.edit', $contract) }}" class="inline-flex items-center px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-xs font-medium rounded-md">
                                    <i class="fas fa-edit mr-1"></i>
                                </a>
                                <button onclick="toggleContractStatus({{ $contract->id }}, {{ $contract->is_active ? 'true' : 'false' }})" class="inline-flex items-center px-3 py-1 {{ $contract->is_active ? 'bg-orange-100 hover:bg-orange-200 text-orange-800' : 'bg-green-100 hover:bg-green-200 text-green-800' }} text-xs font-medium rounded-md">
                                    <i class="fas {{ $contract->is_active ? 'fa-pause' : 'fa-play' }} mr-1"></i>
                                </button>
                                <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium rounded-md" onclick="return confirm('Are you sure you want to delete this contract?')">
                                        <i class="fas fa-trash mr-1"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No contracts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form action="{{ route('admin.contracts.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Import Contract</h3>
                    <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <label for="contract_file" class="block text-sm font-medium text-gray-700 mb-2">Contract File</label>
                    <input type="file" id="contract_file" name="contract_file" accept=".pdf,.doc,.docx" required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

function toggleContractStatus(contractId, currentStatus) {
    const action = currentStatus ? 'deactivate' : 'activate';
    
    if (!confirm(`Are you sure you want to ${action} this contract?`)) {
        return;
    }
    
    // Create a form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ route('admin.contracts.toggle-status', ['contract' => ':id']) }}`.replace(':id', contractId);
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    form.appendChild(csrfToken);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection

