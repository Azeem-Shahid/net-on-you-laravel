<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\Magazine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function non_admin_cannot_access_dashboard()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/dashboard');

        // Non-admin users should be redirected to the default login page
        // since they're not authenticated as admin
        $response->assertRedirect('/login');
    }

    /** @test */
    public function dashboard_shows_correct_statistics()
    {
        // Create test data
        User::create([
            'name' => 'Test User 1',
            'email' => 'user1@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Test User 2',
            'email' => 'user2@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'status' => 'blocked',
        ]);

        Transaction::create([
            'user_id' => 1,
            'amount' => 100.00,
            'currency' => 'USD',
            'gateway' => 'Stripe',
            'transaction_hash' => 'test_hash_1',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('2'); // Total users
        $response->assertSee('1'); // Active users
        $response->assertSee('1'); // Blocked users
        $response->assertSee('100.00'); // Total revenue
    }

    /** @test */
    public function admin_can_access_user_management()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }

    /** @test */
    public function admin_can_access_magazine_management()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/magazines');

        $response->assertStatus(200);
        $response->assertViewIs('admin.magazines.index');
    }

    /** @test */
    public function admin_can_access_transaction_management()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/transactions');

        $response->assertStatus(200);
        $response->assertViewIs('admin.transactions.index');
    }

    /** @test */
    public function admin_can_block_and_unblock_users()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Block user
        $response = $this->actingAs($this->admin, 'admin')
            ->post("/admin/users/{$user->id}/toggle-block");

        $response->assertStatus(200);
        $response->assertJson(['new_status' => 'blocked']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'blocked'
        ]);

        // Unblock user
        $response = $this->actingAs($this->admin, 'admin')
            ->post("/admin/users/{$user->id}/toggle-block");

        $response->assertStatus(200);
        $response->assertJson(['new_status' => 'active']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_activity_is_logged()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'view_dashboard',
            'target_type' => 'dashboard'
        ]);
    }
}
