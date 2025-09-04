@extends('admin.layouts.app')

@section('title', 'Command Scheduler')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Command Scheduler</h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700 font-medium">Command Scheduler</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="openBatchCommandsModal()">
                    <i class="fas fa-play-circle mr-2"></i> Run Batch Commands
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="openScheduleCommandModal()">
                    <i class="fas fa-plus mr-2"></i> Schedule Command
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Commands</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalCommands">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-play-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Commands</p>
                    <p class="text-2xl font-semibold text-gray-900" id="activeCommands">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-indigo-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Executions</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalExecutions">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Execution Time</p>
                    <p class="text-2xl font-semibold text-gray-900" id="avgExecutionTime">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Scheduled Commands -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Scheduled Commands</h3>
                        <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="refreshCommands()">
                            <i class="fas fa-sync-alt mr-2"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Command</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Run</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Run</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="scheduledCommandsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Commands will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Logs -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="runCommand('system:core-maintenance')">
                            <i class="fas fa-cogs mr-2"></i> Core Maintenance
                        </button>
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="runCommand('subscriptions:check-expiry')">
                            <i class="fas fa-check-circle mr-2"></i> Check Subscriptions
                        </button>
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="runCommand('system:health-check')">
                            <i class="fas fa-heartbeat mr-2"></i> System Health Check
                        </button>
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="runCommand('system:cleanup')">
                            <i class="fas fa-broom mr-2"></i> System Cleanup
                        </button>
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200" onclick="runCommand('system:backup-database')">
                            <i class="fas fa-database mr-2"></i> Backup Database
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Logs -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Recent Logs</h3>
                        <button class="inline-flex items-center px-3 py-2 border border-red-300 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 transition-colors duration-200" onclick="clearLogs()">
                            <i class="fas fa-trash mr-2"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div id="recentLogs">
                        <!-- Recent logs will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Command Logs Modal -->
    <div id="commandLogsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Command Logs</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('commandLogsModal')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
                            <div class="mb-6">
                    <!-- Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="filterCommand">
                                <option value="">All Commands</option>
                            </select>
                        </div>
                        <div>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="filterStatus">
                                <option value="">All Statuses</option>
                                <option value="success">Success</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="filterDateFrom" placeholder="From Date">
                        </div>
                        <div>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="filterDateTo" placeholder="To Date">
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Command</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Executed By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Executed At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Execution Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody" class="bg-white divide-y divide-gray-200">
                                <!-- Logs will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="logsPagination" class="flex justify-center mt-6">
                        <!-- Pagination will be loaded here -->
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('commandLogsModal')">Close</button>
                    <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200" onclick="exportLogs()">
                        <i class="fas fa-download mr-2"></i> Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Command Modal -->
    <div id="scheduleCommandModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Schedule Command</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('scheduleCommandModal')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="scheduleCommandForm">
                <div class="space-y-4">
                    <div>
                        <label for="commandSelect" class="block text-sm font-medium text-gray-700 mb-2">Command</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="commandSelect" required>
                            <option value="">Select a command</option>
                        </select>
                    </div>
                    <div>
                        <label for="frequencySelect" class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="frequencySelect" required>
                            <option value="">Select frequency</option>
                        </select>
                    </div>
                    <div>
                        <label for="statusSelect" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="statusSelect" required>
                            <option value="">Select status</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('scheduleCommandModal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">Schedule Command</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Batch Commands Modal -->
    <div id="batchCommandsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Run Batch Commands</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('batchCommandsModal')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Predefined Groups</h4>
                    <div class="space-y-3">
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-purple-300 text-purple-700 bg-white hover:bg-purple-50 font-medium rounded-lg transition-colors duration-200" onclick="runCommand('system:core-maintenance')">
                            <i class="fas fa-cogs mr-2"></i> Core Maintenance
                        </button>
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 text-blue-700 bg-white hover:bg-blue-50 font-medium rounded-lg transition-colors duration-200" onclick="runCommandGroup('maintenance')">
                            <i class="fas fa-tools mr-2"></i> System Maintenance
                        </button>
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-indigo-300 text-indigo-700 bg-white hover:bg-indigo-50 font-medium rounded-lg transition-colors duration-200" onclick="runCommandGroup('update')">
                            <i class="fas fa-sync-alt mr-2"></i> System Update
                        </button>
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 text-green-700 bg-white hover:bg-green-50 font-medium rounded-lg transition-colors duration-200" onclick="runCommandGroup('business')">
                            <i class="fas fa-briefcase mr-2"></i> Business Operations
                        </button>
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-yellow-300 text-yellow-700 bg-white hover:bg-yellow-50 font-medium rounded-lg transition-colors duration-200" onclick="runCommandGroup('cleanup')">
                            <i class="fas fa-broom mr-2"></i> Deep Cleanup
                        </button>
                    </div>
                </div>
                                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Custom Selection</h4>
                    <div id="customCommandsList">
                        <!-- Commands will be loaded here -->
                    </div>
                    <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 mt-4" onclick="runCustomCommands()">
                        <i class="fas fa-play mr-2"></i> Run Selected Commands
                    </button>
                </div>
            </div>
            
            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Batch Execution Results</h4>
                <div id="batchResults" class="bg-gray-50 p-4 rounded-lg border border-gray-200" style="max-height: 300px; overflow-y: auto;">
                    <div class="text-gray-500 text-center">Results will appear here after execution...</div>
                </div>
            </div>
            
            <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('batchCommandsModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Command Output Modal -->
    <div id="commandOutputModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Command Output</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('commandOutputModal')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Command:</span>
                        <span id="outputCommandName" class="ml-2 text-gray-900"></span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Status:</span>
                        <span id="outputStatus" class="ml-2 text-gray-900"></span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Execution Time:</span>
                        <span id="outputExecutionTime" class="ml-2 text-gray-900"></span>
                    </div>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Output:</span>
                    <pre id="commandOutput" class="mt-2 bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm text-gray-800" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
            </div>
            <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" onclick="closeModal('commandOutputModal')">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Global variables
let currentPage = 1;
let logsPerPage = 20;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Command Scheduler page loaded');
    console.log('📊 Starting initialization...');
    
    // Load data with error handling
    try {
        console.log('📈 Loading statistics...');
        loadStats();
        
        console.log('📋 Loading scheduled commands...');
        loadScheduledCommands();
        
        console.log('📝 Loading recent logs...');
        loadRecentLogs();
        
        console.log('🔧 Populating command selects...');
        populateCommandSelects();
        
        console.log('📦 Populating custom commands list...');
        populateCustomCommandsList();
        
        console.log('🎯 Setting up event listeners...');
        setupEventListeners();
        
        console.log('✅ Initialization completed successfully');
    } catch (error) {
        console.error('❌ Error during initialization:', error);
    }
});

