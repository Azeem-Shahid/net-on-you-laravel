<?php

namespace App\Console\Commands;

use App\Models\ReportCache;
use Illuminate\Console\Command;

class ClearExpiredReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:clear-expired {--hours=24 : Hours after which reports are considered expired}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired report cache entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoff = now()->subHours($hours);
        
        $deletedCount = ReportCache::where('generated_at', '<', $cutoff)->delete();
        
        $this->info("Cleared {$deletedCount} expired report cache entries (older than {$hours} hours).");
        
        return Command::SUCCESS;
    }
}
