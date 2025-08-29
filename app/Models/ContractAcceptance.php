<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAcceptance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contract_id',
        'accepted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    /**
     * Get the user who accepted the contract
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contract that was accepted
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Check if user has accepted the latest active contract for a language
     */
    public static function hasAcceptedLatest(User $user, string $language = 'en'): bool
    {
        $latestContract = Contract::getLatestActive($language);
        
        if (!$latestContract) {
            return false;
        }

        return static::where('user_id', $user->id)
            ->where('contract_id', $latestContract->id)
            ->exists();
    }

    /**
     * Accept the latest active contract for a user
     */
    public static function acceptLatest(User $user, string $language = 'en', string $ipAddress = null, string $userAgent = null): bool
    {
        $latestContract = Contract::getLatestActive($language);
        
        if (!$latestContract) {
            return false;
        }

        // Check if already accepted
        if (static::hasAcceptedLatest($user, $language)) {
            return true;
        }

        // Create acceptance record
        return static::create([
            'user_id' => $user->id,
            'contract_id' => $latestContract->id,
            'accepted_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]) !== null;
    }
}
