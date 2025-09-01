<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\ScheduledCommand;
use App\Models\CommandLog;
use Carbon\Carbon;

class CronController extends Controller
{
    /**
     * Main cron endpoint for cPanel cron jobs
     * This is the URL you'll use in cPanel: https://yoursite.com/cron
     */
    public function index(Request $request)
    {
        // Verify cron secret if provided
        if ($request->has('secret')) {
            $secret = config('app.cron_secret', 'default-secret');
            if ($request->secret !== $secret) {
                return response()->json(['error' => 'Invalid secret'], 403);
            }
        }

        try {
            $results = [];
            $startTime = microtime(true);
            
            // Get all active scheduled commands
            $activeCommands = ScheduledCommand::where('status', 'active')->get();
            
            foreach ($activeCommands as $scheduledCommand) {
                if ($this->shouldRunCommand($scheduledCommand)) {
                    $result = $this->executeCommand($scheduledCommand);
                    $results[] = $result;
                    
                    // Update next run time
                    $scheduledCommand->calculateNextRun();
                    $scheduledCommand->save();
                }
            }
            
            $totalTime = round((microtime(true) - $startTime) * 1000);
            
            Log::info('Cron job executed', [
                'total_commands' => count($results),
                'total_execution_time' => $totalTime,
                'results' => $results
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cron job completed successfully',
                'total_commands' => count($results),
                'total_execution_time' => $totalTime,
                'results' => $results,
                'executed_at' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cron job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute a specific command via web URL
     * URL: https://yoursite.com/cron/command/{command}
     */
    public function executeSpecificCommand($command, Request $request)
    {
        // Verify cron secret if provided
        if ($request->has('secret')) {
            $secret = config('app.cron_secret', 'default-secret');
            if ($request->secret !== $secret) {
                return response()->json(['error' => 'Invalid secret'], 403);
            }
        }

        try {
            $scheduledCommand = ScheduledCommand::where('command', $command)->first();
            
            if (!$scheduledCommand) {
                return response()->json(['error' => 'Command not found'], 404);
            }
            
            $result = $this->executeCommand($scheduledCommand);
            
            return response()->json([
                'success' => true,
                'command' => $command,
                'result' => $result,
                'executed_at' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Specific command execution failed', [
                'command' => $command,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute maintenance commands via web URL
     * URL: https://yoursite.com/cron/maintenance
     */
    public function maintenance(Request $request)
    {
        // Verify cron secret if provided
        if ($request->has('secret')) {
            $secret = config('app.cron_secret', 'default-secret');
            if ($request->secret !== $secret) {
                return response()->json(['error' => 'Invalid secret'], 403);
            }
        }

        $maintenanceCommands = [
            'system:cleanup',
            'system:health-check',
            'system:optimize-cache',
            'system:clear-expired-reports'
        ];

        return $this->executeCommandGroup($maintenanceCommands, 'maintenance');
    }

    /**
     * Execute update commands via web URL
     * URL: https://yoursite.com/cron/update
     */
    public function update(Request $request)
    {
        // Verify cron secret if provided
        if ($request->has('secret')) {
            $secret = config('app.cron_secret', 'default-secret');
            if ($request->secret !== $secret) {
                return response()->json(['error' => 'Invalid secret'], 403);
            }
        }

        $updateCommands = [
            'system:generate-reports',
            'system:backup-database',
            'subscriptions:check-expiry',
            'commissions:check-eligibility'
        ];

        return $this->executeCommandGroup($updateCommands, 'update');
    }

    /**
     * Execute business commands via web URL
     * URL: https://yoursite.com/cron/business
     */
    public function business(Request $request)
    {
        // Verify cron secret if provided
        if ($request->has('secret')) {
            $secret = config('app.cron_secret', 'default-secret');
            if ($request->secret !== $secret) {
                return response()->json(['error' => 'Invalid secret'], 403);
            }
        }

        $businessCommands = [
            'subscriptions:check-expiry',
            'commissions:check-eligibility',
            'commissions:re-evaluate-eligibility'
        ];

        return $this->executeCommandGroup($businessCommands, 'business');
    }

    /**
     * Check if a command should run based on its schedule
     */
    private function shouldRunCommand(ScheduledCommand $scheduledCommand): bool
    {
        if (!$scheduledCommand->next_run_at) {
            return false;
        }

        return $scheduledCommand->next_run_at->isPast();
    }

    /**
     * Execute a single command
     */
    private function executeCommand(ScheduledCommand $scheduledCommand): array
    {
        $startTime = microtime(true);
        
        try {
            $exitCode = Artisan::call($scheduledCommand->command);
            $output = Artisan::output();
            $executionTime = round((microtime(true) - $startTime) * 1000);

            // Log the execution
            $log = CommandLog::create([
                'command' => $scheduledCommand->command,
                'output' => $output,
                'status' => $exitCode === 0 ? 'success' : 'failed',
                'executed_by_admin_id' => null, // System execution
                'executed_at' => now(),
                'error_message' => $exitCode !== 0 ? 'Command exited with code: ' . $exitCode : null,
                'execution_time_ms' => $executionTime
            ]);

            return [
                'command' => $scheduledCommand->command,
                'status' => $exitCode === 0 ? 'success' : 'failed',
                'execution_time' => $executionTime,
                'log_id' => $log->id
            ];

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000);
            
            // Log the error
            $log = CommandLog::create([
                'command' => $scheduledCommand->command,
                'output' => null,
                'status' => 'failed',
                'executed_by_admin_id' => null,
                'executed_at' => now(),
                'error_message' => $e->getMessage(),
                'execution_time_ms' => $executionTime
            ]);

            return [
                'command' => $scheduledCommand->command,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'execution_time' => $executionTime,
                'log_id' => $log->id
            ];
        }
    }

    /**
     * Execute a group of commands
     */
    private function executeCommandGroup(array $commands, string $type): \Illuminate\Http\JsonResponse
    {
        $results = [];
        $totalStartTime = microtime(true);

        try {
            foreach ($commands as $commandName) {
                $startTime = microtime(true);
                
                try {
                    $exitCode = Artisan::call($commandName);
                    $output = Artisan::output();
                    $executionTime = round((microtime(true) - $startTime) * 1000);

                    // Log the execution
                    $log = CommandLog::create([
                        'command' => $commandName,
                        'output' => $output,
                        'status' => $exitCode === 0 ? 'success' : 'failed',
                        'executed_by_admin_id' => null,
                        'executed_at' => now(),
                        'error_message' => $exitCode !== 0 ? 'Command exited with code: ' . $exitCode : null,
                        'execution_time_ms' => $executionTime
                    ]);

                    $results[] = [
                        'command' => $commandName,
                        'status' => $exitCode === 0 ? 'success' : 'failed',
                        'execution_time' => $executionTime,
                        'log_id' => $log->id
                    ];

                } catch (\Exception $e) {
                    $executionTime = round((microtime(true) - $startTime) * 1000);
                    
                    $log = CommandLog::create([
                        'command' => $commandName,
                        'output' => null,
                        'status' => 'failed',
                        'executed_by_admin_id' => null,
                        'executed_at' => now(),
                        'error_message' => $e->getMessage(),
                        'execution_time_ms' => $executionTime
                    ]);

                    $results[] = [
                        'command' => $commandName,
                        'status' => 'failed',
                        'error' => $e->getMessage(),
                        'execution_time' => $executionTime,
                        'log_id' => $log->id
                    ];
                }
            }

            $totalExecutionTime = round((microtime(true) - $totalStartTime) * 1000);

            Log::info("Cron group '{$type}' executed", [
                'type' => $type,
                'commands' => $commands,
                'total_execution_time' => $totalExecutionTime,
                'results' => $results
            ]);

            return response()->json([
                'success' => true,
                'type' => $type,
                'total_commands' => count($commands),
                'total_execution_time' => $totalExecutionTime,
                'results' => $results,
                'executed_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error("Cron group '{$type}' failed", [
                'type' => $type,
                'commands' => $commands,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
