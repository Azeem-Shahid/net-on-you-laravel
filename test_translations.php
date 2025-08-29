<?php

/**
 * Quick Test Script for Multi-Language System
 * Run this with: php test_translations.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\TranslationService;
use App\Models\Language;
use App\Models\Translation;

echo "🧪 Testing Multi-Language System\n";
echo "================================\n\n";

try {
    // Test 1: Check if languages exist
    echo "1. Checking languages...\n";
    $languages = Language::all();
    if ($languages->count() > 0) {
        echo "   ✅ Found " . $languages->count() . " languages:\n";
        foreach ($languages as $lang) {
            echo "      - {$lang->name} ({$lang->code}) - " . 
                 ($lang->is_default ? 'Default' : 'Not Default') . 
                 " - Status: {$lang->status}\n";
        }
    } else {
        echo "   ❌ No languages found. Run the seeder first.\n";
    }
    
    echo "\n";
    
    // Test 2: Check if translations exist
    echo "2. Checking translations...\n";
    $translations = Translation::all();
    if ($translations->count() > 0) {
        echo "   ✅ Found " . $translations->count() . " translations\n";
        
        // Show sample translations
        $sample = $translations->take(3);
        foreach ($sample as $trans) {
            echo "      - {$trans->key} ({$trans->language_code}): {$trans->value}\n";
        }
    } else {
        echo "   ❌ No translations found. Run the seeder first.\n";
    }
    
    echo "\n";
    
    // Test 3: Test TranslationService
    echo "3. Testing TranslationService...\n";
    $translationService = app(TranslationService::class);
    
    $currentLang = $translationService->getCurrentLanguage();
    echo "   Current language: {$currentLang}\n";
    
    $availableLangs = $translationService->getAvailableLanguages();
    echo "   Available languages: " . $availableLangs->pluck('code')->implode(', ') . "\n";
    
    // Test getting a translation
    $testKey = 'welcome';
    $translation = $translationService->get($testKey);
    echo "   Translation for '{$testKey}': {$translation}\n";
    
    echo "\n";
    
    // Test 4: Test helper functions
    echo "4. Testing helper functions...\n";
    if (function_exists('t')) {
        echo "   ✅ t() function is available\n";
        $helperTranslation = t('welcome');
        echo "   t('welcome') returns: {$helperTranslation}\n";
    } else {
        echo "   ❌ t() function not found. Check composer autoload.\n";
    }
    
    if (function_exists('current_language')) {
        echo "   ✅ current_language() function is available\n";
        $currentLangHelper = current_language();
        echo "   current_language() returns: {$currentLangHelper}\n";
    } else {
        echo "   ❌ current_language() function not found.\n";
    }
    
    echo "\n";
    
    // Test 5: Test language switching
    echo "5. Testing language switching...\n";
    $defaultLang = Language::where('is_default', true)->first();
    if ($defaultLang) {
        echo "   Default language: {$defaultLang->name} ({$defaultLang->code})\n";
        
        // Try to get a translation in the default language
        $defaultTranslation = $translationService->get('welcome', [], null, $defaultLang->code);
        echo "   Default language translation for 'welcome': {$defaultTranslation}\n";
    } else {
        echo "   ❌ No default language set\n";
    }
    
    echo "\n";
    echo "🎉 Testing completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
