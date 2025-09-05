<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckCommissionEligibility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:check-eligibility {--month= : Specific month to check (Y-m format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check commission eligibility for all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->option('month') ?: now()->format('Y-m');
        
        $this->info("Checking commission eligibility for month: {$month}");
        
        try {
            // Get all users with referrals
            $usersWithReferrals = User::whereHas('referrals')
                ->where('role', 'user')
                ->get();
            
            $this->info("Found {$usersWithReferrals->count()} users with referrals");
            
            $eligibleCount = 0;
            $ineligibleCount = 0;
            $updatedCount = 0;
            
            foreach ($usersWithReferrals as $user) {
                try {
                    $eligibility = $user->getCommissionEligibility($month);
                    
                    // Check if eligibility needs updating
                    $currentCommissions = Commission::where('earner_user_id', $user->id)
                        ->where('month', $month)
                        ->get();
                    
                    if ($currentCommissions->isNotEmpty()) {
                        $needsUpdate = false;
                        
                        foreach ($currentCommissions as $commission) {
                            if ($commission->eligibility !== $eligibility) {
                                $needsUpdate = true;
                                break;
                            }
                        }
                        
                        if ($needsUpdate) {
                            // Update commission eligibility
                            Commission::where('earner_user_id', $user->id)
                                ->where('month', $month)
                                ->update(['eligibility' => $eligibility]);
                            
                            $updatedCount++;
                            $this->line("Updated eligibility for {$user->email}: {$eligibility}");
                        }
                    }
                    
                    if ($eligibility === 'eligible') {
                        $eligibleCount++;
                    } else {
                        $ineligibleCount++;
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Failed to check eligibility for user {$user->id}: " . $e->getMessage());
                    $this->error("Error checking user {$user->email}: " . $e->getMessage());
                }
            }
            
            $this->info("Commission eligibility check completed:");
            $this->info("  - Eligible users: {$eligibleCount}");
            $this->info("  - Ineligible users: {$ineligibleCount}");
            $this->info("  - Updated commissions: {$updatedCount}");
            
            return 0;
            
        } catch (\Exception $e) {
            Log::error("Commission eligibility check failed: " . $e->getMessage());
            $this->error("Command failed: " . $e->getMessage());
            return 1;
        }
    }
}

