<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLanguagePreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'language_code',
    ];

    /**
     * Get user for this preference
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get language for this preference
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }

    /**
     * Find preference by user ID
     */
    public static function findByUserId($userId)
    {
        return static::where('user_id', $userId)->first();
    }

    /**
     * Update or create preference for user
     */
    public static function updateOrCreateForUser($userId, $languageCode)
    {
        return static::updateOrCreate(
            ['user_id' => $userId],
            ['language_code' => $languageCode]
        );
    }
}
