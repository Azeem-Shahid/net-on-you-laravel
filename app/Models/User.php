<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\PasswordResetNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'referrer_id',
        'referral_code',
        'name',
        'email',
        'password',
        'wallet_address',
        'role',
        'language',
        'status',
        'subscription_start_date',
        'subscription_end_date',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has verified email
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get referrer user
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get referred users
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    /**
     * Get referral records
     */
    public function referralRecords()
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Get referrals by level
     */
    public function referralsByLevel(int $level)
    {
        return $this->referralRecords()->where('level', $level);
    }

    /**
     * Get direct referrals (level 1)
     */
    public function directReferrals()
    {
        return $this->referralsByLevel(1);
    }

    /**
     * Get all downline users (all levels)
     */
    public function downlineUsers()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    /**
     * Get downline users by level
     */
    public function downlineUsersByLevel(int $level)
    {
        return $this->downlineUsers()->whereHas('referralRecords', function($query) use ($level) {
            $query->where('level', $level);
        });
    }

    /**
     * Check if user has direct sales in a specific month
     */
    public function hasDirectSalesInMonth(string $month): bool
    {
        return $this->referrals()
            ->whereHas('transactions', function($query) use ($month) {
                $query->where('status', 'completed')
                      ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
            })
            ->exists();
    }

    /**
     * Get commission eligibility for a specific month
     */
    public function getCommissionEligibility(string $month): string
    {
        // Founder (User ID 1) always has special access - no sales requirements
        if ($this->id === 1) {
            return 'eligible';
        }
        
        // Direct referrals of founder must meet sales requirements
        if ($this->referrer_id === 1) {
            return $this->hasDirectSalesInMonth($month) ? 'eligible' : 'ineligible';
        }
        
        // All other users must meet sales requirements
        return $this->hasDirectSalesInMonth($month) ? 'eligible' : 'ineligible';
    }

    /**
     * Get transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get commissions earned
     */
    public function commissionsEarned()
    {
        return $this->hasMany(Commission::class, 'earner_user_id');
    }

    /**
     * Get magazine entitlements
     */
    public function magazineEntitlements()
    {
        return $this->hasMany(MagazineEntitlement::class);
    }

    /**
     * Get contract acceptances
     */
    public function contractAcceptances()
    {
        return $this->hasMany(ContractAcceptance::class);
    }

    /**
     * Check if user has accepted the latest contract for their language
     */
    public function hasAcceptedLatestContract(): bool
    {
        $language = $this->language ?? 'en';
        return ContractAcceptance::hasAcceptedLatest($this, $language);
    }

    /**
     * Check if user has special access (founder or direct referral of founder)
     */
    public function hasSpecialAccess(): bool
    {
        $founderId = $this->getFounderId();
        
        // Founder has special access
        if ($this->id === $founderId) {
            return true;
        }

        // Direct referrals of founder have special access
        if ($this->referrer_id === $founderId) {
            return true;
        }

        return false;
    }

    /**
     * Get the founder's user ID (first created user)
     */
    public function getFounderId(): int
    {
        static $founderId = null;
        
        if ($founderId === null) {
            $founder = static::orderBy('id')->first();
            $founderId = $founder ? $founder->id : 1;
        }
        
        return $founderId;
    }

    /**
     * Check if user gets free access (no payment required)
     */
    public function getsFreeAccess(): bool
    {
        return $this->hasSpecialAccess();
    }

    /**
     * Check if user needs to pay for subscription
     */
    public function needsPayment(): bool
    {
        return !$this->getsFreeAccess();
    }

    /**
     * Get free access reason
     */
    public function getFreeAccessReason(): ?string
    {
        $founderId = $this->getFounderId();
        
        if ($this->id === $founderId) {
            return 'Network Founder - Free Access Granted';
        }
        
        if ($this->referrer_id === $founderId) {
            return 'Direct Referral of Network Founder - Free Access Granted';
        }
        
        return null;
    }

    /**
     * Get active subscription
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->where('end_date', '>', now());
    }

    /**
     * Get all subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get language preference
     */
    public function languagePreference()
    {
        return $this->hasOne(UserLanguagePreference::class);
    }

    /**
     * Check if user has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Check if user is in grace period (7 days after expiry)
     */
    public function isInGracePeriod(): bool
    {
        $lastActiveSubscription = $this->subscriptions()
            ->where('status', 'active')
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$lastActiveSubscription) {
            return false;
        }

        $gracePeriodEnd = $lastActiveSubscription->end_date->addDays(7);
        return now()->isBetween($lastActiveSubscription->end_date, $gracePeriodEnd);
    }

    /**
     * Get subscription status
     */
    public function getSubscriptionStatus(): string
    {
        if ($this->hasActiveSubscription()) {
            return 'active';
        }

        if ($this->isInGracePeriod()) {
            return 'grace';
        }

        return 'inactive';
    }

    /**
     * Check if user has made any completed payments
     */
    public function hasPaid(): bool
    {
        return $this->transactions()
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Check if user has active payment status (has paid and has active subscription)
     */
    public function hasActivePaymentStatus(): bool
    {
        return $this->hasPaid() && $this->hasActiveSubscription();
    }

    /**
     * Get payment status for referral display
     */
    public function getPaymentStatus(): string
    {
        if ($this->hasActiveSubscription()) {
            return 'active';
        }
        
        if ($this->hasPaid()) {
            return 'expired';
        }
        
        return 'unpaid';
    }

    /**
     * Get days until subscription expires
     */
    public function getDaysUntilExpiry(): int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return 0;
        }

        return $subscription->daysUntilExpiry();
    }

    /**
     * Check if subscription expires soon (within 7 days)
     */
    public function subscriptionExpiresSoon(): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return false;
        }

        return $subscription->expiresSoon();
    }

    /**
     * Generate referral link using referral code
     */
    public function getReferralLink(): string
    {
        return url('/register?ref=' . urlencode($this->referral_code));
    }

    /**
     * Get a shorter referral link for sharing
     */
    public function getShortReferralLink(): string
    {
        return url('/ref/' . $this->referral_code);
    }

    /**
     * Validate referral code format
     */
    public static function isValidReferralCodeFormat(string $code): bool
    {
        // Pattern: REF-{USERNAME}-{RANDOM}
        return preg_match('/^REF-[A-Z0-9]{3,6}-[A-Z0-9]{4}$/', $code) === 1;
    }

    /**
     * Generate a unique referral code for the user with special pattern
     * Pattern: REF-{USERNAME}-{RANDOM}
     */
    public function generateReferralCode(): string
    {
        // Clean username for code generation
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->name));
        $username = substr($username, 0, 6); // Limit to 6 characters for better pattern
        
        // If username is too short, use first 3 characters of email
        if (strlen($username) < 3) {
            $emailParts = explode('@', $this->email);
            $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $emailParts[0]));
            $username = substr($username, 0, 6);
        }
        
        // Ensure minimum length
        if (strlen($username) < 3) {
            $username = 'USER' . substr($this->id, 0, 3);
        }
        
        $username = strtoupper($username);
        
        // Generate code with pattern: REF-{USERNAME}-{RANDOM}
        do {
            $randomSuffix = strtoupper(substr(md5($this->id . time() . rand()), 0, 4));
            $code = "REF-{$username}-{$randomSuffix}";
        } while ($this->referralCodeExists($code));
        
        return $code;
    }

    /**
     * Check if referral code already exists
     */
    private function referralCodeExists(string $code): bool
    {
        return static::where('referral_code', $code)->where('id', '!=', $this->id)->exists();
    }

    /**
     * Find user by referral code with validation
     */
    public static function findByReferralCode(string $referralCode): ?User
    {
        // Validate format first
        if (!static::isValidReferralCodeFormat($referralCode)) {
            return null;
        }
        
        return static::where('referral_code', $referralCode)->first();
    }

    /**
     * Find user by referral code (legacy support for old format)
     */
    public static function findByReferralCodeLegacy(string $referralCode): ?User
    {
        return static::where('referral_code', $referralCode)->first();
    }

    /**
     * Boot method to auto-generate referral code
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = $user->generateReferralCode();
            }
        });
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        \Log::info('=== USER SEND EMAIL VERIFICATION NOTIFICATION START ===', [
            'user_id' => $this->id,
            'email' => $this->email,
            'timestamp' => now()
        ]);

        try {
            $this->notify(new EmailVerificationNotification);
            
            \Log::info('Email verification notification sent successfully', [
                'user_id' => $this->id,
                'email' => $this->email,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send email verification notification', [
                'user_id' => $this->id,
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);
            
            throw $e;
        }
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        \Log::info('=== USER SEND PASSWORD RESET NOTIFICATION START ===', [
            'user_id' => $this->id,
            'email' => $this->email,
            'token' => $token,
            'timestamp' => now()
        ]);

        try {
            $this->notify(new PasswordResetNotification($token));
            
            \Log::info('Password reset notification sent successfully', [
                'user_id' => $this->id,
                'email' => $this->email,
                'token' => $token,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset notification', [
                'user_id' => $this->id,
                'email' => $this->email,
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);
            
            throw $e;
        }
    }
}
