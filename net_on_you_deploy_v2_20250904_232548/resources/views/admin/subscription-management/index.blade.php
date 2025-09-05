@extends('admin.layouts.app')

@section('title', 'Subscription Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Subscription Management Dashboard</h3>
                    <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
                        <a href="{{ route('admin.subscription-management.create-admin-user') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/80 transition-colors">
                            <i class="fas fa-user-plus mr-2"></i> Create Admin User
                        </a>
                        <a href="{{ route('admin.subscription-management.expiration-alerts') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Expiration Alerts
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Subscription Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-green-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $activeSubscriptions }}</h4>
                                    <p class="text-sm opacity-75">Active Subscriptions</p>
                                </div>
                                <i class="fas fa-check-circle text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $expiringSubscriptions }}</h4>
                                    <p class="text-sm opacity-75">Expiring Soon</p>
                                </div>
                                <i class="fas fa-clock text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $expiredSubscriptions }}</h4>
                                    <p class="text-sm opacity-75">Expired</p>
                                </div>
                                <i class="fas fa-times-circle text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $totalUsers }}</h4>
                                    <p class="text-sm opacity-75">Total Users</p>
                                </div>
                                <i class="fas fa-users text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Subscriptions -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-8">
                        <div class="bg-white rounded-lg shadow-md">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h5 class="text-lg font-medium text-gray-900">Recent Subscriptions</h5>
                            </div>
                            <div class="p-6">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($recentSubscriptions as $subscription)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subscription->user->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subscription->user->email }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subscription->start_date->format('Y-m-d') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subscription->end_date->format('Y-m-d') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($subscription->is_active)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                                    @elseif($subscription->end_date->isPast())
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Expiring Soon</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('admin.subscriptions.show', $subscription->user) }}" class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded hover:bg-blue-200 transition-colors">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($subscription->end_date->diffInDays(now()) <= 30)
                                                            <button type="button" class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded hover:bg-yellow-200 transition-colors" onclick="sendExpirationNotification({{ $subscription->user->id }})">
                                                                <i class="fas fa-bell"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No recent subscriptions found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="lg:col-span-4">
                        <div class="bg-white rounded-lg shadow-md">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h5 class="text-lg font-medium text-gray-900">Quick Actions</h5>
                            </div>
                            <div class="p-6">
                                <div class="space-y-2">
                                    <a href="{{ route('admin.subscription-management.create-admin-user') }}" class="flex items-center px-4 py-3 text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-user-plus mr-3 text-primary"></i> Create Admin User
                                    </a>
                                    <a href="{{ route('admin.subscription-management.expiration-alerts') }}" class="flex items-center px-4 py-3 text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-600"></i> View Expiration Alerts
                                    </a>
                                    <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center px-4 py-3 text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-list mr-3 text-blue-600"></i> All Subscriptions
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-users mr-3 text-green-600"></i> User Management
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendExpirationNotification(userId) {
    if (confirm('Send expiration notification to this user?')) {
        fetch(`/admin/subscription-management/users/${userId}/send-expiration-notification`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Notification sent successfully!');
                location.reload();
            } else {
                alert('Failed to send notification: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the notification.');
        });
    }
}
</script>
@endsection

