@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('transaction_details', [], 'common') }}</h1>
                <p class="text-gray-600">{{ t('view_transaction_information', [], 'common') }}</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                {{ t('back_to_transactions', [], 'common') }}
            </a>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header with Status -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ t('transaction', [], 'common') }} #{{ $transaction->transaction_hash ?: $transaction->id }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ $transaction->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>
                <div class="flex items-center">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        @if($transaction->status === 'completed') bg-green-100 text-green-800
                        @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($transaction->status === 'failed') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Transaction Information -->
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('transaction_information', [], 'common') }}</h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ t('transaction_id', [], 'common') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->transaction_hash ?: $transaction->id }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ t('amount', [], 'common') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="text-2xl font-bold text-green-600">${{ number_format($transaction->amount, 2) }}</span>
                                    <span class="text-gray-500 ml-2">{{ strtoupper($transaction->currency) }}</span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ t('gateway', [], 'common') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($transaction->gateway) }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ t('status', [], 'common') }}</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($transaction->status === 'completed') bg-green-100 text-green-800
                                        @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($transaction->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('timeline', [], 'common') }}</h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ t('created_at', [], 'common') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->format('F d, Y g:i A') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ t('updated_at', [], 'common') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $transaction->updated_at->format('F d, Y g:i A') }}</dd>
                            </div>
                            
                            @if($transaction->meta && isset($transaction->meta['processed_at']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ t('processed_at', [], 'common') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($transaction->meta['processed_at'])->format('F d, Y g:i A') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($transaction->notes)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('notes', [], 'common') }}</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700">{{ $transaction->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Meta Information -->
            @if($transaction->meta && count($transaction->meta) > 0)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('additional_information', [], 'common') }}</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($transaction->meta as $key => $value)
                                @if(!in_array($key, ['processed_at']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if(is_array($value))
                                                <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Related Information -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('related_information', [], 'common') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Subscription -->
                    @if($transaction->subscription)
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">{{ t('subscription', [], 'common') }}</h4>
                            <p class="text-sm text-blue-700">
                                {{ t('subscription_active_until', [], 'common') }}: 
                                {{ $transaction->subscription->end_date ? $transaction->subscription->end_date->format('F d, Y') : t('no_expiry', [], 'common') }}
                            </p>
                        </div>
                    @endif

                    <!-- Commissions -->
                    @if($transaction->commissions && $transaction->commissions->count() > 0)
                        <div class="bg-green-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-green-900 mb-2">{{ t('commissions', [], 'common') }}</h4>
                            <p class="text-sm text-green-700">
                                {{ t('total_commissions', [], 'common') }}: ${{ number_format($transaction->commissions->sum('amount'), 2) }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

