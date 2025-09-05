<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_default',
        'status',
        'is_core',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_core' => 'boolean',
    ];

    /**
     * Get all active languages
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get default language
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get translations for this language
     */
    public function translations()
    {
        return $this->hasMany(Translation::class, 'language_code', 'code');
    }

    /**
     * Check if this is the default language
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Check if this language is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Set as default language
     */
    public function setAsDefault(): void
    {
        // Remove default from other languages
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }

    /**
     * Get all core languages
     */
    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }

    /**
     * Check if this is a core language
     */
    public function isCore(): bool
    {
        return $this->is_core;
    }

    /**
     * Get core language codes
     */
    public static function getCoreLanguageCodes(): array
    {
        return static::core()->pluck('code')->toArray();
    }

    /**
     * Get fallback language for content
     */
    public static function getContentFallbackLanguage(string $languageCode): string
    {
        $coreLanguages = static::getCoreLanguageCodes();
        return in_array($languageCode, $coreLanguages) ? $languageCode : 'en';
    }
}
