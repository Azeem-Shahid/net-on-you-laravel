@extends('admin.layouts.app')

@section('title', 'Cron Job Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Cron Job Management</h1>
        <p class="text-gray-600">Manage and configure automated tasks for business operations and system maintenance</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-cogs text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Commands</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalCommands">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Business Commands</p>
                    <p class="text-2xl font-semibold text-gray-900" id="businessCommands">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Scheduled</p>
                    <p class="text-2xl font-semibold text-gray-900" id="scheduledCommands">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-history text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Last 24h</p>
                    <p class="text-2xl font-semibold text-gray-900" id="last24hExecutions">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Operations Section -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Business Operations</h2>
            <p class="text-sm text-gray-600 mt-1">Configure automated business tasks for subscriptions, commissions, magazines, and reports</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="businessCommandsContainer">
                <!-- Business commands will be loaded here -->
            </div>
        </div>
    </div>

    <!-- System Maintenance Section -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">System Maintenance</h2>
            <p class="text-sm text-gray-600 mt-1">System-level maintenance and optimization commands</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="systemCommandsContainer">
                <!-- System commands will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Recent Executions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Recent Executions</h2>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Command</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Executed By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="recentLogsTable">
                        <!-- Recent logs will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Setup Guide Modal -->
<div id="setupGuideModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Cron Job Setup Guide</h3>
                <button onclick="closeSetupGuideModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="setupGuideContent">
                <!-- Setup guide content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadStatus();
    loadBusinessCommands();
    loadSystemCommands();
    loadRecentLogs();
});

function loadStatus() {
    fetch('{{ route("admin.cron-jobs.status") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalCommands').textContent = data.stats.total_commands;
                document.getElementById('businessCommands').textContent = data.stats.business_commands;
                document.getElementById('scheduledCommands').textContent = data.stats.scheduled_commands;
                document.getElementById('last24hExecutions').textContent = data.stats.last_24h_executions;
            }
        })
        .catch(error => console.error('Error loading status:', error));
}

function loadBusinessCommands() {
    const businessCommands = @json($businessCommands);
    const container = document.getElementById('businessCommandsContainer');
    
    Object.entries(businessCommands).forEach(([category, data]) => {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'bg-gray-50 rounded-lg p-4';
        
        let commandsHtml = `<h3 class="text-lg font-semibold text-gray-900 mb-3">${data.name}</h3>`;
        
        Object.entries(data.commands).forEach(([command, info]) => {
            commandsHtml += `
                <div class="bg-white rounded-lg p-4 mb-3 border">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">${command}</h4>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getFrequencyColor(info.frequency)}">${info.frequency}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">${info.description}</p>
                    <div class="flex space-x-2">
                        <button onclick="runBusinessCommand('${command}')" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            <i class="fas fa-play mr-1"></i> Run Now
                        </button>
                        <button onclick="showSetupGuide('${command}')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                            <i class="fas fa-cog mr-1"></i> Setup
                        </button>
                        <button onclick="copyCronCommand('${info.cron_command}')" class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                            <i class="fas fa-copy mr-1"></i> Copy
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <strong>Recommended:</strong> ${info.frequency} at ${info.recommended_time}
                    </div>
                </div>
            `;
        });
        
        categoryDiv.innerHTML = commandsHtml;
        container.appendChild(categoryDiv);
    });
}

function loadSystemCommands() {
    const systemCommands = @json($systemCommands);
    const container = document.getElementById('systemCommandsContainer');
    
    Object.entries(systemCommands).forEach(([category, data]) => {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'bg-gray-50 rounded-lg p-4';
        
        let commandsHtml = `<h3 class="text-lg font-semibold text-gray-900 mb-3">${data.name}</h3>`;
        
        Object.entries(data.commands).forEach(([command, info]) => {
            commandsHtml += `
                <div class="bg-white rounded-lg p-4 mb-3 border">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">${command}</h4>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getFrequencyColor(info.frequency)}">${info.frequency}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">${info.description}</p>
                    <div class="flex space-x-2">
                        <button onclick="runBusinessCommand('${command}')" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            <i class="fas fa-play mr-1"></i> Run Now
                        </button>
                        <button onclick="showSetupGuide('${command}')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                            <i class="fas fa-cog mr-1"></i> Setup
                        </button>
                        <button onclick="copyCronCommand('${info.cron_command}')" class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                            <i class="fas fa-copy mr-1"></i> Copy
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <strong>Recommended:</strong> ${info.frequency} at ${info.recommended_time}
                    </div>
                </div>
            `;
        });
        
        categoryDiv.innerHTML = commandsHtml;
        container.appendChild(categoryDiv);
    });
}

