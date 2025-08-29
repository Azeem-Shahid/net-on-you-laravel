<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\MagazineEntitlement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load test routes
        require_once __DIR__ . '/../TestRoutes.php';
        
        // Create a test user with verified email
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
            'language' => 'en',
            'wallet_address' => '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
            'subscription_start_date' => now(),
            'subscription_end_date' => now()->addYear(),
            'email_verified_at' => now(),
        ]);
    }



    /** @test */
    public function dashboard_is_accessible_only_after_login()
    {
        // Try to access dashboard without login
        $response = $this->get('/test-dashboard');
        $response->assertRedirect('/login');

        // Login and access dashboard
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /** @test */
    public function dashboard_shows_user_profile_information()
    {
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->email);
        $response->assertSee(strtoupper($this->user->language));
        $response->assertSee($this->user->wallet_address);
    }

    /** @test */
    public function dashboard_shows_subscription_status()
    {
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $response->assertSee('Active');
        $response->assertSee('Expires: ' . $this->user->subscription_end_date->format('M d, Y'));
    }

    /** @test */
    public function dashboard_shows_referral_link()
    {
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $expectedLink = url('/register?ref=' . $this->user->id);
        $response->assertSee($expectedLink);
    }

    /** @test */
    public function dashboard_shows_referral_stats_by_level()
    {
        // Create some referral data
        Referral::create([
            'user_id' => $this->user->id,
            'referred_user_id' => User::create([
                'name' => 'Referred User 1',
                'email' => 'referred1@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ])->id,
            'level' => 1,
        ]);

        Referral::create([
            'user_id' => $this->user->id,
            'referred_user_id' => User::create([
                'name' => 'Referred User 2',
                'email' => 'referred2@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ])->id,
            'level' => 2,
        ]);

        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $response->assertSee('1'); // Level 1 count
        $response->assertSee('1'); // Level 2 count
        $response->assertSee('0'); // Other levels should show 0
    }

    /** @test */
    public function dashboard_shows_commission_earnings()
    {
        // Create commission data
        Commission::create([
            'earner_user_id' => $this->user->id,
            'source_user_id' => User::create([
                'name' => 'Source User',
                'email' => 'source@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ])->id,
            'transaction_id' => Transaction::create([
                'user_id' => $this->user->id,
                'amount' => 100.00,
                'currency' => 'USDT',
                'gateway' => 'Stripe',
                'transaction_hash' => '0x1234567890abcdef',
                'status' => 'completed',
            ])->id,
            'level' => 1,
            'amount' => 25.00,
            'month' => now()->format('Y-m'),
            'eligibility' => 'eligible',
            'payout_status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $response->assertSee('$25.00'); // Monthly earnings
        $response->assertSee('$25.00'); // Total earnings
    }

    /** @test */
    public function dashboard_shows_recent_transactions()
    {
        // Create transaction data
        Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 150.00,
            'currency' => 'USDT',
            'gateway' => 'PayPal',
            'transaction_hash' => '0xabcdef1234567890',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $response->assertSee('$150.00');
        $response->assertSee('USDT');
        $response->assertSee('PayPal');
        $response->assertSee('Completed');
    }

    /** @test */
    public function dashboard_shows_magazine_entitlements()
    {
        // Create magazine entitlement
        MagazineEntitlement::create([
            'user_id' => $this->user->id,
            'magazine_id' => 5,
            'granted_at' => now()->subDays(30),
            'reason' => 'active_issue',
        ]);

        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $response->assertSee('Magazine #5');
        $response->assertSee('Active Issue');
        $response->assertSee('Download');
    }

    /** @test */
    public function profile_edit_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get('/test-profile/edit');
        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
    }

    /** @test */
    public function profile_can_be_updated()
    {
        $response = $this->actingAs($this->user)->put('/test-profile/update', [
            'name' => 'Updated Name',
            'wallet_address' => '0xUpdatedWalletAddress',
            'language' => 'es',
        ]);

        $response->assertRedirect('/test-dashboard');
        
        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('0xUpdatedWalletAddress', $this->user->wallet_address);
        $this->assertEquals('es', $this->user->language);
    }

    /** @test */
    public function profile_update_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->put('/test-profile/update', [
            'name' => '',
            'language' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['name', 'language']);
    }

    /** @test */
    public function change_password_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get('/test-profile/change-password');
        $response->assertStatus(200);
        $response->assertViewIs('profile.change-password');
    }

    /** @test */
    public function password_can_be_changed()
    {
        $response = $this->actingAs($this->user)->put('/test-profile/change-password', [
            'current_password' => 'password123',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect('/test-dashboard');
        
        // Verify new password works
        $this->assertTrue(Hash::check('NewPassword123!', $this->user->fresh()->password));
    }

    /** @test */
    public function password_change_validates_current_password()
    {
        $response = $this->actingAs($this->user)->put('/test-profile/change-password', [
            'current_password' => 'wrongpassword',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    /** @test */
    public function password_change_validates_password_confirmation()
    {
        $response = $this->actingAs($this->user)->put('/test-profile/change-password', [
            'current_password' => 'password123',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function dashboard_is_responsive_on_mobile()
    {
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        // Check that mobile-friendly classes are present
        $response->assertSee('grid-cols-1 md:grid-cols-2 lg:grid-cols-4');
        $response->assertSee('overflow-x-auto'); // For tables
    }

    /** @test */
    public function subscription_status_shows_correct_colors()
    {
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        // Active subscription should show green
        $response->assertSee('text-[#00ff00]');
        
        // Test grace period
        $this->user->update(['subscription_end_date' => now()->subDays(3)]);
        
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        $response->assertSee('text-yellow-600'); // Grace period color
        
        // Test inactive
        $this->user->update(['subscription_end_date' => now()->subDays(10)]);
        
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        $response->assertSee('text-[#ff0000]'); // Inactive color
    }

    /** @test */
    public function transaction_status_shows_correct_colors()
    {
        // Create transactions with different statuses
        Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'currency' => 'USDT',
            'gateway' => 'Stripe',
            'transaction_hash' => '0x1234567890abcdef',
            'status' => 'completed',
        ]);

        Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 200.00,
            'currency' => 'USDC',
            'gateway' => 'PayPal',
            'transaction_hash' => '0xabcdef1234567890',
            'status' => 'failed',
        ]);

        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $response->assertSee('text-[#00ff00]'); // Completed color
        $response->assertSee('text-[#ff0000]'); // Failed color
    }

    /** @test */
    public function referral_link_can_be_copied()
    {
        $response = $this->actingAs($this->user)->get('/test-dashboard');
        
        $response->assertSee('copyToClipboard');
        $response->assertSee('Copy');
    }

    /** @test */
    public function dashboard_handles_empty_data_gracefully()
    {
        // Create user with no data
        $emptyUser = User::create([
            'name' => 'Empty User',
            'email' => 'empty@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($emptyUser)->get('/test-dashboard');
        
        $response->assertSee('No transactions found');
        $response->assertSee('No magazine access available');
        $response->assertSee('$0.00'); // Zero earnings
    }
}
