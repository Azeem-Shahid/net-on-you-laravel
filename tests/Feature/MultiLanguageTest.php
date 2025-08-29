<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Language;
use App\Models\Translation;
use App\Models\User;
use App\Models\Admin;
use App\Models\UserLanguagePreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MultiLanguageTest extends TestCase
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
            'role' => 'super_admin',
        ]);

        // Create regular user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function it_can_create_languages()
    {
        $this->actingAs($this->admin, 'admin');

        $languageData = [
            'code' => 'fr',
            'name' => 'French',
            'status' => 'active',
        ];

        $response = $this->post(route('admin.languages.store'), $languageData);

        $response->assertRedirect(route('admin.languages.index'));
        $this->assertDatabaseHas('languages', $languageData);
    }

    /** @test */
    public function it_can_set_language_as_default()
    {
        $this->actingAs($this->admin, 'admin');

        // Create a language
        $language = Language::create([
            'code' => 'es',
            'name' => 'Spanish',
            'status' => 'active',
            'is_default' => false,
        ]);

        $response = $this->post(route('admin.languages.set-default', $language));

        $response->assertRedirect();
        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'is_default' => true,
        ]);
    }

    /** @test */
    public function it_can_toggle_language_status()
    {
        $this->actingAs($this->admin, 'admin');

        $language = Language::create([
            'code' => 'de',
            'name' => 'German',
            'status' => 'active',
            'is_default' => false,
        ]);

        $response = $this->post(route('admin.languages.toggle-status', $language));

        $response->assertRedirect();
        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function it_can_create_translations()
    {
        $this->actingAs($this->admin, 'admin');

        // Create a language first
        $language = Language::create([
            'code' => 'it',
            'name' => 'Italian',
            'status' => 'active',
        ]);

        $translationData = [
            'language_code' => 'it',
            'key' => 'welcome',
            'value' => 'Benvenuto',
            'module' => 'common',
        ];

        $response = $this->post(route('admin.translations.store'), $translationData);

        $response->assertRedirect(route('admin.translations.index'));
        $this->assertDatabaseHas('translations', $translationData);
    }

    /** @test */
    public function it_can_switch_language()
    {
        // Create languages
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'is_default' => true,
            'status' => 'active',
        ]);

        Language::create([
            'code' => 'pt',
            'name' => 'Portuguese',
            'status' => 'active',
        ]);

        $response = $this->post(route('language.switch'), [
            'language' => 'pt',
        ]);

        $response->assertRedirect();
        $this->assertEquals('pt', session('language'));
    }

    /** @test */
    public function it_saves_user_language_preference()
    {
        // Create languages
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'is_default' => true,
            'status' => 'active',
        ]);

        Language::create([
            'code' => 'ru',
            'name' => 'Russian',
            'status' => 'active',
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('language.switch'), [
            'language' => 'ru',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('user_language_preferences', [
            'user_id' => $this->user->id,
            'language_code' => 'ru',
        ]);
    }

    /** @test */
    public function it_detects_browser_language()
    {
        // Create languages
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'is_default' => true,
            'status' => 'active',
        ]);

        Language::create([
            'code' => 'ja',
            'name' => 'Japanese',
            'status' => 'active',
        ]);

        $response = $this->withHeaders([
            'Accept-Language' => 'ja,en;q=0.9',
        ])->get(route('language.current'));

        $response->assertJson([
            'current' => 'ja',
        ]);
    }

    /** @test */
    public function it_falls_back_to_default_language()
    {
        // Create languages
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'is_default' => true,
            'status' => 'active',
        ]);

        Language::create([
            'code' => 'ar',
            'name' => 'Arabic',
            'status' => 'active',
        ]);

        // Create translation only in English
        Translation::create([
            'language_code' => 'en',
            'key' => 'hello',
            'value' => 'Hello',
            'module' => 'common',
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        // Set current language to Arabic
        session(['language' => 'ar']);

        $response = $this->get(route('language.current'));
        
        // Should fallback to English for missing translations
        $this->assertEquals('en', $response->json('current'));
    }

    /** @test */
    public function it_prevents_deleting_default_language()
    {
        $this->actingAs($this->admin, 'admin');

        $defaultLanguage = Language::create([
            'code' => 'en',
            'name' => 'English',
            'is_default' => true,
            'status' => 'active',
        ]);

        $response = $this->delete(route('admin.languages.destroy', $defaultLanguage));

        $response->assertRedirect();
        $this->assertDatabaseHas('languages', [
            'id' => $defaultLanguage->id,
        ]);
    }

    /** @test */
    public function it_prevents_deleting_language_with_translations()
    {
        $this->actingAs($this->admin, 'admin');

        $language = Language::create([
            'code' => 'fr',
            'name' => 'French',
            'status' => 'active',
        ]);

        // Create a translation
        Translation::create([
            'language_code' => 'fr',
            'key' => 'bonjour',
            'value' => 'Bonjour',
            'module' => 'common',
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $response = $this->delete(route('admin.languages.destroy', $language));

        $response->assertRedirect();
        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
        ]);
    }

    /** @test */
    public function it_prevents_deactivating_default_language()
    {
        $this->actingAs($this->admin, 'admin');

        $defaultLanguage = Language::create([
            'code' => 'en',
            'name' => 'English',
            'is_default' => true,
            'status' => 'active',
        ]);

        $response = $this->post(route('admin.languages.toggle-status', $defaultLanguage));

        $response->assertRedirect();
        $this->assertDatabaseHas('languages', [
            'id' => $defaultLanguage->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function it_requires_admin_authentication_for_language_management()
    {
        $response = $this->get(route('admin.languages.index'));
        $response->assertRedirect(route('admin.login'));

        $response = $this->post(route('admin.languages.store'), []);
        $response->assertRedirect(route('admin.login'));
    }

    /** @test */
    public function it_requires_admin_authentication_for_translation_management()
    {
        $response = $this->get(route('admin.translations.index'));
        $response->assertRedirect(route('admin.login'));

        $response = $this->post(route('admin.translations.store'), []);
        $response->assertRedirect(route('admin.login'));
    }

    /** @test */
    public function it_validates_language_data()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->post(route('admin.languages.store'), []);

        $response->assertSessionHasErrors(['code', 'name', 'status']);
    }

    /** @test */
    public function it_validates_translation_data()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->post(route('admin.translations.store'), []);

        $response->assertSessionHasErrors(['language_code', 'key', 'value']);
    }

    /** @test */
    public function it_prevents_duplicate_translation_keys_per_language()
    {
        $this->actingAs($this->admin, 'admin');

        // Create a language
        $language = Language::create([
            'code' => 'nl',
            'name' => 'Dutch',
            'status' => 'active',
        ]);

        // Create first translation
        Translation::create([
            'language_code' => 'nl',
            'key' => 'hallo',
            'value' => 'Hallo',
            'module' => 'common',
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        // Try to create duplicate key
        $response = $this->post(route('admin.translations.store'), [
            'language_code' => 'nl',
            'key' => 'hallo',
            'value' => 'Hello',
            'module' => 'common',
        ]);

        $response->assertSessionHasErrors(['key']);
    }

    /** @test */
    public function it_can_export_translations()
    {
        $this->actingAs($this->admin, 'admin');

        // Create language and translations
        $language = Language::create([
            'code' => 'sv',
            'name' => 'Swedish',
            'status' => 'active',
        ]);

        Translation::create([
            'language_code' => 'sv',
            'key' => 'hej',
            'value' => 'Hej',
            'module' => 'common',
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        $response = $this->get(route('admin.translations.export', [
            'language_code' => 'sv',
            'format' => 'json',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    /** @test */
    public function it_handles_missing_translations_gracefully()
    {
        // Create only English language
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'is_default' => true,
            'status' => 'active',
        ]);

        // Try to access non-existent translation
        $response = $this->get(route('language.current'));
        
        $response->assertStatus(200);
        $this->assertEquals('en', $response->json('current'));
    }
}