function loadRecentLogs() {
    fetch('{{ route("admin.cron-jobs.command-history") }}?limit=10')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tableBody = document.getElementById('recentLogsTable');
                tableBody.innerHTML = '';
                
                data.logs.forEach(log => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${log.command}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${log.status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${log.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.executed_by_admin ? log.executed_by_admin.name : 'System'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(log.executed_at).toLocaleString()}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.execution_time_ms}ms</td>
                    `;
                    tableBody.appendChild(row);
                });
            }
        })
        .catch(error => console.error('Error loading recent logs:', error));
}

function runBusinessCommand(command) {
    if (!confirm(`Are you sure you want to run "${command}" now?`)) {
        return;
    }
    
    fetch('{{ route("admin.cron-jobs.run-business-command") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ command: command })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(`Command "${command}" executed successfully in ${data.execution_time}ms`);
            loadRecentLogs();
        } else {
            showError(`Failed to execute command: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error running command:', error);
        showError('Failed to execute command');
    });
}

function showSetupGuide(command) {
    fetch(`{{ route("admin.cron-jobs.setup-guide") }}?command=${encodeURIComponent(command)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalTitle').textContent = `Setup Guide: ${command}`;
                
                const content = document.getElementById('setupGuideContent');
                content.innerHTML = `
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 mb-2">${data.category}</h4>
                        <p class="text-sm text-gray-600">${data.info.description}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-900 mb-2">cPanel Setup Steps:</h5>
                        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                            ${Object.entries(data.setup_instructions.cpanel_steps).map(([step, action]) => 
                                `<li><strong>${step}:</strong> ${action}</li>`
                            ).join('')}
                        </ol>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-900 mb-2">Cron Command:</h5>
                        <div class="bg-gray-100 p-3 rounded text-sm font-mono break-all">
                            ${data.info.cron_command}
                        </div>
                        <button onclick="copyCronCommand('${data.info.cron_command}')" class="mt-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            <i class="fas fa-copy mr-1"></i> Copy Command
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-900 mb-2">Testing:</h5>
                        <div class="bg-gray-100 p-3 rounded text-sm font-mono">
                            ${data.setup_instructions.testing.test_command}
                        </div>
                    </div>
                `;
                
                document.getElementById('setupGuideModal').classList.remove('hidden');
            } else {
                showError('Failed to load setup guide');
            }
        })
        .catch(error => {
            console.error('Error loading setup guide:', error);
            showError('Failed to load setup guide');
        });
}

function closeSetupGuideModal() {
    document.getElementById('setupGuideModal').classList.add('hidden');
}

function copyCronCommand(command) {
    navigator.clipboard.writeText(command).then(() => {
        showSuccess('Cron command copied to clipboard!');
    }).catch(() => {
        showError('Failed to copy command');
    });
}

function getFrequencyColor(frequency) {
    const colors = {
        'daily': 'bg-blue-100 text-blue-800',
        'weekly': 'bg-green-100 text-green-800',
        'monthly': 'bg-purple-100 text-purple-800',
        'bimonthly': 'bg-orange-100 text-orange-800'
    };
    return colors[frequency] || 'bg-gray-100 text-gray-800';
}

function showSuccess(message) {
    // You can implement a toast notification here
    alert('Success: ' + message);
}

function showError(message) {
    // You can implement a toast notification here
    alert('Error: ' + message);
}
</script>
@endpush
