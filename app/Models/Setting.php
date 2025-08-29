<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'updated_by_admin_id',
    ];

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, string $value, string $type = 'string', string $description = null, int $adminId = null): void
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'updated_by_admin_id' => $adminId,
            ]
        );

        // Clear cache
        Cache::forget("setting_{$key}");
    }

    /**
     * Get multiple settings by keys
     */
    public static function getMultiple(array $keys): array
    {
        $settings = static::whereIn('key', $keys)->get()->keyBy('key');
        
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings->get($key)?->value;
        }
        
        return $result;
    }

    /**
     * Set multiple settings at once
     */
    public static function setMultiple(array $settings, int $adminId = null): void
    {
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                static::setValue(
                    $key, 
                    $value['value'], 
                    $value['type'] ?? 'string',
                    $value['description'] ?? null,
                    $adminId
                );
            } else {
                static::setValue($key, $value, 'string', null, $adminId);
            }
        }
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting_{$key}");
        }
    }

    /**
     * Get all settings as key-value pairs
     */
    public static function getAllAsArray(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

    /**
     * Get admin who last updated this setting
     */
    public function updatedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    /**
     * Get settings grouped by category
     */
    public static function getGroupedSettings(): array
    {
        $settings = static::all()->groupBy(function ($setting) {
            $parts = explode('.', $setting->key);
            return $parts[0] ?? 'general';
        });

        return $settings->toArray();
    }
}
