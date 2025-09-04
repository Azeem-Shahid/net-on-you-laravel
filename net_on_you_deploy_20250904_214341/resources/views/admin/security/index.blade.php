@extends('admin.layouts.app')

@section('title', 'Security Policies')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Security Policies</h1>
            <p class="mt-2 text-sm text-gray-700">Configure security policies and authentication settings for the application.</p>
        </div>
    </div>

    <!-- Security Policy Groups -->
    <div class="mt-8 space-y-6">
        <!-- Password Policies -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Password Policies</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password_min_length" class="block text-sm font-medium text-gray-700">Minimum Length</label>
                            <input type="number" id="password_min_length" name="password_min_length" 
                                   value="{{ \App\Models\SecurityPolicy::getPolicyValue('password_min_length', '8') }}" 
                                   min="6" max="32"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                   data-policy-name="password_min_length">
                            <p class="mt-1 text-xs text-gray-500">Minimum password length required (6-32 characters)</p>
                        </div>
                        <div>
                            <label for="password_require_uppercase" class="block text-sm font-medium text-gray-700">Require Uppercase</label>
                            <select id="password_require_uppercase" name="password_require_uppercase" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-policy-name="password_require_uppercase">
                                <option value="true" {{ \App\Models\SecurityPolicy::getPolicyValue('password_require_uppercase', 'true') == 'true' ? 'selected' : '' }}>Yes</option>
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('password_require_uppercase', 'true') == 'false' ? 'selected' : '' }}>No</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Require at least one uppercase letter</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password_require_lowercase" class="block text-sm font-medium text-gray-700">Require Lowercase</label>
                            <select id="password_require_lowercase" name="password_require_lowercase" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-policy-name="password_require_lowercase">
                                <option value="true" {{ \App\Models\SecurityPolicy::getPolicyValue('password_require_lowercase', 'true') == 'true' ? 'selected' : '' }}>Yes</option>
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('password_require_lowercase', 'true') == 'false' ? 'selected' : '' }}>No</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Require at least one lowercase letter</p>
                        </div>
                        <div>
                            <label for="password_require_numbers" class="block text-sm font-medium text-gray-700">Require Numbers</label>
                            <select id="password_require_numbers" name="password_require_numbers" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-policy-name="password_require_numbers">
                                <option value="true" {{ \App\Models\SecurityPolicy::getPolicyValue('password_require_numbers', 'true') == 'true' ? 'selected' : '' }}>Yes</option>
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('password_require_numbers', 'true') == 'false' ? 'selected' : '' }}>No</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Require at least one number</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password_require_symbols" class="block text-sm font-medium text-gray-700">Require Symbols</label>
                            <select id="password_require_symbols" name="password_require_symbols" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-policy-name="password_require_symbols">
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('password_require_symbols', 'false') == 'true' ? 'selected' : '' }}>Yes</option>
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('password_require_symbols', 'false') == 'false' ? 'selected' : '' }}>No</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Require at least one special character</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Policies -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Session Policies</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="session_timeout_minutes" class="block text-sm font-medium text-gray-700">Session Timeout</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="session_timeout_minutes" name="session_timeout_minutes" 
                                       value="{{ \App\Models\SecurityPolicy::getPolicyValue('session_timeout_minutes', '120') }}" 
                                       min="15" max="1440"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                       data-policy-name="session_timeout_minutes">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">minutes</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Session timeout in minutes (15-1440)</p>
                        </div>
                        <div>
                            <label for="max_sessions_per_user" class="block text-sm font-medium text-gray-700">Max Sessions per User</label>
                            <input type="number" id="max_sessions_per_user" name="max_sessions_per_user" 
                                   value="{{ \App\Models\SecurityPolicy::getPolicyValue('max_sessions_per_user', '5') }}" 
                                   min="1" max="20"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                   data-policy-name="max_sessions_per_user">
                            <p class="mt-1 text-xs text-gray-500">Maximum concurrent sessions per user (1-20)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authentication Policies -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Authentication Policies</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="require_2fa" class="block text-sm font-medium text-gray-700">Require 2FA</label>
                            <select id="require_2fa" name="require_2fa" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-policy-name="require_2fa">
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('require_2fa', 'false') == 'true' ? 'selected' : '' }}>Yes</option>
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('require_2fa', 'false') == 'false' ? 'selected' : '' }}>No</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Require two-factor authentication for admin accounts</p>
                        </div>
                        <div>
                            <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">Max Login Attempts</label>
                            <input type="number" id="max_login_attempts" name="max_login_attempts" 
                                   value="{{ \App\Models\SecurityPolicy::getPolicyValue('max_login_attempts', '5') }}" 
                                   min="3" max="10"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                   data-policy-name="max_login_attempts">
                            <p class="mt-1 text-xs text-gray-500">Maximum failed login attempts before lockout (3-10)</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="lockout_duration_minutes" class="block text-sm font-medium text-gray-700">Lockout Duration</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="lockout_duration_minutes" name="lockout_duration_minutes" 
                                       value="{{ \App\Models\SecurityPolicy::getPolicyValue('lockout_duration_minutes', '30') }}" 
                                       min="5" max="1440"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                       data-policy-name="lockout_duration_minutes">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">minutes</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Account lockout duration in minutes (5-1440)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Maintenance Mode</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="maintenance_mode" class="block text-sm font-medium text-gray-700">Enable Maintenance Mode</label>
                            <select id="maintenance_mode" name="maintenance_mode" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    data-policy-name="maintenance_mode">
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('maintenance_mode', 'false') == 'true' ? 'selected' : '' }}>Yes</option>
                                <option value="false" {{ \App\Models\SecurityPolicy::getPolicyValue('maintenance_mode', 'false') == 'false' ? 'selected' : '' }}>No</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Enable maintenance mode to block non-admin access</p>
                        </div>
                        <div>
                            <label for="maintenance_message" class="block text-sm font-medium text-gray-700">Maintenance Message</label>
                            <input type="text" id="maintenance_message" name="maintenance_message" 
                                   value="{{ \App\Models\SecurityPolicy::getPolicyValue('maintenance_message', 'Site is under maintenance. Please try again later.') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                   data-policy-name="maintenance_message">
                            <p class="mt-1 text-xs text-gray-500">Message displayed during maintenance mode</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="button" id="save-policies-btn" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/80 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                Save All Policies
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
                            Please provide a reason for this security policy change. This will be logged for audit purposes.
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
    let currentPolicyName = null;
    let currentPolicyValue = null;

    // Handle policy changes
    document.querySelectorAll('[data-policy-name]').forEach(element => {
        element.addEventListener('change', function() {
            currentPolicyName = this.dataset.policyName;
            currentPolicyValue = this.value;
            showReasonModal();
        });
    });

    // Save all policies
    document.getElementById('save-policies-btn').addEventListener('click', function() {
        const policies = [];
        document.querySelectorAll('[data-policy-name]').forEach(element => {
            policies.push({
                name: element.dataset.policyName,
                value: element.value
            });
        });
        
        currentPolicyName = 'multiple';
        currentPolicyValue = policies;
        showReasonModal();
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
        currentPolicyName = null;
        currentPolicyValue = null;
    }

    // Confirm change
    document.getElementById('confirm-change-btn').addEventListener('click', function() {
        const reason = document.getElementById('change-reason').value.trim();
        if (!reason) {
            alert('Please provide a reason for the change.');
            return;
        }

        if (currentPolicyName === 'multiple') {
            updateMultiplePolicies(currentPolicyValue, reason);
        } else {
            updatePolicy(currentPolicyName, currentPolicyValue, reason);
        }

        hideReasonModal();
    });

    // Cancel change
    document.getElementById('cancel-change-btn').addEventListener('click', hideReasonModal);

    // Update single policy
    function updatePolicy(name, value, reason) {
        fetch(`{{ route('admin.security.index') }}/${name}`, {
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
                showNotification('Policy updated successfully', 'success');
            } else {
                showNotification(data.message || 'Failed to update policy', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to update policy', 'error');
        });
    }

    // Update multiple policies
    function updateMultiplePolicies(policies, reason) {
        fetch('{{ route("admin.security.update-multiple") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ policies, reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Policies updated successfully', 'success');
            } else {
                showNotification(data.message || 'Failed to update policies', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to update policies', 'error');
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

