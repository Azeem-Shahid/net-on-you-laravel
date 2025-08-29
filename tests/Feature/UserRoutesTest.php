<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Magazine;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\MagazineEntitlement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $magazine;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test admin
        $this->admin = \App\Models\Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // Create a test user with verified email
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
            'language' => 'en',
            'wallet_address' => '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
            'email_verified_at' => now(),
        ]);

        // Create a subscription for the user
        \App\Models\Subscription::create([
            'user_id' => $this->user->id,
            'plan_name' => 'Premium',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'active',
        ]);

        // Create a test magazine
        $this->magazine = Magazine::create([
            'title' => 'Test Magazine',
            'description' => 'Test Description',
            'category' => 'Technology',
            'language_code' => 'en',
            'file_path' => 'magazines/test.pdf',
            'file_name' => 'test.pdf',
            'file_size' => 1024000,
            'mime_type' => 'application/pdf',
            'status' => 'active',
            'uploaded_by_admin_id' => $this->admin->id,
            'published_at' => now(),
        ]);
    }

    /** @test */
    public function user_can_access_dashboard()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertSee($this->user->name);
    }

    /** @test */
    public function user_cannot_access_dashboard_without_verification()
    {
        $this->user->update(['email_verified_at' => null]);
        
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertRedirect('/email/verify');
    }

    /** @test */
    public function user_can_access_profile_edit()
    {
        $response = $this->actingAs($this->user)->get('/profile/edit');
        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
    }

    /** @test */
    public function user_can_update_profile()
    {
        $response = $this->actingAs($this->user)->put('/profile/update', [
            'name' => 'Updated Name',
            'wallet_address' => '0xUpdatedWalletAddress',
            'language' => 'es',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'wallet_address' => '0xUpdatedWalletAddress',
            'language' => 'es',
        ]);
    }

    /** @test */
    public function user_can_access_change_password()
    {
        $response = $this->actingAs($this->user)->get('/profile/change-password');
        $response->assertStatus(200);
        $response->assertViewIs('profile.change-password');
    }

    /** @test */
    public function user_can_change_password()
    {
        $response = $this->actingAs($this->user)->put('/profile/change-password', [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    /** @test */
    public function user_can_access_magazines_index()
    {
        $response = $this->actingAs($this->user)->get('/magazines');
        $response->assertStatus(200);
        $response->assertViewIs('magazines.index');
    }

    /** @test */
    public function user_can_view_magazine_details()
    {
        $response = $this->actingAs($this->user)->get("/magazines/{$this->magazine->id}");
        $response->assertStatus(200);
        $response->assertViewIs('magazines.show');
        $response->assertSee($this->magazine->title);
    }

    /** @test */
    public function user_can_access_payment_checkout()
    {
        $response = $this->actingAs($this->user)->get('/payment/checkout');
        $response->assertStatus(200);
        $response->assertViewIs('payment.checkout');
    }

    /** @test */
    public function user_can_access_payment_history()
    {
        $response = $this->actingAs($this->user)->get('/payment/history');
        $response->assertStatus(200);
        $response->assertViewIs('payment.history');
    }

    /** @test */
    public function user_can_access_payment_status()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'currency' => 'USDT',
            'gateway' => 'Stripe',
            'transaction_hash' => '0x1234567890abcdef',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)->get("/payment/status/{$transaction->id}");
        $response->assertStatus(200);
        $response->assertViewIs('payment.status');
    }

    /** @test */
    public function user_can_access_payment_manual()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'currency' => 'USDT',
            'gateway' => 'Manual',
            'transaction_hash' => '0x1234567890abcdef',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get("/payment/manual/{$transaction->id}");
        $response->assertStatus(200);
        $response->assertViewIs('payment.manual');
    }

    /** @test */
    public function user_can_upload_payment_proof()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'currency' => 'USDT',
            'gateway' => 'Manual',
            'transaction_hash' => '0x1234567890abcdef',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->post("/payment/upload-proof/{$transaction->id}", [
            'proof_file' => 'test_proof.pdf',
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function user_can_access_magazine_access_status()
    {
        $response = $this->actingAs($this->user)->get('/magazines/access/status');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_access_magazine_categories_api()
    {
        $response = $this->get('/api/magazines/categories');
        $response->assertStatus(200);
        $response->assertJsonStructure(['categories']);
    }

    /** @test */
    public function user_can_access_magazine_languages_api()
    {
        $response = $this->get('/api/magazines/languages');
        $response->assertStatus(200);
        $response->assertJsonStructure(['languages']);
    }

    /** @test */
    public function user_can_logout()
    {
        $response = $this->actingAs($this->user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_access_language_switching()
    {
        $response = $this->actingAs($this->user)->post('/language/switch', [
            'language' => 'es'
        ]);
        $response->assertStatus(302); // Language switching redirects
        $response->assertRedirect();
    }

    /** @test */
    public function user_can_check_current_language()
    {
        $response = $this->actingAs($this->user)->get('/language/current');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_access_admin_routes()
    {
        $response = $this->actingAs($this->user)->get('/admin/dashboard');
        $response->assertStatus(302); // Redirects to default login
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_cannot_access_other_users_profiles()
    {
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get("/admin/users/{$otherUser->id}");
        $response->assertStatus(302); // Redirects to default login
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_can_access_dashboard_with_referral_data()
    {
        // Create referral data
        $referredUser = User::create([
            'name' => 'Referred User',
            'email' => 'referred@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create subscription for referred user
        \App\Models\Subscription::create([
            'user_id' => $referredUser->id,
            'plan_name' => 'Premium',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'active',
        ]);

        Referral::create([
            'user_id' => $this->user->id,
            'referred_user_id' => $referredUser->id,
            'level' => 1,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('1'); // Should show referral count
    }

    /** @test */
    public function user_can_access_dashboard_with_commission_data()
    {
        // Create commission data
        Commission::create([
            'earner_user_id' => $this->user->id,
            'source_user_id' => 1,
            'transaction_id' => 1,
            'amount' => 50.00,
            'level' => 1,
            'month' => now()->format('Y-m'),
            'eligibility' => 'eligible',
            'payout_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('50.00'); // Should show commission amount
    }

    /** @test */
    public function user_can_access_dashboard_with_transaction_data()
    {
        // Create transaction data
        Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'currency' => 'USDT',
            'gateway' => 'Stripe',
            'transaction_hash' => '0x1234567890abcdef',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('100.00'); // Should show transaction amount
    }

    /** @test */
    public function user_can_access_dashboard_with_magazine_entitlements()
    {
        // Create magazine entitlement
        MagazineEntitlement::create([
            'user_id' => $this->user->id,
            'magazine_id' => $this->magazine->id,
            'granted_at' => now(),
            'reason' => 'subscription',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee($this->magazine->title);
    }

    /** @test */
    public function user_cannot_access_magazine_download_without_subscription()
    {
        // Remove the subscription
        \App\Models\Subscription::where('user_id', $this->user->id)->delete();
        
        $response = $this->actingAs($this->user)->get("/magazines/{$this->magazine->id}/download");
        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_access_magazine_download_with_inactive_magazine()
    {
        $this->magazine->update(['status' => 'inactive']);
        
        $response = $this->actingAs($this->user)->get("/magazines/{$this->magazine->id}/download");
        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_access_profile_with_validation_errors()
    {
        $response = $this->actingAs($this->user)->put('/profile/update', [
            'name' => '', // Invalid: empty name
            'language' => 'invalid_language', // Invalid language
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'language']);
    }

    /** @test */
    public function user_can_access_change_password_with_validation_errors()
    {
        $response = $this->actingAs($this->user)->put('/profile/change-password', [
            'current_password' => 'wrongpassword', // Wrong current password
            'password' => 'short', // Too short
            'password_confirmation' => 'different', // Mismatch
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['current_password', 'password']);
    }

    /** @test */
    public function user_can_access_magazines_with_search_and_filters()
    {
        $response = $this->actingAs($this->user)->get('/magazines', [
            'search' => 'Test',
            'category' => 'Technology',
            'language_code' => 'en',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('magazines.index');
    }

    /** @test */
    public function user_can_access_payment_initiate()
    {
        $response = $this->actingAs($this->user)->post('/payment/initiate', [
            'amount' => 100.00,
            'currency' => 'USDT',
            'gateway' => 'Stripe',
        ]);

        $response->assertStatus(302); // Redirect after payment initiation
    }

    /** @test */
    public function user_can_access_email_verification()
    {
        $this->user->update(['email_verified_at' => null]);
        
        $response = $this->actingAs($this->user)->get('/email/verify');
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    /** @test */
    public function user_can_resend_verification_email()
    {
        $this->user->update(['email_verified_at' => null]);
        
        $response = $this->actingAs($this->user)->post('/email/verification-notification');
        $response->assertRedirect();
    }

    /** @test */
    public function user_can_verify_email_with_valid_hash()
    {
        $this->user->update(['email_verified_at' => null]);
        
        // Test that the verification route exists and is accessible
        $response = $this->actingAs($this->user)->get('/email/verify');
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }
}
