<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Commission;
use App\Models\User;
use App\Models\PayoutBatch;
use App\Models\PayoutBatchItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessMonthlyCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:process-monthly {--month= : Specific month to process (Y-m format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process monthly commissions for eligible users (1st-10th of each month)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->option('month') ?: now()->subMonth()->format('Y-m');
        
        $this->info("Processing commissions for month: {$month}");
        
        try {
            DB::beginTransaction();
            
            // Get all eligible commissions for the month
            $eligibleCommissions = Commission::where('month', $month)
                ->where('eligibility', 'eligible')
                ->where('payout_status', 'pending')
                ->with(['earner', 'sourceUser'])
                ->get();
            
            if ($eligibleCommissions->isEmpty()) {
                $this->warn("No eligible commissions found for month: {$month}");
                return 0;
            }
            
            $this->info("Found {$eligibleCommissions->count()} eligible commissions");
            
            // Group commissions by earner
            $commissionsByEarner = $eligibleCommissions->groupBy('earner_user_id');
            
            // Create payout batch
            $payoutBatch = PayoutBatch::create([
                'month' => $month,
                'status' => 'processing',
                'total_amount' => $eligibleCommissions->sum('amount'),
                'total_commissions' => $eligibleCommissions->count(),
                'created_by_admin_id' => 1, // System admin
                'notes' => "Monthly commission processing for {$month}"
            ]);
            
            $this->info("Created payout batch: {$payoutBatch->id}");
            
            // Process each earner's commissions
            foreach ($commissionsByEarner as $earnerId => $commissions) {
                $earner = User::find($earnerId);
                $totalAmount = $commissions->sum('amount');
                
                // Create payout batch item
                $payoutItem = PayoutBatchItem::create([
                    'payout_batch_id' => $payoutBatch->id,
                    'user_id' => $earnerId,
                    'amount' => $totalAmount,
                    'commission_count' => $commissions->count(),
                    'status' => 'pending',
                    'notes' => "Monthly payout for {$month}"
                ]);
                
                // Update commission status
                $commissionIds = $commissions->pluck('id')->toArray();
                Commission::whereIn('id', $commissionIds)
                    ->update([
                        'payout_status' => 'processing',
                        'payout_batch_item_id' => $payoutItem->id
                    ]);
                
                $this->line("Processed {$earner->email}: {$totalAmount} USDT ({$commissions->count()} commissions)");
            }
            
            // Update batch status
            $payoutBatch->update(['status' => 'ready']);
            
            DB::commit();
            
            $this->info("Successfully processed {$eligibleCommissions->count()} commissions for month {$month}");
            $this->info("Total payout amount: {$payoutBatch->total_amount} USDT");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process monthly commissions for {$month}: " . $e->getMessage());
            $this->error("Failed to process commissions: " . $e->getMessage());
            return 1;
        }
    }
}

