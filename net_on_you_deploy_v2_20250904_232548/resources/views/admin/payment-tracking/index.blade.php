@extends('admin.layouts.app')

@section('title', 'Payment Tracking')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Payment Tracking Dashboard</h3>
            </div>
            <div class="p-6">
                <!-- Payment Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-green-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $completedPayments }}</h4>
                                    <p class="text-sm opacity-75">Completed</p>
                                </div>
                                <i class="fas fa-check-circle text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $pendingReview }}</h4>
                                    <p class="text-sm opacity-75">Pending Review</p>
                                </div>
                                <i class="fas fa-clock text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">{{ $failedPayments }}</h4>
                                    <p class="text-sm opacity-75">Failed</p>
                                </div>
                                <i class="fas fa-times-circle text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-600 text-white rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-2xl font-bold mb-1">${{ number_format($totalAmount, 2) }}</h4>
                                    <p class="text-sm opacity-75">Total Amount</p>
                                </div>
                                <i class="fas fa-dollar-sign text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="bg-white rounded-lg shadow-md mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-medium text-gray-900">Recent Payments</h5>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recentPayments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->transaction_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($payment->status == 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                            @elseif($payment->status == 'pending_review')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending Review</span>
                                            @elseif($payment->status == 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($payment->payment_method ?? 'Unknown') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.payment-tracking.show', $payment) }}" class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded hover:bg-blue-200 transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($payment->status == 'pending_review')
                                                    <button type="button" class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded hover:bg-green-200 transition-colors" onclick="markAsReviewed({{ $payment->id }})">
                                                        <i class="fas fa-check"></i> Mark Reviewed
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No recent payments found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pending Review -->
                @if($pendingReview > 0)
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-medium text-gray-900">Payments Pending Review</h5>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingPayments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->transaction_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($payment->payment_method ?? 'Unknown') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.payment-tracking.show', $payment) }}" class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded hover:bg-blue-200 transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded hover:bg-green-200 transition-colors" onclick="markAsReviewed({{ $payment->id }})">
                                                    <i class="fas fa-check"></i> Mark Reviewed
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function markAsReviewed(paymentId) {
    if (confirm('Mark this payment as reviewed? This will process referral commissions.')) {
        fetch(`/admin/payment-tracking/${paymentId}/mark-reviewed`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment marked as reviewed successfully!');
                location.reload();
            } else {
                alert('Failed to mark payment as reviewed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the payment.');
        });
    }
}
</script>
@endsection

