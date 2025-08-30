@extends('admin.layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">System Settings</h1>
            <p class="mt-2 text-sm text-gray-700">Manage global application settings and configurations.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <button type="button" id="clear-cache-btn" class="block rounded-md bg-primary px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary/80 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                Clear Cache
            </button>
        </div>
    </div>

    <!-- Settings Groups -->
    <div class="mt-8 space-y-6">
        <!-- General Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">General Settings</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="site_title" class="block text-sm font-medium text-gray-700">Site Title</label>
                            <input type="text" id="site_title" name="site_title" value="{{ \App\Models\Setting::getValue('site_title', 'NetOnYou') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                   data-setting-key="site_title">
                            <p class="mt-1 text-xs text-gray-500">The main title displayed on the website</p>
                        </div>
                        <div>
                            <label for="default_currency" class="block text-sm font-medium text-gray-700">Default Currency</label>
                            <select id="default_currency" name="default_currency" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-setting-key="default_currency">
                                <option value="USD" {{ \App\Models\Setting::getValue('default_currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ \App\Models\Setting::getValue('default_currency', 'USD') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ \App\Models\Setting::getValue('default_currency', 'USD') == 'GBP' ? 'selected' : '' }}>GBP</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Default currency for transactions</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="default_language" class="block text-sm font-medium text-gray-700">Default Language</label>
                            <select id="default_language" name="default_language" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-setting-key="default_language">
                                <option value="en" {{ \App\Models\Setting::getValue('default_language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ \App\Models\Setting::getValue('default_language', 'en') == 'es' ? 'selected' : '' }}>Spanish</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Default language for new users</p>
                        </div>
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                            <select id="timezone" name="timezone" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-setting-key="timezone">
                                <option value="UTC" {{ \App\Models\Setting::getValue('timezone', 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ \App\Models\Setting::getValue('timezone', 'UTC') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="Europe/London" {{ \App\Models\Setting::getValue('timezone', 'UTC') == 'Europe/London' ? 'selected' : '' }}>London</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Default timezone for the application</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Payment Settings</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="payment_gateway" class="block text-sm font-medium text-gray-700">Payment Gateway</label>
                            <select id="payment_gateway" name="payment_gateway" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-setting-key="payment_gateway">
                                <option value="stripe" {{ \App\Models\Setting::getValue('payment_gateway', 'stripe') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="paypal" {{ \App\Models\Setting::getValue('payment_gateway', 'stripe') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="coinpayments" {{ \App\Models\Setting::getValue('payment_gateway', 'stripe') == 'coinpayments' ? 'selected' : '' }}>CoinPayments (Crypto)</option>
                                <option value="manual" {{ \App\Models\Setting::getValue('payment_gateway', 'stripe') == 'manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Primary payment processing method</p>
                        </div>
                        <div>
                            <label for="subscription_price" class="block text-sm font-medium text-gray-700">Subscription Price</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" id="subscription_price" name="subscription_price" 
                                       value="{{ \App\Models\Setting::getValue('subscription_price', '9.99') }}" 
                                       step="0.01" min="0"
                                       class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                       data-setting-key="subscription_price">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Monthly subscription price in USD</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CoinPayments Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">CoinPayments Configuration</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="coinpayments_enabled" class="block text-sm font-medium text-gray-700">Enable CoinPayments</label>
                            <select id="coinpayments_enabled" name="coinpayments_enabled" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-setting-key="coinpayments_enabled">
                                <option value="1" {{ \App\Models\Setting::getValue('coinpayments_enabled', '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ \App\Models\Setting::getValue('coinpayments_enabled', '0') == '0' ? 'selected' : '' }}>Disabled</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Enable cryptocurrency payments via CoinPayments</p>
                        </div>
                        <div>
                            <label for="coinpayments_currency" class="block text-sm font-medium text-gray-700">Default Crypto Currency</label>
                            <select id="coinpayments_currency" name="coinpayments_currency" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-setting-key="coinpayments_currency">
                                <option value="USDT.TRC20" {{ \App\Models\Setting::getValue('coinpayments_currency', 'USDT.TRC20') == 'USDT.TRC20' ? 'selected' : '' }}>USDT (TRC20)</option>
                                <option value="USDT.ERC20" {{ \App\Models\Setting::getValue('coinpayments_currency', 'USDT.TRC20') == 'USDT.ERC20' ? 'selected' : '' }}>USDT (ERC20)</option>
                                <option value="BTC" {{ \App\Models\Setting::getValue('coinpayments_currency', 'USDT.TRC20') == 'BTC' ? 'selected' : '' }}>Bitcoin</option>
                                <option value="ETH" {{ \App\Models\Setting::getValue('coinpayments_currency', 'USDT.TRC20') == 'ETH' ? 'selected' : '' }}>Ethereum</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Default cryptocurrency for payments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Email Settings</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="mail_from_address" class="block text-sm font-medium text-gray-700">From Email Address</label>
                            <input type="email" id="mail_from_address" name="mail_from_address" 
                                   value="{{ \App\Models\Setting::getValue('mail_from_address', 'noreply@netonyou.com') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                   data-setting-key="mail_from_address">
                            <p class="mt-1 text-xs text-gray-500">Default sender email address</p>
                        </div>
                        <div>
                            <label for="mail_from_name" class="block text-sm font-medium text-gray-700">From Name</label>
                            <input type="text" id="mail_from_name" name="mail_from_name" 
                                   value="{{ \App\Models\Setting::getValue('mail_from_name', 'NetOnYou') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                   data-setting-key="mail_from_name">
                            <p class="mt-1 text-xs text-gray-500">Default sender name</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="button" id="save-settings-btn" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/80 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                Save All Settings
            </button>
        </div>
    </div>
</div>

<!-- Reason Modal -->
<div id="reason-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Reason for Change
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Please provide a reason for this change. This will be logged for audit purposes.
                        </p>
                        <textarea id="change-reason" rows="3" class="mt-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" placeholder="Enter reason for change..."></textarea>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button type="button" id="confirm-change-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:col-start-2 sm:text-sm">
                    Confirm Change
                </button>
                <button type="button" id="cancel-change-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSettingKey = null;
    let currentSettingValue = null;

    // Handle setting changes
    document.querySelectorAll('[data-setting-key]').forEach(element => {
        element.addEventListener('change', function() {
            currentSettingKey = this.dataset.settingKey;
            currentSettingValue = this.value;
            showReasonModal();
        });
    });

    // Save all settings
    document.getElementById('save-settings-btn').addEventListener('click', function() {
        const settings = [];
        document.querySelectorAll('[data-setting-key]').forEach(element => {
            settings.push({
                key: element.dataset.settingKey,
                value: element.value
            });
        });
        
        currentSettingKey = 'multiple';
        currentSettingValue = settings;
        showReasonModal();
    });

    // Clear cache
    document.getElementById('clear-cache-btn').addEventListener('click', function() {
        fetch('{{ route("admin.settings.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cache cleared successfully', 'success');
            } else {
                showNotification('Failed to clear cache', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to clear cache', 'error');
        });
    });

    // Show reason modal
    function showReasonModal() {
        document.getElementById('reason-modal').classList.remove('hidden');
        document.getElementById('change-reason').focus();
    }

    // Hide reason modal
    function hideReasonModal() {
        document.getElementById('reason-modal').classList.add('hidden');
        document.getElementById('change-reason').value = '';
        currentSettingKey = null;
        currentSettingValue = null;
    }

    // Confirm change
    document.getElementById('confirm-change-btn').addEventListener('click', function() {
        const reason = document.getElementById('change-reason').value.trim();
        if (!reason) {
            alert('Please provide a reason for the change.');
            return;
        }

        if (currentSettingKey === 'multiple') {
            updateMultipleSettings(currentSettingValue, reason);
        } else {
            updateSetting(currentSettingKey, currentSettingValue, reason);
        }

        hideReasonModal();
    });

    // Cancel change
    document.getElementById('cancel-change-btn').addEventListener('click', hideReasonModal);

    // Update single setting
    function updateSetting(key, value, reason) {
        fetch(`{{ route('admin.settings.index') }}/${key}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ value, reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Setting updated successfully', 'success');
            } else {
                showNotification(data.message || 'Failed to update setting', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to update setting', 'error');
        });
    }

    // Update multiple settings
    function updateMultipleSettings(settings, reason) {
        fetch('{{ route("admin.settings.update-multiple") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ settings, reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Settings updated successfully', 'success');
            } else {
                showNotification(data.message || 'Failed to update settings', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to update settings', 'error');
        });
    }

    // Show notification
    function showNotification(message, type) {
        // You can implement a proper notification system here
        alert(message);
    }
});
</script>
@endpush

