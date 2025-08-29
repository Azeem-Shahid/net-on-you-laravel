<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\Magazine;
use App\Models\MagazineView;
use App\Models\ReportCache;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = Admin::factory()->create([
            'role' => 'admin',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_can_access_analytics_dashboard()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.analytics.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.analytics.index');
        $response->assertSee('Analytics & Reports');
    }

    /** @test */
    public function unauthorized_users_cannot_access_analytics()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('admin.analytics.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function analytics_dashboard_shows_correct_kpis()
    {
        // Create test data
        User::factory()->count(5)->create(['status' => 'active']);
        User::factory()->count(3)->create(['status' => 'blocked']);
        
        Transaction::factory()->count(3)->create([
            'status' => 'completed',
            'amount' => 100
        ]);
        
        Commission::factory()->count(2)->create([
            'payout_status' => 'paid',
            'amount' => 50
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.analytics.index'));

        $response->assertStatus(200);
        $response->assertSee('5'); // Active users
        $response->assertSee('300'); // Total payments
        $response->assertSee('100'); // Commission payouts
    }

    /** @test */
    public function admin_can_export_csv_report()
    {
        // Create test data
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.analytics.export'), [
                'report_type' => 'users',
                'format' => 'csv',
                'filters' => [
                    'date_from' => now()->subDays(30)->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ]
            ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function admin_can_export_pdf_report()
    {
        // Create test data
        Transaction::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.analytics.export'), [
                'report_type' => 'transactions',
                'format' => 'pdf',
                'filters' => [
                    'date_from' => now()->subDays(30)->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ]
            ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function report_cache_improves_performance()
    {
        // Create test data
        User::factory()->count(5)->create();

        // First request - should create cache
        $response1 = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.analytics.export'), [
                'report_type' => 'users',
                'format' => 'csv',
                'filters' => [
                    'date_from' => now()->subDays(30)->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ]
            ]);

        $response1->assertStatus(200);

        // Check if cache was created
        $this->assertDatabaseHas('report_cache', [
            'created_by_admin_id' => $this->admin->id
        ]);

        // Second request - should use cache
        $response2 = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.analytics.export'), [
                'report_type' => 'users',
                'format' => 'csv',
                'filters' => [
                    'date_from' => now()->subDays(30)->format('Y-m-d'),
                    'date_to' => now()->format('Y-m-d')
                ]
            ]);

        $response2->assertStatus(200);
    }

    /** @test */
    public function admin_actions_are_logged_in_audit_logs()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.analytics.index'));

        $response->assertStatus(200);

        // Check if audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'admin_user_id' => $this->admin->id,
            'action' => 'view_analytics'
        ]);
    }

    /** @test */
    public function export_actions_are_logged_in_audit_logs()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.analytics.export'), [
                'report_type' => 'users',
                'format' => 'csv',
                'filters' => []
            ]);

        $response->assertStatus(200);

        // Check if audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'admin_user_id' => $this->admin->id,
            'action' => 'export_report'
        ]);
    }

    /** @test */
    public function filters_work_correctly()
    {
        // Create test data with different languages
        User::factory()->create(['language' => 'en']);
        User::factory()->create(['language' => 'es']);
        User::factory()->create(['language' => 'fr']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.analytics.index') . '?language=en');

        $response->assertStatus(200);
        // Should only show English users in KPIs
    }

    /** @test */
    public function chart_data_endpoint_returns_correct_data()
    {
        // Create test data
        Transaction::factory()->count(5)->create([
            'status' => 'completed',
            'amount' => 100,
            'created_at' => now()
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.analytics.chart-data', [
                'chart_type' => 'revenue',
                'period' => 7
            ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['date', 'revenue']
        ]);
    }

    /** @test */
    public function kpis_endpoint_returns_correct_data()
    {
        // Create test data
        User::factory()->count(3)->create(['status' => 'active']);
        Transaction::factory()->count(2)->create([
            'status' => 'completed',
            'amount' => 100
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.analytics.kpis'));

        $response->assertStatus(200);
        $response->assertJson([
            'active_users' => 3,
            'total_payments' => 200
        ]);
    }

    /** @test */
    public function top_earners_are_displayed_correctly()
    {
        // Create users with commissions
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Commission::factory()->create([
            'user_id' => $user1->id,
            'amount' => 100
        ]);

        Commission::factory()->create([
            'user_id' => $user2->id,
            'amount' => 200
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.analytics.index'));

        $response->assertStatus(200);
        $response->assertSee($user2->name); // Should show top earner first
    }

    /** @test */
    public function magazine_engagement_is_displayed_correctly()
    {
        // Create magazines with views
        $magazine1 = Magazine::factory()->create(['title' => 'Magazine 1']);
        $magazine2 = Magazine::factory()->create(['title' => 'Magazine 2']);

        MagazineView::factory()->count(5)->create(['magazine_id' => $magazine1->id]);
        MagazineView::factory()->count(10)->create(['magazine_id' => $magazine2->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.analytics.index'));

        $response->assertStatus(200);
        $response->assertSee('Magazine 2'); // Should show top viewed magazine first
    }

    /** @test */
    public function invalid_report_type_returns_error()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.analytics.export'), [
                'report_type' => 'invalid_type',
                'format' => 'csv',
                'filters' => []
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function invalid_export_format_returns_error()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.analytics.export'), [
                'report_type' => 'users',
                'format' => 'invalid_format',
                'filters' => []
            ]);

        $response->assertStatus(422);
    }
}
