<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_name',
        'policy_value',
        'description',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'policy_value' => 'string',
    ];

    /**
     * Get policy value by name
     */
    public static function getPolicyValue(string $name, $default = null)
    {
        $policy = static::where('policy_name', $name)->first();
        return $policy ? $policy->policy_value : $default;
    }

    /**
     * Set policy value by name
     */
    public static function setPolicyValue(string $name, string $value, string $description = null, int $adminId = null): void
    {
        static::updateOrCreate(
            ['policy_name' => $name],
            [
                'policy_value' => $value,
                'description' => $description,
                'updated_by_admin_id' => $adminId,
            ]
        );
    }

    /**
     * Get admin who last updated this policy
     */
    public function updatedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    /**
     * Get all policies as key-value pairs
     */
    public static function getAllAsArray(): array
    {
        return static::pluck('policy_value', 'policy_name')->toArray();
    }
}

