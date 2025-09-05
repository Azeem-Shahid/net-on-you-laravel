<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'language',
        'title',
        'content',
        'is_active',
        'effective_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'datetime',
    ];

    /**
     * Get contract acceptances for this contract
     */
    public function acceptances()
    {
        return $this->hasMany(ContractAcceptance::class);
    }

    /**
     * Scope for active contracts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for contracts by language
     */
    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Get the latest active contract for a specific language
     * Falls back to English if the requested language is not available
     */
    public static function getLatestActive($language = 'en')
    {
        // Check if the language is a core language
        $coreLanguages = Language::getCoreLanguageCodes();
        $isCoreLanguage = in_array($language, $coreLanguages);

        // Try to get contract in requested language
        $contract = static::active()
            ->byLanguage($language)
            ->orderBy('effective_date', 'desc')
            ->first();

        // If not found and language is not English, fall back to English
        if (!$contract && $language !== 'en') {
            $contract = static::active()
                ->byLanguage('en')
                ->orderBy('effective_date', 'desc')
                ->first();
        }

        return $contract;
    }

    /**
     * Get the latest active contract for a specific language with fallback logic
     * Uses core language system for proper fallback
     */
    public static function getLatestActiveWithFallback($language = 'en')
    {
        // Get fallback language using the core language system
        $fallbackLanguage = Language::getContentFallbackLanguage($language);
        
        // Try to get contract in requested language first
        $contract = static::active()
            ->byLanguage($language)
            ->orderBy('effective_date', 'desc')
            ->first();

        // If not found, try fallback language
        if (!$contract && $fallbackLanguage !== $language) {
            $contract = static::active()
                ->byLanguage($fallbackLanguage)
                ->orderBy('effective_date', 'desc')
                ->first();
        }

        return $contract;
    }

    /**
     * Check if this contract is the latest active version
     */
    public function isLatestActive(): bool
    {
        $latest = static::getLatestActive($this->language);
        return $latest && $latest->id === $this->id;
    }

    /**
     * Activate this contract version
     */
    public function activate(): void
    {
        // Deactivate all other contracts of the same language
        static::where('language', $this->language)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate this contract version
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
