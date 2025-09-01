<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\ScheduledCommand;
use App\Models\CommandLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;

class CommandSchedulerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = Admin::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin'
        ]);

        // Create regular user
        $this->user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password')
        ]);
    }

    /** @test */
    public function admin_can_access_command_scheduler_dashboard()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.command-scheduler.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.command-scheduler.index');
        $response->assertSee('Command Scheduler');
    }

    /** @test */
    public function non_admin_cannot_access_command_scheduler()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.command-scheduler.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_run_command_manually()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.command-scheduler.run-command'), [
                'command' => 'system:cleanup'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Check that command log was created
        $this->assertDatabaseHas('command_logs', [
            'command' => 'system:cleanup',
            'executed_by_admin_id' => $this->admin->id
        ]);

        // Check that scheduled command record was created
        $this->assertDatabaseHas('scheduled_commands', [
            'command' => 'system:cleanup',
            'frequency' => 'manual'
        ]);
    }

    /** @test */
    public function admin_cannot_run_invalid_command()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.command-scheduler.run-command'), [
                'command' => 'invalid:command'
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false
        ]);
    }

    /** @test */
    public function admin_can_schedule_command()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.command-scheduler.schedule-command'), [
                'command' => 'system:health-check',
                'frequency' => 'daily',
                'status' => 'active'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Check that scheduled command was created/updated
        $this->assertDatabaseHas('scheduled_commands', [
            'command' => 'system:health-check',
            'frequency' => 'daily',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_can_view_command_logs()
    {
        // Create some test logs
        CommandLog::factory()->create([
            'command' => 'system:cleanup',
            'status' => 'success',
            'executed_by_admin_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.command-scheduler.logs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);
    }

    /** @test */
    public function admin_can_clear_command_logs()
    {
        // Create some test logs
        CommandLog::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.command-scheduler.clear-logs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Check that logs were cleared
        $this->assertEquals(0, CommandLog::count());
    }

    /** @test */
    public function admin_can_get_command_statistics()
    {
        // Create some test data
        ScheduledCommand::factory()->count(3)->create();
        CommandLog::factory()->count(10)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.command-scheduler.stats'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $data = $response->json('stats');
        $this->assertArrayHasKey('total_commands', $data);
        $this->assertArrayHasKey('total_executions', $data);
    }

    /** @test */
    public function admin_can_run_multiple_commands()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.command-scheduler.run-multiple-commands'), [
                'type' => 'maintenance',
                'commands' => ['system:cleanup', 'system:health-check']
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Check that logs were created for both commands
        $this->assertDatabaseHas('command_logs', [
            'command' => 'system:cleanup'
        ]);
        $this->assertDatabaseHas('command_logs', [
            'command' => 'system:health-check'
        ]);
    }

    /** @test */
    public function admin_can_export_command_logs()
    {
        // Create some test logs
        CommandLog::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.command-scheduler.export-logs'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
        $response->assertHeader('Content-Disposition');
    }

    /** @test */
    public function cron_endpoint_can_execute_commands()
    {
        // Create an active scheduled command
        ScheduledCommand::factory()->create([
            'command' => 'system:cleanup',
            'status' => 'active',
            'next_run_at' => now()->subHour() // Due to run
        ]);

        $response = $this->get('/cron');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Check that command was executed
        $this->assertDatabaseHas('command_logs', [
            'command' => 'system:cleanup',
            'executed_by_admin_id' => null // System execution
        ]);
    }

    /** @test */
    public function cron_maintenance_endpoint_works()
    {
        $response = $this->get('/cron/maintenance');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'type' => 'maintenance'
        ]);
    }

    /** @test */
    public function cron_update_endpoint_works()
    {
        $response = $this->get('/cron/update');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'type' => 'update'
        ]);
    }

    /** @test */
    public function cron_business_endpoint_works()
    {
        $response = $this->get('/cron/business');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'type' => 'business'
        ]);
    }

    /** @test */
    public function cron_specific_command_endpoint_works()
    {
        $response = $this->get('/cron/command/system:cleanup');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'command' => 'system:cleanup'
        ]);
    }

    /** @test */
    public function cron_endpoint_with_secret_verification_works()
    {
        // Set a secret in config
        config(['app.cron_secret' => 'test-secret']);

        $response = $this->get('/cron?secret=test-secret');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);
    }

    /** @test */
    public function cron_endpoint_rejects_invalid_secret()
    {
        // Set a secret in config
        config(['app.cron_secret' => 'test-secret']);

        $response = $this->get('/cron?secret=wrong-secret');

        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Invalid secret'
        ]);
    }

    /** @test */
    public function scheduled_commands_calculate_next_run_correctly()
    {
        $command = ScheduledCommand::factory()->create([
            'frequency' => 'daily',
            'status' => 'active'
        ]);

        $command->calculateNextRun();
        $command->save();

        $this->assertNotNull($command->next_run_at);
        $this->assertTrue($command->next_run_at->isFuture());
    }

    /** @test */
    public function command_logs_have_proper_relationships()
    {
        $log = CommandLog::factory()->create([
            'executed_by_admin_id' => $this->admin->id
        ]);

        $this->assertInstanceOf(Admin::class, $log->executedByAdmin);
        $this->assertEquals($this->admin->id, $log->executedByAdmin->id);
    }

    /** @test */
    public function scheduled_commands_have_proper_relationships()
    {
        $command = ScheduledCommand::factory()->create();
        
        // Create a log for this command
        CommandLog::factory()->create([
            'command' => $command->command
        ]);

        $this->assertInstanceOf(CommandLog::class, $command->latestLog);
    }
}
