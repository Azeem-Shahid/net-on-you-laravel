<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduledCommand;
use App\Models\CommandLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CronJobController extends Controller
{
    /**
     * Display the cron job management dashboard
     */
    public function index()
    {
        $businessCommands = $this->getBusinessCommands();
        $systemCommands = $this->getSystemCommands();
        $scheduledCommands = ScheduledCommand::with('latestLog')->get();
        $recentLogs = CommandLog::with('executedByAdmin')
            ->latest('executed_at')
            ->take(20)
            ->get();

        return view('admin.cron-jobs.index', compact(
            'businessCommands',
            'systemCommands',
            'scheduledCommands',
            'recentLogs'
        ));
    }

    /**
     * Get business operations commands (non-system)
     */
    private function getBusinessCommands(): array
    {
        return [
            'subscriptions' => [
                'name' => 'Subscription Management',
                'commands' => [
                    'subscriptions:check-expiry' => [
                        'description' => 'Check for expired subscriptions and send notifications',
                        'frequency' => 'daily',
                        'recommended_time' => '06:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 6 * * * cd /home/netonyou/public_html && php artisan subscriptions:check-expiry >> /dev/null 2>&1'
                    ]
                ]
            ],
            'commissions' => [
                'name' => 'Commission Processing',
                'commands' => [
                    'commissions:check-eligibility' => [
                        'description' => 'Check commission eligibility for all users',
                        'frequency' => 'weekly',
                        'recommended_time' => '06:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 6 * * 1 cd /home/netonyou/public_html && php artisan commissions:check-eligibility >> /dev/null 2>&1'
                    ],
                    'commissions:process-monthly' => [
                        'description' => 'Process monthly commissions for eligible users',
                        'frequency' => 'monthly',
                        'recommended_time' => '00:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 0 1 * * cd /home/netonyou/public_html && php artisan commissions:process-monthly >> /dev/null 2>&1'
                    ],
                    'commissions:re-evaluate-eligibility' => [
                        'description' => 'Re-evaluate commission eligibility for changes',
                        'frequency' => 'daily',
                        'recommended_time' => '04:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 4 * * * cd /home/netonyou/public_html && php artisan commissions:re-evaluate-eligibility >> /dev/null 2>&1'
                    ]
                ]
            ],
            'magazines' => [
                'name' => 'Magazine Management',
                'commands' => [
                    'magazines:release-reminder' => [
                        'description' => 'Send reminder to admins for bimonthly magazine release',
                        'frequency' => 'bimonthly',
                        'recommended_time' => '09:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 9 1 */2 * cd /home/netonyou/public_html && php artisan magazines:release-reminder >> /dev/null 2>&1'
                    ]
                ]
            ],
            'reports' => [
                'name' => 'Report Management',
                'commands' => [
                    'reports:clear-expired' => [
                        'description' => 'Clear expired report cache entries',
                        'frequency' => 'daily',
                        'recommended_time' => '03:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 3 * * * cd /home/netonyou/public_html && php artisan reports:clear-expired >> /dev/null 2>&1'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get system maintenance commands
     */
    private function getSystemCommands(): array
    {
        return [
            'system' => [
                'name' => 'System Maintenance',
                'commands' => [
                    'system:core-maintenance' => [
                        'description' => 'Run comprehensive system core maintenance (all operations)',
                        'frequency' => 'daily',
                        'recommended_time' => '06:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 6 * * * cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1'
                    ],
                    'system:cleanup' => [
                        'description' => 'Clean up old logs, cache, and temporary files',
                        'frequency' => 'weekly',
                        'recommended_time' => '02:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 2 * * 0 cd /home/netonyou/public_html && php artisan system:cleanup >> /dev/null 2>&1'
                    ],
                    'system:health-check' => [
                        'description' => 'Check system health and send alerts',
                        'frequency' => 'daily',
                        'recommended_time' => '02:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 2 * * * cd /home/netonyou/public_html && php artisan system:health-check >> /dev/null 2>&1'
                    ],
                    'system:backup-database' => [
                        'description' => 'Create database backup',
                        'frequency' => 'daily',
                        'recommended_time' => '01:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 1 * * * cd /home/netonyou/public_html && php artisan system:backup-database >> /dev/null 2>&1'
                    ],
                    'system:optimize-cache' => [
                        'description' => 'Optimize application cache and performance',
                        'frequency' => 'weekly',
                        'recommended_time' => '03:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 3 * * 0 cd /home/netonyou/public_html && php artisan system:optimize-cache >> /dev/null 2>&1'
                    ],
                    'system:generate-reports' => [
                        'description' => 'Generate monthly reports and analytics',
                        'frequency' => 'monthly',
                        'recommended_time' => '01:00',
                        'cpanel_url' => 'https://netonyou.com/cpanel',
                        'cron_command' => '0 1 1 * * cd /home/netonyou/public_html && php artisan system:generate-reports >> /dev/null 2>&1'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get cron job setup guide for a specific command
     */
    public function getSetupGuide(Request $request)
    {
        $command = $request->query('command');
        $businessCommands = $this->getBusinessCommands();
        $systemCommands = $this->getSystemCommands();

        // Find the command in business or system commands
        $commandInfo = null;
        $category = null;

        foreach ($businessCommands as $cat => $data) {
            if (isset($data['commands'][$command])) {
                $commandInfo = $data['commands'][$command];
                $category = $data['name'];
                break;
            }
        }

        if (!$commandInfo) {
            foreach ($systemCommands as $cat => $data) {
                if (isset($data['commands'][$command])) {
                    $commandInfo = $data['commands'][$command];
                    $category = $data['name'];
                    break;
                }
            }
        }

        if (!$commandInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Command not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'command' => $command,
            'category' => $category,
            'info' => $commandInfo,
            'setup_instructions' => $this->getSetupInstructions($command, $commandInfo)
        ]);
    }

    /**
     * Get setup instructions for a command
     */
    private function getSetupInstructions(string $command, array $commandInfo): array
    {
        return [
            'cpanel_steps' => [
                '1. Log into cPanel' => $commandInfo['cpanel_url'],
                '2. Navigate to Advanced → Cron Jobs' => $commandInfo['cpanel_url'] . '/cron',
                '3. Add New Cron Job' => 'Click "Add New Cron Job" button',
                '4. Set Common Settings' => "Select \"{$commandInfo['frequency']}\" frequency",
                '5. Set Custom Timing' => $this->getCronTiming($commandInfo['frequency'], $commandInfo['recommended_time']),
                '6. Enter Command' => $commandInfo['cron_command'],
                '7. Save' => 'Click "Add New Cron Job" to save'
            ],
            'manual_setup' => [
                'ssh_access' => 'If you have SSH access, you can add directly to crontab:',
                'command' => "crontab -e",
                'add_line' => $commandInfo['cron_command']
            ],
            'testing' => [
                'test_command' => "cd /home/netonyou/public_html && php artisan {$command}",
                'check_logs' => "tail -f /home/netonyou/public_html/storage/logs/laravel.log | grep \"{$command}\"",
                'admin_panel' => 'https://netonyou.com/admin/command-scheduler'
            ]
        ];
    }

    /**
     * Get cron timing string
     */
    private function getCronTiming(string $frequency, string $time): string
    {
        $parts = explode(':', $time);
        $hour = $parts[0];
        $minute = $parts[1];

        switch ($frequency) {
            case 'daily':
                return "Minute: {$minute}, Hour: {$hour}, Day: *, Month: *, Weekday: *";
            case 'weekly':
                return "Minute: {$minute}, Hour: {$hour}, Day: *, Month: *, Weekday: 1 (Monday)";
            case 'monthly':
                return "Minute: {$minute}, Hour: {$hour}, Day: 1, Month: *, Weekday: *";
            case 'bimonthly':
                return "Minute: {$minute}, Hour: {$hour}, Day: 1, Month: */2, Weekday: *";
            default:
                return "Minute: {$minute}, Hour: {$hour}, Day: *, Month: *, Weekday: *";
        }
    }

    /**
     * Run a business command manually
     */
    public function runBusinessCommand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid command specified'
            ], 400);
        }

        $commandName = $request->command;
        $startTime = microtime(true);

        try {
            // Run the command
            $exitCode = Artisan::call($commandName);
            $output = Artisan::output();
            $executionTime = round((microtime(true) - $startTime) * 1000);

            // Log the execution
            $log = CommandLog::create([
                'command' => $commandName,
                'output' => $output,
                'status' => $exitCode === 0 ? 'success' : 'failed',
                'executed_by_admin_id' => Auth::id(),
                'executed_at' => now(),
                'error_message' => $exitCode !== 0 ? 'Command exited with code: ' . $exitCode : null,
                'execution_time_ms' => $executionTime
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Business command executed successfully',
                'log' => $log,
                'output' => $output,
                'execution_time' => $executionTime
            ]);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000);
            
            // Log the error
            $log = CommandLog::create([
                'command' => $commandName,
                'output' => null,
                'status' => 'failed',
                'executed_by_admin_id' => Auth::id(),
                'executed_at' => now(),
                'error_message' => $e->getMessage(),
                'execution_time_ms' => $executionTime
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Business command failed: ' . $e->getMessage(),
                'log' => $log,
                'execution_time' => $executionTime
            ], 500);
        }
    }

    /**
     * Get command execution history
     */
    public function getCommandHistory(Request $request)
    {
        $command = $request->query('command');
        $limit = $request->query('limit', 50);

        $query = CommandLog::with('executedByAdmin')
            ->latest('executed_at');

        if ($command) {
            $query->where('command', $command);
        }

        $logs = $query->take($limit)->get();

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'total' => $logs->count()
        ]);
    }

    /**
     * Get cron job status and statistics
     */
    public function getStatus()
    {
        $businessCommands = $this->getBusinessCommands();
        $systemCommands = $this->getSystemCommands();
        
        $stats = [
            'total_commands' => 0,
            'business_commands' => 0,
            'system_commands' => 0,
            'scheduled_commands' => 0,
            'active_commands' => 0,
            'last_24h_executions' => 0
        ];

        // Count business commands
        foreach ($businessCommands as $category) {
            $stats['business_commands'] += count($category['commands']);
            $stats['total_commands'] += count($category['commands']);
        }

        // Count system commands
        foreach ($systemCommands as $category) {
            $stats['system_commands'] += count($category['commands']);
            $stats['total_commands'] += count($category['commands']);
        }

        // Get scheduled commands stats
        $scheduledCommands = ScheduledCommand::all();
        $stats['scheduled_commands'] = $scheduledCommands->count();
        $stats['active_commands'] = $scheduledCommands->where('status', 'active')->count();

        // Get last 24h executions
        $stats['last_24h_executions'] = CommandLog::where('executed_at', '>=', now()->subDay())->count();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'business_categories' => array_keys($businessCommands),
            'system_categories' => array_keys($systemCommands)
        ]);
    }
}
