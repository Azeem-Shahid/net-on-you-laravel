<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class TranslationService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'translations';

    /**
     * Get translation for a key in the current language
     */
    public function get(string $key, array $replacements = [], ?string $module = null): string
    {
        $languageCode = $this->getCurrentLanguage();
        $cacheKey = $this->getCacheKey($languageCode, $key, $module);

        // Try to get from cache first
        $translation = Cache::get($cacheKey);
        
        if (!$translation) {
            // Get from database
            $query = Translation::where('key', $key)
                ->where('language_code', $languageCode);

            if ($module) {
                $query->module($module);
            }

            $translation = $query->value('value');

            // If not found, try default language
            if (!$translation) {
                $defaultLanguage = Language::default()->first();
                if ($defaultLanguage && $defaultLanguage->code !== $languageCode) {
                    $query = Translation::where('key', $key)
                        ->where('language_code', $defaultLanguage->code);

                    if ($module) {
                        $query->module($module);
                    }

                    $translation = $query->value('value');
                }
            }

            // If still not found, return the key
            if (!$translation) {
                $translation = $key;
            }

            // Cache the result
            Cache::put($cacheKey, $translation, self::CACHE_TTL);
        }

        // Apply replacements
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
            // Validate the cookie language exists
            $language = Language::active()->where('code', $cookieLanguage)->first();
            if ($language) {
                return $cookieLanguage;
            }
        }

        // Check browser language
        $browserLanguage = $this->getBrowserLanguage();
        if ($browserLanguage) {
            return $browserLanguage;
        }

        // Fallback to default language
        $defaultLanguage = Language::default()->first();
        return $defaultLanguage ? $defaultLanguage->code : 'en';
    }

    /**
     * Set current language
     */
    public function setLanguage(string $languageCode): void
    {
        // Validate language exists and is active
        $language = Language::active()->where('code', $languageCode)->first();
        if (!$language) {
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
            return Language::active()->orderBy('is_default', 'desc')->orderBy('name')->get();
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
            $language = Language::active()->where('code', $code)->first();
            if ($language) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Get cache key for translation
     */
    private function getCacheKey(string $languageCode, string $key, ?string $module): string
    {
        $parts = [self::CACHE_PREFIX, $languageCode, $key];
        if ($module) {
            $parts[] = $module;
        }
        return implode(':', $parts);
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

    /**
     * Clear cache for specific language
     */
    public function clearCacheForLanguage(string $languageCode): void
    {
        $keys = Cache::get('translation_keys_' . $languageCode, []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('translation_keys_' . $languageCode);
    }

    /**
     * Get translation with fallback
     */
    public function getWithFallback(string $key, array $replacements = [], ?string $module = null): string
    {
        $translation = $this->get($key, $replacements, $module);
        
        // If translation is the same as key, try without module
        if ($translation === $key && $module) {
            $translation = $this->get($key, $replacements);
        }

        return $translation;
    }
}
