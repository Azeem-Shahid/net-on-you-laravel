<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'scopes',
        'is_active',
        'created_by_admin_id',
        'last_used_at',
        'metadata',
    ];

    protected $casts = [
        'scopes' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'key',
    ];

    /**
     * Generate a new API key
     */
    public static function generateKey(): string
    {
        return 'nk_' . Str::random(32);
    }

    /**
     * Check if API key has specific scope
     */
    public function hasScope(string $scope): bool
    {
        return isset($this->scopes[$scope]) && $this->scopes[$scope] === true;
    }

    /**
     * Check if API key has any of the given scopes
     */
    public function hasAnyScope(array $scopes): bool
    {
        foreach ($scopes as $scope) {
            if ($this->hasScope($scope)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get admin who created this API key
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /**
     * Get masked key for display
     */
    public function getMaskedKeyAttribute(): string
    {
        if (strlen($this->key) <= 8) {
            return str_repeat('*', strlen($this->key));
        }
        return substr($this->key, 0, 4) . str_repeat('*', strlen($this->key) - 8) . substr($this->key, -4);
    }

    /**
     * Boot method to generate key before creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($apiKey) {
            if (empty($apiKey->key)) {
                $apiKey->key = static::generateKey();
            }
        });
    }
}

