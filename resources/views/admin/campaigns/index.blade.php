@extends('admin.layouts.app')

@section('title', 'Email Campaigns')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">Email Campaigns</h1>
            <a href="{{ route('admin.campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-action text-primary font-medium rounded-lg hover:bg-action/90 transition-colors">
                <i class="fas fa-plus"></i> Create Campaign
            </a>
        </div>

        <!-- Campaign Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-blue-600 text-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-2xl font-bold mb-1" id="totalUsers">-</h4>
                            <p class="text-sm opacity-75">Total Users</p>
                        </div>
                        <i class="fas fa-users text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="bg-green-600 text-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-2xl font-bold mb-1" id="marketingOptIn">-</h4>
                            <p class="text-sm opacity-75">Marketing Opt-in</p>
                        </div>
                        <i class="fas fa-check-circle text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-600 text-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-2xl font-bold mb-1" id="marketingOptOut">-</h4>
                            <p class="text-sm opacity-75">Marketing Opt-out</p>
                        </div>
                        <i class="fas fa-ban text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-600 text-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-2xl font-bold mb-1" id="noPreference">-</h4>
                            <p class="text-sm opacity-75">No Preference</p>
                        </div>
                        <i class="fas fa-question-circle text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Campaign Activity -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-medium text-gray-900">Recent Campaign Activity</h5>
            </div>
            <div class="p-6">
                @if($recentBulkEmails->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emails Sent</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentBulkEmails as $activity)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">{{ $activity->template_name }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($activity->created_at)->format('M d, Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $activity->count }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.email-logs.index', ['template' => $activity->template_name]) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-transparent border border-action text-action text-sm font-medium rounded hover:bg-action hover:text-primary transition-colors">
                                                <i class="fas fa-eye"></i> View Logs
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($recentBulkEmails->hasPages())
                        <div class="flex justify-center mt-6">
                            {{ $recentBulkEmails->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-bullhorn text-5xl text-gray-400 mb-4"></i>
                        <h4 class="text-xl font-medium text-gray-900 mb-2">No Campaign Activity Yet</h4>
                        <p class="text-gray-500 mb-6">Create your first email campaign to get started.</p>
                        <a href="{{ route('admin.campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-action text-primary font-medium rounded-lg hover:bg-action/90 transition-colors">
                            <i class="fas fa-plus"></i> Create Campaign
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Campaign Actions -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-medium text-gray-900">Quick Campaign Actions</h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-6">
                        <div class="bg-white border border-primary rounded-lg shadow-sm">
                            <div class="p-6 text-center">
                                <i class="fas fa-envelope-open-text text-4xl text-primary mb-4"></i>
                                <h5 class="text-lg font-medium text-gray-900 mb-2">Welcome Series</h5>
                                <p class="text-gray-500 mb-4">Send welcome emails to new users</p>
                                <a href="{{ route('admin.campaigns.create') }}?type=welcome" class="inline-flex items-center px-4 py-2 bg-action text-primary font-medium rounded-lg hover:bg-action/90 transition-colors">
                                    Create Welcome Campaign
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-6">
                        <div class="bg-white border border-green-600 rounded-lg shadow-sm">
                            <div class="p-6 text-center">
                                <i class="fas fa-bullhorn text-4xl text-green-600 mb-4"></i>
                                <h5 class="text-lg font-medium text-gray-900 mb-2">Newsletter</h5>
                                <p class="text-gray-500 mb-4">Send updates and announcements</p>
                                <a href="{{ route('admin.campaigns.create') }}?type=newsletter" class="inline-flex items-center px-4 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition-colors">
                                    Create Newsletter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Language Distribution -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-medium text-gray-900">User Language Distribution</h5>
            </div>
            <div class="p-6">
                <div id="languageChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Load user statistics
document.addEventListener('DOMContentLoaded', function() {
    loadUserStats();
    loadLanguageChart();
});

function loadUserStats() {
    fetch('{{ route("admin.campaigns.user-stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalUsers').textContent = data.total_users;
            document.getElementById('marketingOptIn').textContent = data.marketing_opt_in;
            document.getElementById('marketingOptOut').textContent = data.marketing_opt_out;
            document.getElementById('noPreference').textContent = data.no_preference;
        })
        .catch(error => {
            console.error('Error loading user stats:', error);
        });
}

function loadLanguageChart() {
    fetch('{{ route("admin.campaigns.user-stats") }}')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('languageChart').getContext('2d');
            
            const labels = Object.keys(data.by_language);
            const values = Object.values(data.by_language);
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels.map(lang => lang.toUpperCase()),
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d',
                            '#17a2b8', '#fd7e14', '#6f42c1', '#e83e8c', '#20c997'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading language chart:', error);
        });
}
</script>
@endpush
