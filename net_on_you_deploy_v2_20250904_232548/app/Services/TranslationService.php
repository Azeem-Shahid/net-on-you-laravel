<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class TranslationService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'translations';

    /**
     * Get translation for a key in the current language
     * Since we use GTranslate, this just returns the key for GTranslate to handle
     */
    public function get(string $key, array $replacements = [], ?string $module = null): string
    {
        // Apply replacements if any
        $translation = $key;
        if (!empty($replacements)) {
            foreach ($replacements as $placeholder => $value) {
                $translation = str_replace(":$placeholder", $value, $translation);
            }
        }

        return $translation;
    }

    /**
     * Get current language code
     */
    public function getCurrentLanguage(): string
    {
        // Check if user is logged in and has language preference
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->language) {
                return $user->language;
            }
        }

        // Check session
        if (Session::has('language')) {
            return Session::get('language');
        }

        // Check cookie as fallback
        if (isset($_COOKIE['language'])) {
            $cookieLanguage = $_COOKIE['language'];
            // Validate the cookie language is supported
            if (in_array($cookieLanguage, ['en', 'es'])) {
                return $cookieLanguage;
            }
        }

        // Check browser language
        $browserLanguage = $this->getBrowserLanguage();
        if ($browserLanguage && in_array($browserLanguage, ['en', 'es'])) {
            return $browserLanguage;
        }

        // Fallback to default language
        return 'en';
    }

    /**
     * Set current language
     */
    public function setLanguage(string $languageCode): void
    {
        // Validate language is supported
        if (!in_array($languageCode, ['en', 'es'])) {
            return;
        }

        // Ensure session is started
        if (!Session::isStarted()) {
            Session::start();
        }

        // Save to session for guests
        Session::put('language', $languageCode);

        // Save to user language field if logged in
        if (auth()->check()) {
            auth()->user()->update(['language' => $languageCode]);
        }

        // Clear specific caches
        $this->clearAvailableLanguagesCache();
        $this->clearCache();
        
        // Force session to be saved immediately
        Session::save();
        
        // Also set a cookie for better persistence
        setcookie('language', $languageCode, time() + (86400 * 30), '/'); // 30 days
    }

    /**
     * Get available languages
     */
    public function getAvailableLanguages()
    {
        return Cache::remember('available_languages', self::CACHE_TTL, function () {
            return collect([
                ['code' => 'en', 'name' => 'English', 'is_default' => true],
                ['code' => 'es', 'name' => 'Español', 'is_default' => false]
            ]);
        });
    }

    /**
     * Get browser language
     */
    private function getBrowserLanguage(): ?string
    {
        $browserLanguages = request()->getLanguages();
        
        foreach ($browserLanguages as $browserLang) {
            $code = substr($browserLang, 0, 2);
            if (in_array($code, ['en', 'es'])) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Clear translation cache
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
    
    /**
     * Clear available languages cache
     */
    public function clearAvailableLanguagesCache(): void
    {
        Cache::forget('available_languages');
    }
}
