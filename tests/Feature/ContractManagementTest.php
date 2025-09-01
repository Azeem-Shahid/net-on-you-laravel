<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Contract;
use App\Models\EmailTemplate;
use App\Models\AdminActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ContractManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $contract;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = Admin::factory()->superAdmin()->create();
        
        // Create test contract
        $this->contract = Contract::factory()->create([
            'title' => 'Test Contract',
            'language' => 'en',
            'effective_date' => now(),
            'is_active' => true,
        ]);

        // Create required email templates
        $this->createEmailTemplates();
        
        // Mock storage
        Storage::fake('contracts');
    }

    private function createEmailTemplates()
    {
        // Create email templates with all required fields
        EmailTemplate::create([
            'name' => 'contract_created',
            'language' => 'en',
            'subject' => 'Contract Created',
            'body' => 'A new contract has been created.',
            'variables' => json_encode(['contract_name', 'language']),
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        EmailTemplate::create([
            'name' => 'contract_updated',
            'language' => 'en',
            'subject' => 'Contract Updated',
            'body' => 'A contract has been updated.',
            'variables' => json_encode(['contract_name', 'language']),
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_access_contracts_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.contracts.index'));

        $response->assertStatus(200);
        $response->assertSee('Contract Management');
    }

    public function test_admin_can_create_contract()
    {
        $contractData = [
            'title' => 'New Test Contract',
            'language' => 'es',
            'effective_date' => now()->addDays(30)->format('Y-m-d'),
            'description' => 'Test contract description',
            'is_active' => '1',
        ];

        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.store'), array_merge($contractData, [
                'contract_file' => $file
            ]));

        $response->assertRedirect(route('admin.contracts.index'));
        
        $this->assertDatabaseHas('contracts', [
            'title' => 'New Test Contract',
            'language' => 'es',
        ]);
    }

    public function test_admin_can_edit_contract()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.contracts.edit', $this->contract));

        $response->assertStatus(200);
        $response->assertSee('Edit Contract');
    }

    public function test_admin_can_update_contract()
    {
        $updateData = [
            'title' => 'Updated Contract Name',
            'language' => 'en',
            'effective_date' => now()->addDays(60)->format('Y-m-d'),
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.contracts.update', $this->contract), $updateData);

        $response->assertRedirect(route('admin.contracts.index'));
        
        $this->assertDatabaseHas('contracts', [
            'id' => $this->contract->id,
            'title' => 'Updated Contract Name',
        ]);
    }

    public function test_admin_can_delete_contract()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.contracts.destroy', $this->contract));

        $response->assertRedirect(route('admin.contracts.index'));
        
        $this->assertDatabaseMissing('contracts', [
            'id' => $this->contract->id,
        ]);
    }

    public function test_admin_can_toggle_contract_status()
    {
        // Create another contract in the same language
        $otherContract = Contract::factory()->create([
            'title' => 'Other Contract',
            'language' => 'en',
            'effective_date' => now()->addDays(30),
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.toggle-status', $this->contract));

        $response->assertJson(['success' => true]);
        
        // Check that the current contract is now inactive
        $this->contract->refresh();
        $this->assertFalse($this->contract->is_active);
        
        // Check that the other contract is now active
        $otherContract->refresh();
        $this->assertTrue($otherContract->is_active);
    }

    public function test_admin_can_import_contract()
    {
        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.import'), [
                'contract_file' => $file,
                'title' => 'Imported Contract',
                'language' => 'fr',
                'effective_date' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('admin.contracts.index'));
        
        $this->assertDatabaseHas('contracts', [
            'title' => 'Imported Contract',
            'language' => 'fr',
        ]);
    }

    public function test_admin_can_export_contract()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.contracts.export', $this->contract));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_contract_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.store'), []);

        $response->assertSessionHasErrors(['title', 'language', 'effective_date', 'contract_file']);
    }

    public function test_contract_update_requires_valid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.contracts.update', $this->contract), []);

        $response->assertSessionHasErrors(['title', 'language', 'effective_date']);
    }

    public function test_contract_language_must_be_valid()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.store'), [
                'title' => 'Test Contract',
                'language' => 'invalid',
                'effective_date' => now()->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors(['language']);
    }

    public function test_contract_effective_date_must_be_future()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.store'), [
                'title' => 'Test Contract',
                'language' => 'en',
                'effective_date' => now()->subDays(1)->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors(['effective_date']);
    }

    public function test_contract_file_must_be_valid_format()
    {
        $file = UploadedFile::fake()->create('contract.txt', 100);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.store'), [
                'title' => 'Test Contract',
                'language' => 'en',
                'effective_date' => now()->format('Y-m-d'),
                'contract_file' => $file,
            ]);

        $response->assertSessionHasErrors(['contract_file']);
    }

    public function test_contract_name_must_be_unique_per_language()
    {
        // Create contract with same name and language
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.store'), [
                'title' => $this->contract->title,
                'language' => $this->contract->language,
                'effective_date' => now()->addDays(30)->format('Y-m-d'),
                'contract_file' => UploadedFile::fake()->create('contract.pdf', 100),
            ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_contract_can_have_same_name_in_different_languages()
    {
        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.store'), [
                'title' => $this->contract->title,
                'language' => 'es', // Different language
                'effective_date' => now()->addDays(30)->format('Y-m-d'),
                'contract_file' => $file,
            ]);

        $response->assertRedirect(route('admin.contracts.index'));
        
        $this->assertDatabaseHas('contracts', [
            'title' => $this->contract->title,
            'language' => 'es',
        ]);
    }

    public function test_contract_activation_deactivates_others_in_same_language()
    {
        // Create another contract in the same language
        $otherContract = Contract::factory()->create([
            'title' => 'Other Contract',
            'language' => 'en',
            'effective_date' => now()->addDays(30),
            'is_active' => true,
        ]);

        // Activate the first contract
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.toggle-status', $this->contract));

        // Check that the other contract is now inactive
        $otherContract->refresh();
        $this->assertFalse($otherContract->is_active);
    }

    public function test_contract_import_creates_proper_records()
    {
        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.import'), [
                'contract_file' => $file,
                'title' => 'Imported Contract',
                'language' => 'de',
                'effective_date' => now()->format('Y-m-d'),
            ]);

        $contract = Contract::where('title', 'Imported Contract')->first();
        $this->assertNotNull($contract);
        $this->assertEquals('de', $contract->language);
        $this->assertTrue($contract->is_active);
    }

    public function test_contract_export_returns_correct_file()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.contracts.export', $this->contract));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="' . $this->contract->title . '.pdf"');
    }

    public function test_contract_creation_logs_admin_activity()
    {
        $contractData = [
            'title' => 'Activity Logged Contract',
            'language' => 'it',
            'effective_date' => now()->addDays(30)->format('Y-m-d'),
            'description' => 'Test contract for activity logging',
            'is_active' => '1',
        ];

        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.store'), array_merge($contractData, [
                'contract_file' => $file
            ]));

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'created_contract',
        ]);
    }

    public function test_contract_update_logs_admin_activity()
    {
        $updateData = [
            'title' => 'Updated Contract for Activity',
            'language' => 'en',
            'effective_date' => now()->addDays(60)->format('Y-m-d'),
            'description' => 'Updated description for activity logging',
        ];

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.contracts.update', $this->contract), $updateData);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'updated_contract',
        ]);
    }

    public function test_contract_deletion_logs_admin_activity()
    {
        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.contracts.destroy', $this->contract));

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'deleted_contract',
        ]);
    }

    public function test_contract_status_toggle_logs_admin_activity()
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.toggle-status', $this->contract));

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'toggled_contract_status',
        ]);
    }

    public function test_contract_import_logs_admin_activity()
    {
        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.contracts.import'), [
                'contract_file' => $file,
                'title' => 'Imported Contract for Activity',
                'language' => 'pt',
                'effective_date' => now()->format('Y-m-d'),
            ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'imported_contract',
        ]);
    }

    public function test_contract_english_language_fallback()
    {
        // Create English contract
        $englishContract = Contract::factory()->create([
            'title' => 'English Contract',
            'language' => 'en',
            'effective_date' => now(),
            'is_active' => true,
        ]);

        // Try to get contract in Spanish (should fall back to English)
        $contract = Contract::getLatestActive('es');
        
        $this->assertNotNull($contract);
        $this->assertEquals('en', $contract->language);
        $this->assertEquals('English Contract', $contract->title);
    }

    public function test_contract_english_language_no_fallback_when_english_requested()
    {
        // Create Spanish contract only
        $spanishContract = Contract::factory()->create([
            'title' => 'Spanish Contract',
            'language' => 'es',
            'effective_date' => now(),
            'is_active' => true,
        ]);

        // Try to get contract in English (should not fall back)
        $contract = Contract::getLatestActive('en');
        
        $this->assertNull($contract);
    }
}
