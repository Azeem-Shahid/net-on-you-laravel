<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Models\User;
use App\Models\Admin;
use App\Services\EmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class EmailModuleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin'
        ]);

        // Create regular user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function admin_can_access_email_templates_page()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/email-templates');

        $response->assertStatus(200);
        $response->assertSee('Email Templates');
    }

    /** @test */
    public function admin_can_create_email_template()
    {
        $templateData = [
            'name' => 'test_template',
            'language' => 'en',
            'subject' => 'Test Subject {name}',
            'body' => 'Hello {name}, this is a test email.',
            'variables' => ['name']
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/email-templates', $templateData);

        $response->assertRedirect('/admin/email-templates');
        
        $this->assertDatabaseHas('email_templates', [
            'name' => 'test_template',
            'language' => 'en'
        ]);
    }

    /** @test */
    public function admin_can_edit_email_template()
    {
        $template = EmailTemplate::create([
            'name' => 'test_template',
            'language' => 'en',
            'subject' => 'Original Subject',
            'body' => 'Original body',
            'variables' => [],
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $updateData = [
            'name' => 'test_template',
            'language' => 'en',
            'subject' => 'Updated Subject',
            'body' => 'Updated body',
            'variables' => ['name']
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put("/admin/email-templates/{$template->id}", $updateData);

        $response->assertRedirect('/admin/email-templates');
        
        $this->assertDatabaseHas('email_templates', [
            'id' => $template->id,
            'subject' => 'Updated Subject'
        ]);
    }

    /** @test */
    public function admin_can_delete_email_template()
    {
        $template = EmailTemplate::create([
            'name' => 'test_template',
            'language' => 'en',
            'subject' => 'Test Subject',
            'body' => 'Test body',
            'variables' => [],
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete("/admin/email-templates/{$template->id}");

        $response->assertRedirect('/admin/email-templates');
        
        $this->assertDatabaseMissing('email_templates', [
            'id' => $template->id
        ]);
    }

    /** @test */
    public function admin_can_access_email_logs_page()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/email-logs');

        $response->assertStatus(200);
        $response->assertSee('Email Logs');
    }

    /** @test */
    public function admin_can_access_campaigns_page()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/campaigns');

        $response->assertStatus(200);
        $response->assertSee('Email Campaigns');
    }

    /** @test */
    public function email_service_can_send_welcome_email()
    {
        // Create a welcome email template
        $template = EmailTemplate::create([
            'name' => 'welcome_email',
            'language' => 'en',
            'subject' => 'Welcome {name}!',
            'body' => 'Hello {name}, welcome to our platform!',
            'variables' => ['name'],
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $emailService = app(EmailService::class);
        $result = $emailService->sendWelcomeEmail($this->user->id);

        $this->assertTrue($result);
        
        // Check that email log was created
        $this->assertDatabaseHas('email_logs', [
            'template_name' => 'welcome_email',
            'user_id' => $this->user->id,
            'email' => $this->user->email
        ]);
    }

    /** @test */
    public function email_service_can_send_password_reset_email()
    {
        // Create a password reset template
        $template = EmailTemplate::create([
            'name' => 'password_reset',
            'language' => 'en',
            'subject' => 'Reset your password',
            'body' => 'Click here to reset: {reset_link}',
            'variables' => ['name', 'reset_link'],
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $emailService = app(EmailService::class);
        $result = $emailService->sendPasswordResetEmail($this->user->id, 'test-token');

        $this->assertTrue($result);
        
        // Check that email log was created
        $this->assertDatabaseHas('email_logs', [
            'template_name' => 'password_reset',
            'user_id' => $this->user->id,
            'email' => $this->user->email
        ]);
    }

    /** @test */
    public function template_variables_are_replaced_correctly()
    {
        $template = EmailTemplate::create([
            'name' => 'test_template',
            'language' => 'en',
            'subject' => 'Hello {name}!',
            'body' => 'Welcome {name}, your plan is {plan}.',
            'variables' => ['name', 'plan'],
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $data = [
            'name' => 'John Doe',
            'plan' => 'Premium'
        ];

        $result = $template->replaceVariables($data);

        $this->assertEquals('Hello John Doe!', $result['subject']);
        $this->assertEquals('Welcome John Doe, your plan is Premium.', $result['body']);
    }

    /** @test */
    public function non_admin_cannot_access_email_management()
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/email-templates');

        $response->assertStatus(302); // Redirect to login
    }

    /** @test */
    public function guest_cannot_access_email_management()
    {
        $response = $this->get('/admin/email-templates');

        $response->assertStatus(302); // Redirect to login
    }
}
