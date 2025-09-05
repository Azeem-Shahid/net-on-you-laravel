<?php

namespace App\Console\Commands;

use App\Services\ReferralService;
use Illuminate\Console\Command;

class ReEvaluateCommissionEligibility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:re-evaluate-eligibility {month? : Month in YYYY-MM format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-evaluate commission eligibility for a specific month';

    /**
     * Execute the console command.
     */
    public function handle(ReferralService $referralService)
    {
        $month = $this->argument('month') ?: now()->format('Y-m');

        $this->info("Re-evaluating commission eligibility for month: {$month}");

        try {
            $referralService->reEvaluateEligibility($month);
            $this->info("Successfully re-evaluated commission eligibility for month: {$month}");
        } catch (\Exception $e) {
            $this->error("Failed to re-evaluate commission eligibility: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
