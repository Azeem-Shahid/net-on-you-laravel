<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'marketing_opt_in',
        'language_preference',
    ];

    protected $casts = [
        'marketing_opt_in' => 'boolean',
    ];

    /**
     * Get the user these settings belong to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get users who have opted in to marketing
     */
    public function scopeMarketingOptIn($query)
    {
        return $query->where('marketing_opt_in', true);
    }

    /**
     * Scope to get users by language preference
     */
    public function scopeByLanguage($query, $language)
    {
        return $query->where('language_preference', $language);
    }

    /**
     * Check if user has opted in to marketing emails
     */
    public function isMarketingOptIn()
    {
        return $this->marketing_opt_in;
    }

    /**
     * Get language preference
     */
    public function getLanguagePreference()
    {
        return $this->language_preference ?? 'en';
    }
}
