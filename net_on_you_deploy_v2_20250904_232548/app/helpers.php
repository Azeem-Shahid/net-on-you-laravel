<?php

if (!function_exists('t')) {
    /**
     * Translation helper function
     */
    function t(string $key, array $replacements = [], ?string $module = null): string
    {
        return app(\App\Services\TranslationService::class)->get($key, $replacements, $module);
    }
}

if (!function_exists('current_language')) {
    /**
     * Get current language code
     */
    function current_language(): string
    {
        return app(\App\Services\TranslationService::class)->getCurrentLanguage();
    }
}

if (!function_exists('available_languages')) {
    /**
     * Get available languages
     */
    function available_languages()
    {
        return app(\App\Services\TranslationService::class)->getAvailableLanguages();
    }
}
