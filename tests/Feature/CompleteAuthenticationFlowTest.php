<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EmailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;

class CompleteAuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Fake mail and notifications for testing
        Mail::fake();
        Notification::fake();
    }

    /** @test */
    public function test_complete_user_registration_and_verification_flow()
    {
        // Step 1: User visits registration page
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');

        // Step 2: User submits registration form
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'securepassword123',
            'password_confirmation' => 'securepassword123',
            'language' => 'en',
        ];

        $response = $this->post('/register', $userData);
        
        // Step 3: User should be redirected to email verification
        $response->assertRedirect('/email/verify');
        
        // Step 4: Check user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'role' => 'user',
            'status' => 'active',
        ]);
        
        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at); // Email not verified yet
        
        // Step 5: User should be authenticated but not verified
        $this->assertAuthenticated();
        
        // Step 6: User visits email verification page
        $response = $this->get('/email/verify');
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
        
        // Step 7: User requests email verification
        $response = $this->post('/email/verification-notification');
        $response->assertRedirect();
        
        // Step 8: Simulate email verification (in real app, user clicks link in email)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        
        $response = $this->actingAs($user)->get($verificationUrl);
        $response->assertRedirect('/dashboard');
        
        // Step 9: Check email is now verified
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        
        // Step 10: User can now access dashboard
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /** @test */
    public function test_complete_forgot_password_and_reset_flow()
    {
        // Step 1: Create a verified user
        $user = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => Hash::make('oldpassword123'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Debug: Check if user was created
        $this->assertDatabaseHas('users', [
            'email' => 'jane.smith@example.com',
        ]);

        // Step 2: User visits forgot password page
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');

        // Step 3: User submits forgot password form
        $response = $this->post('/forgot-password', [
            'email' => 'jane.smith@example.com',
        ]);
        
        // Debug: Check response status and content
        if ($response->getStatusCode() !== 302) {
            dump('Response status: ' . $response->getStatusCode());
            dump('Response content: ' . $response->getContent());
        }
        
        // Step 4: Check success message
        $response->assertRedirect();
        $response->assertSessionHas('status');
        
        // Step 5: Check password reset token was created
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'jane.smith@example.com',
        ]);
        
        // Step 6: Get the reset token
        $token = Password::createToken($user);
        
        // Step 7: User visits reset password form
        $response = $this->get('/reset-password/' . $token);
        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        
        // Step 8: User submits new password
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'jane.smith@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        
        // Step 9: User should be redirected to login with success message
        $response->assertRedirect('/login');
        $response->assertSessionHas('status');
        
        // Step 10: User can login with new password
        $response = $this->post('/login', [
            'email' => 'jane.smith@example.com',
            'password' => 'newpassword123',
        ]);
        
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /** @test */
    public function test_user_cannot_access_protected_routes_without_verification()
    {
        // Create unverified user
        $user = User::create([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        // User should be redirected to email verification
        $response = $this->get('/dashboard');
        $response->assertRedirect('/email/verify');
        
        // User should not be able to access other protected routes
        $response = $this->get('/profile/edit');
        $response->assertRedirect('/email/verify');
    }

    /** @test */
    public function test_user_cannot_access_protected_routes_when_blocked()
    {
        // Create blocked user
        $user = User::create([
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'blocked',
            'email_verified_at' => now(),
        ]);

        // User should not be able to login
        $response = $this->post('/login', [
            'email' => 'blocked@example.com',
            'password' => 'password123',
        ]);
        
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_with_optional_fields()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'wallet_address' => '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
            'language' => 'es',
            'referrer_id' => null,
        ];

        $response = $this->post('/register', $userData);
        $response->assertRedirect('/email/verify');
        
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'wallet_address' => '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
            'language' => 'es',
        ]);
    }

    /** @test */
    public function test_duplicate_email_registration_handling()
    {
        // Create first user
        User::create([
            'name' => 'First User',
            'email' => 'duplicate@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Try to register with same email
        $userData = [
            'name' => 'Second User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);
        $response->assertSessionHasErrors('email');
        
        // Check only one user exists
        $this->assertEquals(1, User::where('email', 'duplicate@example.com')->count());
    }

    /** @test */
    public function test_password_validation()
    {
        // Test password too short
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ];

        $response = $this->post('/register', $userData);
        $response->assertSessionHasErrors('password');
        
        // Test password confirmation mismatch
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ];

        $response = $this->post('/register', $userData);
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_logout_clears_session()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);
        
        // User should be authenticated
        $this->assertAuthenticated();
        
        // User should be able to access dashboard
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // User logs out
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        
        // User should no longer be authenticated
        $this->assertGuest();
        
        // User should not be able to access protected routes
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_rate_limiting_on_authentication()
    {
        // Test rate limiting on login
        for ($i = 0; $i < 6; $i++) {
            $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }
        
        // 7th attempt should be rate limited
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        
        $response->assertStatus(429);
    }
}
