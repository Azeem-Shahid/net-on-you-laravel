<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityPolicy;
use App\Models\SensitiveChangesLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SecurityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show security policies index page
     */
    public function index()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('security.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $policies = SecurityPolicy::orderBy('policy_name')->get();
        
        // Get default policies if none exist
        if ($policies->isEmpty()) {
            $this->createDefaultPolicies();
            $policies = SecurityPolicy::orderBy('policy_name')->get();
        }

        return view('admin.security.index', compact('policies'));
    }

    /**
     * Update a security policy
     */
    public function update(Request $request, $policyName)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('security.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:191',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $policy = SecurityPolicy::where('policy_name', $policyName)->first();
        if (!$policy) {
            return response()->json([
                'success' => false,
                'message' => 'Policy not found'
            ], 404);
        }

        $oldValue = $policy->policy_value;
        $newValue = $request->input('value');

        // Update policy
        $policy->update([
            'policy_value' => $newValue,
            'updated_by_admin_id' => auth('admin')->id(),
        ]);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'update_security_policy',
            $policyName,
            $oldValue,
            $newValue,
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'update_security_policy',
            'security_policy',
            $policyName,
            [
                'policy_name' => $policyName,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Security policy updated successfully',
            'policy' => $policy->fresh()
        ]);
    }

    /**
     * Update multiple security policies
     */
    public function updateMultiple(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('security.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'policies' => 'required|array',
            'policies.*.name' => 'required|string',
            'policies.*.value' => 'required|string|max:191',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $adminId = auth('admin')->id();
        $policies = $request->input('policies');
        $reason = $request->input('reason');

        foreach ($policies as $policyData) {
            $policy = SecurityPolicy::where('policy_name', $policyData['name'])->first();
            if ($policy) {
                $oldValue = $policy->policy_value;
                $newValue = $policyData['value'];

                // Update policy
                $policy->update([
                    'policy_value' => $newValue,
                    'updated_by_admin_id' => $adminId,
                ]);

                // Log sensitive change
                SensitiveChangesLog::log(
                    $adminId,
                    'update_security_policy',
                    $policyData['name'],
                    $oldValue,
                    $newValue,
                    $request->ip()
                );
            }
        }

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            $adminId,
            'update_multiple_security_policies',
            'security_policies',
            null,
            [
                'count' => count($policies),
                'reason' => $reason,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Security policies updated successfully'
        ]);
    }

    /**
     * Get policy value
     */
    public function getPolicyValue($policyName)
    {
        $value = SecurityPolicy::getPolicyValue($policyName);
        return response()->json(['value' => $value]);
    }

    /**
     * Create default security policies
     */
    private function createDefaultPolicies()
    {
        $defaultPolicies = [
            'password_min_length' => [
                'value' => '8',
                'description' => 'Minimum password length required'
            ],
            'password_require_uppercase' => [
                'value' => 'true',
                'description' => 'Require at least one uppercase letter in password'
            ],
            'password_require_lowercase' => [
                'value' => 'true',
                'description' => 'Require at least one lowercase letter in password'
            ],
            'password_require_numbers' => [
                'value' => 'true',
                'description' => 'Require at least one number in password'
            ],
            'password_require_symbols' => [
                'value' => 'false',
                'description' => 'Require at least one special character in password'
            ],
            'session_timeout_minutes' => [
                'value' => '120',
                'description' => 'Session timeout in minutes'
            ],
            'max_sessions_per_user' => [
                'value' => '5',
                'description' => 'Maximum concurrent sessions per user'
            ],
            'require_2fa' => [
                'value' => 'false',
                'description' => 'Require two-factor authentication for admin accounts'
            ],
            'max_login_attempts' => [
                'value' => '5',
                'description' => 'Maximum failed login attempts before lockout'
            ],
            'lockout_duration_minutes' => [
                'value' => '30',
                'description' => 'Account lockout duration in minutes'
            ],
            'maintenance_mode' => [
                'value' => 'false',
                'description' => 'Enable maintenance mode'
            ],
            'maintenance_message' => [
                'value' => 'Site is under maintenance. Please try again later.',
                'description' => 'Message displayed during maintenance mode'
            ],
        ];

        foreach ($defaultPolicies as $name => $data) {
            SecurityPolicy::create([
                'policy_name' => $name,
                'policy_value' => $data['value'],
                'description' => $data['description'],
                'updated_by_admin_id' => auth('admin')->id() ?? 1,
            ]);
        }
    }
}

