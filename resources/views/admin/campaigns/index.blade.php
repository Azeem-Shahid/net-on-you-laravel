@extends('admin.layouts.app')

@section('title', 'Email Campaigns')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Email Campaigns</h1>
                <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Campaign
                </a>
            </div>

            <!-- Campaign Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="totalUsers">-</h4>
                                    <small>Total Users</small>
                                </div>
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="marketingOptIn">-</h4>
                                    <small>Marketing Opt-in</small>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="marketingOptOut">-</h4>
                                    <small>Marketing Opt-out</small>
                                </div>
                                <i class="fas fa-ban fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="noPreference">-</h4>
                                    <small>No Preference</small>
                                </div>
                                <i class="fas fa-question-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Campaign Activity -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Campaign Activity</h5>
                </div>
                <div class="card-body">
                    @if($recentBulkEmails->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Template</th>
                                        <th>Date</th>
                                        <th>Emails Sent</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBulkEmails as $activity)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $activity->template_name }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('M d, Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ $activity->count }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.email-logs.index', ['template' => $activity->template_name]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
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
                            <div class="d-flex justify-content-center mt-4">
                                {{ $recentBulkEmails->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <h4>No Campaign Activity Yet</h4>
                            <p class="text-muted">Create your first email campaign to get started.</p>
                            <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Campaign
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Campaign Actions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Campaign Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-envelope-open-text fa-2x text-primary mb-3"></i>
                                    <h5>Welcome Series</h5>
                                    <p class="text-muted">Send welcome emails to new users</p>
                                    <a href="{{ route('admin.campaigns.create') }}?type=welcome" class="btn btn-primary">
                                        Create Welcome Campaign
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-bullhorn fa-2x text-success mb-3"></i>
                                    <h5>Newsletter</h5>
                                    <p class="text-muted">Send updates and announcements</p>
                                    <a href="{{ route('admin.campaigns.create') }}?type=newsletter" class="btn btn-success">
                                        Create Newsletter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Language Distribution -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">User Language Distribution</h5>
                </div>
                <div class="card-body">
                    <div id="languageChart" style="height: 300px;"></div>
                </div>
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