// Setup event listeners
function setupEventListeners() {
    console.log('🎯 Setting up event listeners...');
    
    try {
        // Filter change events
        const filterCommand = document.getElementById('filterCommand');
        const filterStatus = document.getElementById('filterStatus');
        const filterDateFrom = document.getElementById('filterDateFrom');
        const filterDateTo = document.getElementById('filterDateTo');
        const scheduleCommandForm = document.getElementById('scheduleCommandForm');
        
        console.log('🔍 Found elements:', {
            filterCommand: !!filterCommand,
            filterStatus: !!filterStatus,
            filterDateFrom: !!filterDateFrom,
            filterDateTo: !!filterDateTo,
            scheduleCommandForm: !!scheduleCommandForm
        });
        
        if (filterCommand) {
            filterCommand.addEventListener('change', () => {
                console.log('🔍 Filter command changed');
                loadLogs(1);
            });
        }
        if (filterStatus) {
            filterStatus.addEventListener('change', () => {
                console.log('🔍 Filter status changed');
                loadLogs(1);
            });
        }
        if (filterDateFrom) {
            filterDateFrom.addEventListener('change', () => {
                console.log('🔍 Filter date from changed');
                loadLogs(1);
            });
        }
        if (filterDateTo) {
            filterDateTo.addEventListener('change', () => {
                console.log('🔍 Filter date to changed');
                loadLogs(1);
            });
        }
        if (scheduleCommandForm) {
            scheduleCommandForm.addEventListener('submit', (e) => {
                console.log('📝 Schedule command form submitted');
                handleScheduleCommand(e);
            });
        }
        
        // Check for quick action buttons
        console.log('🔍 Looking for quick action buttons...');
        const quickActionButtons = document.querySelectorAll('button[onclick*="runCommand"]');
        console.log('🔘 Quick action buttons found:', quickActionButtons.length);
        
        quickActionButtons.forEach((button, index) => {
            console.log(`🔘 Quick action button ${index + 1}:`, button.textContent.trim());
        });
        
        console.log('✅ Event listeners setup completed');
    } catch (error) {
        console.error('❌ Error setting up event listeners:', error);
    }
}

