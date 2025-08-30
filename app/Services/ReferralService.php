<?php

namespace App\Services;

use App\Models\User;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Commission amounts by level
     */
    private const COMMISSION_AMOUNTS = [
        1 => 15.00, // L1: 15 USDT
        2 => 10.00, // L2: 10 USDT
        3 => 5.00,  // L3: 5 USDT
        4 => 1.00,  // L4: 1 USDT
        5 => 1.00,  // L5: 1 USDT
        6 => 1.00,  // L6: 1 USDT
    ];

    /**
     * Build referral upline for a new user
     */
    public function buildReferralUpline(User $newUser, ?User $referrer = null): void
    {
        if (!$referrer) {
            return;
        }

        try {
            DB::beginTransaction();

            // Build 6-level upline
            $currentUser = $referrer;
            $level = 1;

            while ($currentUser && $level <= 6) {
                // Create referral record
                Referral::create([
                    'user_id' => $currentUser->id,
                    'referred_user_id' => $newUser->id,
                    'level' => $level,
                ]);

                // Move up one level
                $currentUser = $currentUser->referrer;
                $level++;
            }

            DB::commit();
            Log::info("Referral upline built for user {$newUser->id} with referrer {$referrer->id}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to build referral upline for user {$newUser->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate commissions for a completed transaction
     */
    public function generateCommissions(Transaction $transaction): void
    {
        if (!$transaction->isCompleted()) {
            return;
        }

        $sourceUser = $transaction->user;
        $month = $transaction->created_at->format('Y-m');

        try {
            DB::beginTransaction();

            // Get all users in the upline (up to 6 levels)
            $uplineUsers = $this->getUplineUsers($sourceUser);

            foreach ($uplineUsers as $level => $earnerUser) {
                // Check eligibility for this month
                $eligibility = $earnerUser->getCommissionEligibility($month);
                
                // Apply special eligibility override for User ID 1's downline
                if ($this->isInUserId1Downline($earnerUser)) {
                    $eligibility = 'eligible';
                }

                // Create commission record
                Commission::create([
                    'earner_user_id' => $earnerUser->id,
                    'source_user_id' => $sourceUser->id,
                    'transaction_id' => $transaction->id,
                    'level' => $level,
                    'amount' => self::COMMISSION_AMOUNTS[$level],
                    'month' => $month,
                    'eligibility' => $eligibility,
                    'payout_status' => 'pending',
                ]);
            }

            DB::commit();
            Log::info("Commissions generated for transaction {$transaction->id} for month {$month}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate commissions for transaction {$transaction->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get upline users for a given user (up to 6 levels)
     */
    private function getUplineUsers(User $user): array
    {
        $uplineUsers = [];
        $currentUser = $user->referrer;
        $level = 1;

        while ($currentUser && $level <= 6) {
            $uplineUsers[$level] = $currentUser;
            $currentUser = $currentUser->referrer;
            $level++;
        }

        return $uplineUsers;
    }

    /**
     * Re-evaluate commission eligibility for a specific month
     */
    public function reEvaluateEligibility(string $month): void
    {
        try {
            DB::beginTransaction();

            // Get all commissions for the month
            $commissions = Commission::where('month', $month)->get();

            foreach ($commissions as $commission) {
                $earner = $commission->earner;
                $newEligibility = $earner->getCommissionEligibility($month);
                
                // Apply special eligibility override for User ID 1's downline
                if ($this->isInUserId1Downline($earner)) {
                    $newEligibility = 'eligible';
                }

                if ($commission->eligibility !== $newEligibility) {
                    $commission->update(['eligibility' => $newEligibility]);
                    Log::info("Commission {$commission->id} eligibility updated from {$commission->eligibility} to {$newEligibility}");
                }
            }

            DB::commit();
            Log::info("Commission eligibility re-evaluation completed for month {$month}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to re-evaluate commission eligibility for month {$month}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if a user is in User ID 1's downline (special eligibility override)
     */
    private function isInUserId1Downline(User $user): bool
    {
        // User ID 1 has special access
        if ($user->id === 1) {
            return true;
        }

        // Check if user is a direct referral of User ID 1
        if ($user->referrer_id === 1) {
            return true;
        }

        // Check if user is deeper in User ID 1's downline
        $currentUser = $user;
        while ($currentUser->referrer_id) {
            if ($currentUser->referrer_id === 1) {
                return true;
            }
            $currentUser = $currentUser->referrer;
        }

        return false;
    }

    /**
     * Get referral statistics for a user
     */
    public function getReferralStats(int $userId): array
    {
        $stats = [];

        for ($level = 1; $level <= 6; $level++) {
            $count = Referral::where('user_id', $userId)
                ->where('level', $level)
                ->count();

            $stats[$level] = $count;
        }

        return $stats;
    }

    /**
     * Get commission earnings for a user
     */
    public function getCommissionEarnings(int $userId): array
    {
        $currentMonth = now()->format('Y-m');

        $monthlyEarnings = Commission::where('earner_user_id', $userId)
            ->where('eligibility', 'eligible')
            ->where('month', $currentMonth)
            ->sum('amount');

        $totalEarnings = Commission::where('earner_user_id', $userId)
            ->where('eligibility', 'eligible')
            ->sum('amount');

        $pendingEarnings = Commission::where('earner_user_id', $userId)
            ->where('eligibility', 'eligible')
            ->where('payout_status', 'pending')
            ->sum('amount');

        return [
            'monthly' => $monthlyEarnings,
            'total' => $totalEarnings,
            'pending' => $pendingEarnings,
        ];
    }

    /**
     * Get detailed commission breakdown for a user
     */
    public function getCommissionBreakdown(int $userId, string $month = null): array
    {
        $month = $month ?: now()->format('Y-m');

        $query = Commission::where('earner_user_id', $userId)
            ->where('month', $month);

        $eligible = $query->clone()->where('eligibility', 'eligible')->sum('amount');
        $ineligible = $query->clone()->where('eligibility', 'ineligible')->sum('amount');
        $pending = $query->clone()->where('payout_status', 'pending')->sum('amount');
        $paid = $query->clone()->where('payout_status', 'paid')->sum('amount');

        return [
            'month' => $month,
            'eligible' => $eligible,
            'ineligible' => $ineligible,
            'pending' => $pending,
            'paid' => $paid,
            'total' => $eligible + $ineligible,
        ];
    }
}
