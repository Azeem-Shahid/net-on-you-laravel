<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SecurityPolicy;

class SecurityPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'policy_name' => 'password_min_length',
                'policy_value' => '8',
                'description' => 'Minimum password length required',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'password_require_uppercase',
                'policy_value' => 'true',
                'description' => 'Require at least one uppercase letter in password',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'password_require_lowercase',
                'policy_value' => 'true',
                'description' => 'Require at least one lowercase letter in password',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'password_require_numbers',
                'policy_value' => 'true',
                'description' => 'Require at least one number in password',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'password_require_symbols',
                'policy_value' => 'false',
                'description' => 'Require at least one special character in password',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'session_timeout_minutes',
                'policy_value' => '120',
                'description' => 'Session timeout in minutes',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'max_sessions_per_user',
                'policy_value' => '5',
                'description' => 'Maximum concurrent sessions per user',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'require_2fa',
                'policy_value' => 'false',
                'description' => 'Require two-factor authentication for admin accounts',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'max_login_attempts',
                'policy_value' => '5',
                'description' => 'Maximum failed login attempts before lockout',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'lockout_duration_minutes',
                'policy_value' => '30',
                'description' => 'Account lockout duration in minutes',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'maintenance_mode',
                'policy_value' => 'false',
                'description' => 'Enable maintenance mode',
                'updated_by_admin_id' => 1,
            ],
            [
                'policy_name' => 'maintenance_message',
                'policy_value' => 'Site is under maintenance. Please try again later.',
                'description' => 'Message displayed during maintenance mode',
                'updated_by_admin_id' => 1,
            ],
        ];

        foreach ($policies as $policy) {
            SecurityPolicy::updateOrCreate(
                ['policy_name' => $policy['policy_name']],
                $policy
            );
        }
    }
}

