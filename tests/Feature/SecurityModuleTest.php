<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\SecurityPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a super admin user
        $this->admin = Admin::factory()->create([
            'role' => 'super_admin',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_can_access_settings_page()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/settings');

        $response->assertStatus(200);
        $response->assertSee('System Settings');
    }

    /** @test */
    public function admin_can_access_security_page()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/security');

        $response->assertStatus(200);
        $response->assertSee('Security Policies');
    }

    /** @test */
    public function admin_cannot_access_settings_without_permission()
    {
        // Create admin without settings permission
        $admin = Admin::factory()->create([
            'role' => 'editor',
            'status' => 'active'
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/settings');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_security_without_permission()
    {
        // Create admin without security permission
        $admin = Admin::factory()->create([
            'role' => 'editor',
            'status' => 'active'
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/security');

        $response->assertStatus(403);
    }

    /** @test */
    public function can_update_setting_with_reason()
    {
        // Create a test setting
        $setting = Setting::create([
            'key' => 'test_setting',
            'value' => 'old_value',
            'type' => 'string',
            'description' => 'Test setting',
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put("/admin/settings/{$setting->key}", [
                'value' => 'new_value',
                'reason' => 'Testing the update functionality'
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify setting was updated
        $this->assertDatabaseHas('settings', [
            'key' => 'test_setting',
            'value' => 'new_value',
            'updated_by_admin_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function can_update_security_policy_with_reason()
    {
        // Create a test policy
        $policy = SecurityPolicy::create([
            'policy_name' => 'test_policy',
            'policy_value' => 'old_value',
            'description' => 'Test policy',
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put("/admin/security/{$policy->policy_name}", [
                'value' => 'new_value',
                'reason' => 'Testing the policy update functionality'
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify policy was updated
        $this->assertDatabaseHas('security_policies', [
            'policy_name' => 'test_policy',
            'policy_value' => 'new_value',
            'updated_by_admin_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function setting_update_requires_reason()
    {
        $setting = Setting::create([
            'key' => 'test_setting',
            'value' => 'old_value',
            'type' => 'string',
            'description' => 'Test setting',
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put("/admin/settings/{$setting->key}", [
                'value' => 'new_value'
                // Missing reason
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reason']);
    }

    /** @test */
    public function security_policy_update_requires_reason()
    {
        $policy = SecurityPolicy::create([
            'policy_name' => 'test_policy',
            'policy_value' => 'old_value',
            'description' => 'Test policy',
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put("/admin/security/{$policy->policy_name}", [
                'value' => 'new_value'
                // Missing reason
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reason']);
    }

    /** @test */
    public function super_admin_has_all_permissions()
    {
        $this->assertTrue($this->admin->hasPermission('settings.manage'));
        $this->assertTrue($this->admin->hasPermission('security.manage'));
        $this->assertTrue($this->admin->hasPermission('roles.manage'));
        $this->assertTrue($this->admin->hasPermission('api_keys.manage'));
        $this->assertTrue($this->admin->hasPermission('sessions.manage'));
    }

    /** @test */
    public function editor_has_limited_permissions()
    {
        $editor = Admin::factory()->create([
            'role' => 'editor',
            'status' => 'active'
        ]);

        $this->assertFalse($editor->hasPermission('settings.manage'));
        $this->assertFalse($editor->hasPermission('security.manage'));
        $this->assertFalse($editor->hasPermission('roles.manage'));
        $this->assertFalse($editor->hasPermission('api_keys.manage'));
        $this->assertFalse($editor->hasPermission('sessions.manage'));
        
        $this->assertTrue($editor->hasPermission('users.manage'));
        $this->assertTrue($editor->hasPermission('magazines.manage'));
    }

    /** @test */
    public function accountant_has_financial_permissions()
    {
        $accountant = Admin::factory()->create([
            'role' => 'accountant',
            'status' => 'active'
        ]);

        $this->assertFalse($accountant->hasPermission('settings.manage'));
        $this->assertFalse($accountant->hasPermission('security.manage'));
        
        $this->assertTrue($accountant->hasPermission('transactions.manage'));
        $this->assertTrue($accountant->hasPermission('commissions.manage'));
        $this->assertTrue($accountant->hasPermission('payouts.manage'));
    }
}

