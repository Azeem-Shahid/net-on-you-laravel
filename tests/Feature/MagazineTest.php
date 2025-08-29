<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Magazine;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MagazineTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function admin_can_create_magazine()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $magazineData = [
            'title' => 'Test Magazine',
            'description' => 'Test Description',
            'category' => 'Technology',
            'language_code' => 'en',
            'status' => 'active',
            'published_at' => now()->format('Y-m-d'),
            'magazine_file' => UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf'),
        ];

        $response = $this->post(route('admin.magazines.store'), $magazineData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('magazines', [
            'title' => 'Test Magazine',
            'category' => 'Technology',
            'language_code' => 'en',
        ]);
    }

    /** @test */
    public function user_without_subscription_cannot_access_magazines()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('magazines.index'));

        $response->assertViewIs('magazines.subscription-required');
    }

    /** @test */
    public function user_with_active_subscription_can_access_magazines()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create active subscription
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'end_date' => now()->addDays(30),
        ]);

        $response = $this->get(route('magazines.index'));

        $response->assertStatus(200);
        $response->assertViewIs('magazines.index');
    }

    /** @test */
    public function user_can_view_magazine_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create active subscription
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'end_date' => now()->addDays(30),
        ]);

        // Create magazine
        $magazine = Magazine::factory()->create([
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get(route('magazines.show', $magazine));

        $response->assertStatus(200);
        $response->assertViewIs('magazines.show');
    }

    /** @test */
    public function user_can_download_magazine()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create active subscription
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'end_date' => now()->addDays(30),
        ]);

        // Create magazine with file
        $magazine = Magazine::factory()->create([
            'status' => 'active',
            'published_at' => now(),
            'file_path' => 'magazines/test.pdf',
        ]);

        // Mock file exists
        Storage::disk('public')->put('magazines/test.pdf', 'fake content');

        $response = $this->get(route('magazines.download', $magazine));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_upload_magazine_version()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $magazine = Magazine::factory()->create();

        $versionData = [
            'version_file' => UploadedFile::fake()->create('version.pdf', 1000, 'application/pdf'),
            'version' => 'v2.0',
            'notes' => 'Updated content',
        ];

        $response = $this->post(route('admin.magazines.upload-version', $magazine), $versionData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function magazine_analytics_are_recorded()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create active subscription
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'end_date' => now()->addDays(30),
        ]);

        $magazine = Magazine::factory()->create([
            'status' => 'active',
            'published_at' => now(),
        ]);

        // View magazine
        $this->get(route('magazines.show', $magazine));

        $this->assertDatabaseHas('magazine_views', [
            'magazine_id' => $magazine->id,
            'user_id' => $user->id,
            'action' => 'viewed',
        ]);
    }

    /** @test */
    public function magazine_filters_work_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create active subscription
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'end_date' => now()->addDays(30),
        ]);

        // Create magazines with different categories
        Magazine::factory()->create([
            'title' => 'Tech Magazine',
            'category' => 'Technology',
            'status' => 'active',
            'published_at' => now(),
        ]);

        Magazine::factory()->create([
            'title' => 'Finance Magazine',
            'category' => 'Finance',
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get(route('magazines.index', ['category' => 'Technology']));

        $response->assertStatus(200);
        $response->assertSee('Tech Magazine');
        $response->assertDontSee('Finance Magazine');
    }
}