// Load statistics
function loadStats() {
    console.log('📊 Fetching statistics from:', '{{ route("admin.command-scheduler.stats") }}');
    
    fetch('{{ route("admin.command-scheduler.stats") }}')
        .then(response => {
            console.log('📡 Stats response status:', response.status);
            console.log('📡 Stats response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('📊 Stats data received:', data);
            
            if (data.success && data.stats) {
                const stats = data.stats;
                console.log('📈 Processing stats:', stats);
                
                const totalCommandsEl = document.getElementById('totalCommands');
                const activeCommandsEl = document.getElementById('activeCommands');
                const totalExecutionsEl = document.getElementById('totalExecutions');
                const avgExecutionTimeEl = document.getElementById('avgExecutionTime');
                
                console.log('🔍 Found elements:', {
                    totalCommands: !!totalCommandsEl,
                    activeCommands: !!activeCommandsEl,
                    totalExecutions: !!totalExecutionsEl,
                    avgExecutionTime: !!avgExecutionTimeEl
                });
                
                if (totalCommandsEl) totalCommandsEl.textContent = stats.total_commands || 0;
                if (activeCommandsEl) activeCommandsEl.textContent = stats.active_commands || 0;
                if (totalExecutionsEl) totalExecutionsEl.textContent = stats.total_executions || 0;
                if (avgExecutionTimeEl) avgExecutionTimeEl.textContent = Math.round(stats.avg_execution_time || 0) + 'ms';
                
                console.log('✅ Stats updated successfully');
            } else {
                console.error('❌ Failed to load stats:', data.message);
                setDefaultStats();
            }
        })
        .catch(error => {
            console.error('❌ Error loading stats:', error);
            setDefaultStats();
        });
}

function setDefaultStats() {
    console.log('🔄 Setting default stats values');
    const elements = ['totalCommands', 'activeCommands', 'totalExecutions', 'avgExecutionTime'];
    elements.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = id === 'avgExecutionTime' ? '0ms' : '0';
        } else {
            console.warn('⚠️ Element not found:', id);
        }
    });
}

// Load scheduled commands
function loadScheduledCommands() {
    console.log('📋 Loading scheduled commands...');
    
    const tableBody = document.getElementById('scheduledCommandsTable');
    if (!tableBody) {
        console.error('❌ Table body element not found: scheduledCommandsTable');
        return;
    }
    
    console.log('📋 Table body found, setting loading state');
    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';
    
    // Make AJAX call to get scheduled commands
    console.log('📡 Fetching scheduled commands from:', '{{ route("admin.command-scheduler.scheduled-commands") }}');
    
    fetch('{{ route("admin.command-scheduler.scheduled-commands") }}')
        .then(response => {
            console.log('📡 Commands response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📋 Commands data received:', data);
            
            if (data.success && data.commands) {
                console.log('📋 Processing commands:', data.commands.length, 'commands found');
                
                if (data.commands.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No commands scheduled</td></tr>';
                    console.log('📋 No commands to display');
                    return;
                }
                
                const commandsHtml = data.commands.map(command => {
                    console.log('📋 Processing command:', command);
                    return `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${command.command}</div>
                                    <div class="text-sm text-gray-500">${command.description || ''}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">${command.frequency}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${command.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                    ${command.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${command.next_run ? command.next_run : 'N/A'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${command.last_run || 'Never'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 transition-colors duration-200" onclick="runCommand('${command.command}')">
                                        <i class="fas fa-play mr-1"></i> Run
                                    </button>
                                    <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200" onclick="viewLogs('${command.command}')">
                                        <i class="fas fa-list mr-1"></i> Logs
                                    </button>
                                    <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-yellow-600 hover:bg-yellow-700 transition-colors duration-200" onclick="editSchedule('${command.command}')">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                    <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white ${command.status === 'active' ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'} transition-colors duration-200" onclick="toggleCommandStatus('${command.command}', '${command.status}')">
                                        <i class="fas ${command.status === 'active' ? 'fa-pause' : 'fa-play'} mr-1"></i> ${command.status === 'active' ? 'Disable' : 'Enable'}
                                    </button>
                                    <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 transition-colors duration-200" onclick="deleteCommand('${command.command}')">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
                
                tableBody.innerHTML = commandsHtml;
                console.log('✅ Commands table updated successfully');
            } else {
                console.warn('⚠️ No commands data or success false:', data);
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No commands scheduled</td></tr>';
            }
        })
        .catch(error => {
            console.error('❌ Error loading scheduled commands:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-red-500">Error loading commands</td></tr>';
        });
}

// Load recent logs
function loadRecentLogs() {
    const recentLogsDiv = document.getElementById('recentLogs');
    recentLogsDiv.innerHTML = '<div class="text-center text-muted">Loading...</div>';
    
    fetch('{{ route("admin.command-scheduler.logs") }}?limit=10')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.logs) {
                const logs = data.logs;
                
                if (logs.length === 0) {
                    recentLogsDiv.innerHTML = '<div class="text-center text-muted">No logs found</div>';
                    return;
                }
                
                recentLogsDiv.innerHTML = logs.map(log => `
                    <div class="border border-gray-200 rounded-lg p-4 mb-3 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">${log.command}</div>
                                <div class="text-sm text-gray-500 mt-1">${log.executed_at}</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${log.status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${log.status === 'success' ? '✅ Success' : '❌ Failed'}
                            </span>
                        </div>
                        <div class="mt-2">
                            <div class="text-xs text-gray-500">
                                Executed by: ${log.executed_by} | 
                                Time: ${log.execution_time}
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                recentLogsDiv.innerHTML = '<div class="text-center text-muted">No logs found</div>';
            }
        })
        .catch(error => {
            console.error('Error loading recent logs:', error);
            recentLogsDiv.innerHTML = '<div class="text-center text-red-500">Error loading logs</div>';
        });
}

// Populate command selects
function populateCommandSelects() {
    try {
        const commands = {
            'system:cleanup': 'Clean up temporary files and logs',
            'system:health-check': 'Check system health status',
            'subscriptions:check-expiry': 'Check subscription expiry dates',
            'commissions:check-eligibility': 'Check commission eligibility',
            'system:backup-database': 'Create database backup',
            'system:generate-reports': 'Generate system reports'
        };
        
        const commandSelect = document.getElementById('commandSelect');
        const filterCommand = document.getElementById('filterCommand');
        
        if (commandSelect && filterCommand) {
            Object.entries(commands).forEach(([command, description]) => {
                const option = new Option(description, command);
                commandSelect.add(option.cloneNode(true));
                filterCommand.add(option);
            });
        }
        
        // Populate frequency options
        const frequencies = {
            'hourly': 'Every Hour',
            'daily': 'Every Day',
            'weekly': 'Every Week',
            'monthly': 'Every Month'
        };
        const frequencySelect = document.getElementById('frequencySelect');
        
        if (frequencySelect) {
            Object.entries(frequencies).forEach(([frequency, label]) => {
                frequencySelect.add(new Option(label, frequency));
            });
        }
        
        // Populate status options
        const statuses = {
            'active': 'Active',
            'inactive': 'Inactive',
            'paused': 'Paused'
        };
        const statusSelect = document.getElementById('statusSelect');
        
        if (statusSelect) {
            Object.entries(statuses).forEach(([status, label]) => {
                statusSelect.add(new Option(label, status));
            });
        }
        
        console.log('Command selects populated');
    } catch (error) {
        console.error('Error populating command selects:', error);
    }
}

// Run command
function runCommand(commandName) {
    console.log('🚀 Running command:', commandName);
    console.log('🎯 Event target:', event.target);
    console.log('🎯 Event:', event);
    
    if (!confirm(`Are you sure you want to run "${commandName}"?`)) {
        console.log('❌ User cancelled command execution');
        return;
    }
    
    const button = event.target.closest('button');
    console.log('🔘 Button element:', button);
    
    if (!button) {
        console.error('❌ Button element not found');
        return;
    }
    
    const originalText = button.innerHTML;
    console.log('📝 Original button text:', originalText);
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Running...';
    button.disabled = true;
    
    console.log('📡 Sending command execution request...');
    console.log('📡 URL:', '{{ route("admin.command-scheduler.run-command") }}');
    console.log('📡 CSRF Token:', '{{ csrf_token() }}');
    
    fetch('{{ route("admin.command-scheduler.run-command") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ command: commandName })
    })
    .then(response => {
        console.log('📡 Command response status:', response.status);
        console.log('📡 Command response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('📡 Command response data:', data);
        
        if (data.success) {
            console.log('✅ Command executed successfully');
            showSuccess(`Command "${commandName}" executed successfully in ${data.execution_time}`);
            showCommandOutput(commandName, 'success', data.execution_time, data.output);
        } else {
            console.error('❌ Command execution failed:', data.message);
            showError(`Command "${commandName}" failed: ${data.message}`);
        }
        
        // Refresh data
        console.log('🔄 Refreshing data after command execution...');
        loadStats();
        loadScheduledCommands();
        loadRecentLogs();
    })
    .catch(error => {
        console.error('❌ Error running command:', error);
        showError(`Failed to run command "${commandName}"`);
    })
    .finally(() => {
        console.log('🔄 Restoring button state');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Schedule command
function handleScheduleCommand(event) {
    event.preventDefault();
    
    const formData = {
        command: document.getElementById('commandSelect').value,
        frequency: document.getElementById('frequencySelect').value,
        status: document.getElementById('statusSelect').value
    };
    
    fetch('{{ route("admin.command-scheduler.schedule-command") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Command scheduled successfully');
            closeModal('scheduleCommandModal');
            loadScheduledCommands();
            loadStats();
        } else {
            showError(`Failed to schedule command: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error scheduling command:', error);
        showError('Failed to schedule command');
    });
}

// View logs
function viewLogs(command = null) {
    if (command) {
        document.getElementById('filterCommand').value = command;
    }
    
    loadLogs(1);
            openModal('commandLogsModal');
}

// Load logs with pagination
function loadLogs(page) {
    currentPage = page;
    const tableBody = document.getElementById('logsTableBody');
    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';
    
    const params = new URLSearchParams({
        page: page,
        command: document.getElementById('filterCommand').value,
        status: document.getElementById('filterStatus').value,
        date_from: document.getElementById('filterDateFrom').value,
        date_to: document.getElementById('filterDateTo').value
    });
    
    fetch(`{{ route("admin.command-scheduler.logs") }}?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayLogs(data.logs);
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Failed to load logs</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading logs:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading logs</td></tr>';
        });
}

// Display logs in table
function displayLogs(logs) {
    const tableBody = document.getElementById('logsTableBody');
    
    if (logs.data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No logs found</td></tr>';
        return;
    }
    
    tableBody.innerHTML = logs.data.map(log => `
        <tr>
            <td><strong>${log.command}</strong></td>
            <td>
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${log.status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${log.status === 'success' ? '✅ Success' : '❌ Failed'}
                </span>
            </td>
            <td>${log.executed_by_admin ? log.executed_by_admin.name : 'System'}</td>
            <td>${new Date(log.executed_at).toLocaleString()}</td>
            <td>${log.execution_time_ms ? log.execution_time_ms + 'ms' : 'N/A'}</td>
            <td>
                <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200" onclick="showCommandOutput('${log.command}', '${log.status}', '${log.execution_time_ms || 'N/A'}ms', '${log.output || 'No output'}')">
                    <i class="fas fa-eye mr-1"></i> View
                </button>
            </td>
        </tr>
    `).join('');
    
    // Update pagination
    updatePagination(logs);
}

// Update pagination
function updatePagination(logs) {
    const paginationDiv = document.getElementById('logsPagination');
    
    if (logs.last_page <= 1) {
        paginationDiv.innerHTML = '';
        return;
    }
    
    let paginationHtml = '<div class="flex items-center space-x-1">';
    
    // Previous button
    if (logs.current_page > 1) {
        paginationHtml += `<a href="#" onclick="loadLogs(${logs.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Previous</a>`;
    }
    
    // Page numbers
    for (let i = 1; i <= logs.last_page; i++) {
        if (i === logs.current_page) {
            paginationHtml += `<span class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">${i}</span>`;
        } else {
            paginationHtml += `<a href="#" onclick="loadLogs(${i})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">${i}</a>`;
        }
    }
    
    // Next button
    if (logs.current_page < logs.last_page) {
        paginationHtml += `<a href="#" onclick="loadLogs(${logs.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Next</a>`;
    }
    
    paginationHtml += '</div>';
    paginationDiv.innerHTML = paginationHtml;
}

// Clear logs
function clearLogs() {
    if (!confirm('Are you sure you want to clear all command logs? This action cannot be undone.')) {
        return;
    }
    
    fetch('{{ route("admin.command-scheduler.clear-logs") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            loadStats();
            loadRecentLogs();
            if (document.getElementById('commandLogsModal').classList.contains('show')) {
                loadLogs(1);
            }
        } else {
            showError(`Failed to clear logs: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error clearing logs:', error);
        showError('Failed to clear logs');
    });
}

// Export logs
function exportLogs() {
    const params = new URLSearchParams({
        command: document.getElementById('filterCommand').value,
        status: document.getElementById('filterStatus').value,
        date_from: document.getElementById('filterDateFrom').value,
        date_to: document.getElementById('filterDateTo').value
    });
    
    window.open(`{{ route("admin.command-scheduler.export-logs") }}?${params}`, '_blank');
}

// Show command output
function showCommandOutput(command, status, executionTime, output) {
    document.getElementById('outputCommandName').textContent = command;
    document.getElementById('outputStatus').innerHTML = `
        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
            ${status === 'success' ? '✅ Success' : '❌ Failed'}
        </span>
    `;
    document.getElementById('outputExecutionTime').textContent = executionTime;
    document.getElementById('commandOutput').textContent = output || 'No output available';
    
    openModal('commandOutputModal');
}



// Edit schedule
function editSchedule(command) {
    // Find the command in the scheduled commands
    const commands = [
        {
            command: 'system:cleanup',
            description: 'Clean up temporary files and logs',
            frequency: 'daily',
            status: 'active',
            next_run_at: new Date(Date.now() + 86400000).toISOString(),
            latest_log: { executed_at: new Date().toISOString() }
        },
        {
            command: 'subscriptions:check-expiry',
            description: 'Check and update subscription statuses',
            frequency: 'hourly',
            status: 'active',
            next_run_at: new Date(Date.now() + 3600000).toISOString(),
            latest_log: { executed_at: new Date(Date.now() - 3600000).toISOString() }
        }
    ];
    const commandData = commands.find(c => c.command === command);
    
    if (commandData) {
        document.getElementById('commandSelect').value = command;
        document.getElementById('frequencySelect').value = commandData.frequency;
        document.getElementById('statusSelect').value = commandData.status;
        
        openModal('scheduleCommandModal');
    }
}

// Toggle command status (enable/disable)
function toggleCommandStatus(command, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = currentStatus === 'active' ? 'disable' : 'enable';
    
    if (!confirm(`Are you sure you want to ${action} the command "${command}"?`)) {
        return;
    }
    
    fetch(`{{ route('admin.command-scheduler.toggle-command-status', ['scheduledCommand' => ':command']) }}`.replace(':command', command), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(`Command "${command}" ${action}d successfully`);
            loadScheduledCommands();
            loadStats();
        } else {
            showError(`Failed to ${action} command: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error toggling command status:', error);
        showError(`Failed to ${action} command`);
    });
}

// Delete command
function deleteCommand(command) {
    if (!confirm(`Are you sure you want to delete the command "${command}"? This action cannot be undone.`)) {
        return;
    }
    
    fetch(`{{ route('admin.command-scheduler.delete-command', ['scheduledCommand' => ':command']) }}`.replace(':command', command), {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(`Command "${command}" deleted successfully`);
            loadScheduledCommands();
            loadStats();
        } else {
            showError(`Failed to delete command: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error deleting command:', error);
        showError('Failed to delete command');
    });
}

// Refresh commands
function refreshCommands() {
    loadScheduledCommands();
    loadStats();
}

// Run command group
function runCommandGroup(groupType) {
    console.log('🚀 Running command group:', groupType);
    
    if (!confirm(`Are you sure you want to run the ${groupType} command group?`)) {
        console.log('❌ User cancelled command group execution');
        return;
    }
    
    const resultsDiv = document.getElementById('batchResults');
    if (!resultsDiv) {
        console.error('❌ Batch results div not found');
        return;
    }
    
    resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Executing commands...</div>';
    
    // Simulate batch execution
    setTimeout(() => {
        const mockData = {
            type: groupType,
            total_execution_time: Math.floor(Math.random() * 2000) + 500,
            total_commands: getCommandsForGroup(groupType).length,
            results: getCommandsForGroup(groupType).map(cmd => ({
                command: cmd,
                status: Math.random() > 0.2 ? 'success' : 'failed',
                execution_time: Math.floor(Math.random() * 500) + 100
            }))
        };
        
        displayBatchResults(mockData);
        showSuccess(`${groupType} commands executed successfully in ${mockData.total_execution_time}ms`);
        
        // Refresh data
        loadStats();
        loadScheduledCommands();
        loadRecentLogs();
    }, 2000);
}

// Get commands for a specific group
function getCommandsForGroup(groupType) {
    const groups = {
        'maintenance': ['system:cleanup', 'system:health-check', 'system:optimize-cache', 'system:clear-expired-reports'],
        'update': ['system:generate-reports', 'system:backup-database', 'subscriptions:check-expiry', 'commissions:check-eligibility'],
        'business': ['subscriptions:check-expiry', 'commissions:check-eligibility', 'commissions:re-evaluate-eligibility'],
        'cleanup': ['system:cleanup', 'system:clear-expired-reports', 'system:optimize-cache']
    };
    
    return groups[groupType] || [];
}

// Display batch execution results
function displayBatchResults(data) {
    const resultsDiv = document.getElementById('batchResults');
    
    let html = `<div class="text-success mb-2"><strong>✅ ${data.type} completed successfully!</strong></div>`;
    html += `<div class="mb-2"><strong>Total Time:</strong> ${data.total_execution_time}ms</div>`;
    html += `<div class="mb-2"><strong>Commands Executed:</strong> ${data.total_commands}</div>`;
    html += '<hr>';
    
    data.results.forEach(result => {
        const statusIcon = result.status === 'success' ? '✅' : '❌';
        const statusClass = result.status === 'success' ? 'text-success' : 'text-danger';
        
        html += `<div class="mb-1">
            <span class="${statusClass}">${statusIcon} ${result.command}</span>
            <small class="text-muted">(${result.execution_time}ms)</small>
        </div>`;
    });
    
    resultsDiv.innerHTML = html;
}

// Run custom selected commands
function runCustomCommands() {
    console.log('🚀 Running custom commands');
    
    const selectedCommands = [];
    const checkboxes = document.querySelectorAll('#customCommandsList input[type="checkbox"]:checked');
    
    checkboxes.forEach(checkbox => {
        selectedCommands.push(checkbox.value);
    });
    
    console.log('📋 Selected commands:', selectedCommands);
    
    if (selectedCommands.length === 0) {
        showError('Please select at least one command');
        return;
    }
    
    if (!confirm(`Are you sure you want to run ${selectedCommands.length} selected commands?`)) {
        console.log('❌ User cancelled custom commands execution');
        return;
    }
    
    const resultsDiv = document.getElementById('batchResults');
    if (!resultsDiv) {
        console.error('❌ Batch results div not found');
        return;
    }
    
    resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Executing commands...</div>';
    
    fetch('{{ route("admin.command-scheduler.run-multiple-commands") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            type: 'custom',
            commands: selectedCommands
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayBatchResults(data);
            showSuccess(`Custom commands executed successfully in ${data.total_execution_time}ms`);
            
            // Refresh data
            loadStats();
            loadScheduledCommands();
            loadRecentLogs();
        } else {
            showError(`Failed to execute custom commands: ${data.message}`);
            resultsDiv.innerHTML = '<div class="text-danger">Execution failed: ' + data.message + '</div>';
        }
    })
    .catch(error => {
        console.error('Error running custom commands:', error);
        showError('Failed to execute custom commands');
        resultsDiv.innerHTML = '<div class="text-danger">Execution failed</div>';
    });
}

// Populate custom commands list
function populateCustomCommandsList() {
    console.log('📦 Populating custom commands list...');
    
    try {
        const commands = {
            'system:cleanup': 'Clean up temporary files and logs',
            'system:health-check': 'Check system health status',
            'subscriptions:check-expiry': 'Check subscription expiry dates',
            'commissions:check-eligibility': 'Check commission eligibility',
            'system:backup-database': 'Create database backup',
            'system:generate-reports': 'Generate system reports'
        };
        const container = document.getElementById('customCommandsList');
        
        if (!container) {
            console.error('❌ Custom commands container not found');
            return;
        }
        
        Object.entries(commands).forEach(([command, description]) => {
            const div = document.createElement('div');
            div.className = 'flex items-start mb-3';
            div.innerHTML = `
                <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1" type="checkbox" value="${command}" id="cmd_${command}">
                <label class="ml-3 text-sm" for="cmd_${command}">
                    <div class="font-medium text-gray-900">${command}</div>
                    <div class="text-gray-500">${description}</div>
                </label>
            `;
            container.appendChild(div);
        });
        
        console.log('✅ Custom commands list populated');
    } catch (error) {
        console.error('❌ Error populating custom commands list:', error);
    }
}

// Utility functions
function showSuccess(message) {
    console.log('✅ Success:', message);
    // You can implement your own success notification here
    alert('Success: ' + message);
}

function showError(message) {
    console.error('❌ Error:', message);
    // You can implement your own error notification here
    alert('Error: ' + message);
}

// Modal functions
function openModal(modalId) {
    console.log('🔓 Opening modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        console.error('❌ Modal not found:', modalId);
    }
}

function closeModal(modalId) {
    console.log('🔒 Closing modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    } else {
        console.error('❌ Modal not found:', modalId);
    }
}

function openBatchCommandsModal() {
    console.log('🚀 Opening batch commands modal');
    openModal('batchCommandsModal');
}

function openScheduleCommandModal() {
    console.log('📅 Opening schedule command modal');
    openModal('scheduleCommandModal');
}

// Additional utility functions
function refreshCommands() {
    console.log('🔄 Refreshing commands');
    loadScheduledCommands();
    loadStats();
}

function clearLogs() {
    console.log('🗑️ Clearing logs');
    if (!confirm('Are you sure you want to clear all command logs? This action cannot be undone.')) {
        return;
    }
    
    fetch('{{ route("admin.command-scheduler.clear-logs") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            loadStats();
            loadRecentLogs();
            if (document.getElementById('commandLogsModal').classList.contains('show')) {
                loadLogs(1);
            }
        } else {
            showError(`Failed to clear logs: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error clearing logs:', error);
        showError('Failed to clear logs');
    });
}

function exportLogs() {
    console.log('📤 Exporting logs');
    const params = new URLSearchParams({
        command: document.getElementById('filterCommand').value,
        status: document.getElementById('filterStatus').value,
        date_from: document.getElementById('filterDateFrom').value,
        date_to: document.getElementById('filterDateTo').value
    });
    
    window.open(`{{ route("admin.command-scheduler.export-logs") }}?${params}`, '_blank');
}
</script>
@endpush

@push('styles')
<style>
/* Custom styles for command scheduler */
.log-item {
    transition: all 0.3s ease;
}

.log-item:hover {
    background-color: #f9fafb;
    transform: translateY(-1px);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }
    
    .admin-sidebar {
        position: fixed;
        z-index: 50;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endpush
