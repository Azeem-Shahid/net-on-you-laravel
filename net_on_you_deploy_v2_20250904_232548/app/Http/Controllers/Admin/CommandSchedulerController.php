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
use Illuminate\Support\Str;
use Carbon\Carbon;

class CommandSchedulerController extends Controller
{
    /**
     * Display the command scheduler dashboard
     */
    public function index()
    {
        $scheduledCommands = ScheduledCommand::with('latestLog')->get();
        $recentLogs = CommandLog::with('executedByAdmin')
            ->latest('executed_at')
            ->take(20)
            ->get();
        
        $availableCommands = ScheduledCommand::getAvailableCommands();
        $frequencyOptions = ScheduledCommand::getFrequencyOptions();
        $statusOptions = ScheduledCommand::getStatusOptions();

        return view('admin.command-scheduler.index', compact(
            'scheduledCommands',
            'recentLogs',
            'availableCommands',
            'frequencyOptions',
            'statusOptions'
        ));
    }

    /**
     * Run a command manually
     */
    public function runCommand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string|in:' . implode(',', array_keys(ScheduledCommand::getAvailableCommands()))
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
            $executionTime = round((microtime(true) - $startTime) * 1000); // Convert to milliseconds

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

            // Create or update scheduled command record
            $scheduledCommand = ScheduledCommand::firstOrCreate(
                ['command' => $commandName],
                [
                    'frequency' => 'manual',
                    'status' => 'inactive',
                    'description' => ScheduledCommand::getAvailableCommands()[$commandName] ?? null
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Command executed successfully',
                'log' => $log,
                'output' => $output,
                'execution_time' => $log->getExecutionTimeFormatted()
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

            Log::error('Command execution failed', [
                'command' => $commandName,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Command execution failed: ' . $e->getMessage(),
                'log' => $log
            ], 500);
        }
    }

    /**
     * Schedule a command
     */
    public function scheduleCommand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string|in:' . implode(',', array_keys(ScheduledCommand::getAvailableCommands())),
            'frequency' => 'required|string|in:' . implode(',', array_keys(ScheduledCommand::getFrequencyOptions())),
            'status' => 'required|string|in:' . implode(',', array_keys(ScheduledCommand::getStatusOptions()))
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $scheduledCommand = ScheduledCommand::updateOrCreate(
                ['command' => $request->command],
                [
                    'frequency' => $request->frequency,
                    'status' => $request->status,
                    'description' => ScheduledCommand::getAvailableCommands()[$request->command] ?? null
                ]
            );

            // Calculate next run time
            $scheduledCommand->calculateNextRun();
            $scheduledCommand->save();

            return response()->json([
                'success' => true,
                'message' => 'Command scheduled successfully',
                'scheduled_command' => $scheduledCommand
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to schedule command', [
                'command' => $request->command,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule command: ' . $e->getMessage()
            ], 500);
        }
    }









    /**
     * Run multiple commands in sequence
     */
    public function runMultipleCommands(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commands' => 'required|array|min:1',
            'commands.*' => 'string|in:' . implode(',', array_keys(ScheduledCommand::getAvailableCommands())),
            'type' => 'required|string|in:maintenance,update,cleanup,custom'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $commands = $request->commands;
        $type = $request->type;
        $results = [];
        $totalStartTime = microtime(true);

        try {
            foreach ($commands as $commandName) {
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

                    $results[] = [
                        'command' => $commandName,
                        'status' => $exitCode === 0 ? 'success' : 'failed',
                        'output' => $output,
                        'execution_time' => $executionTime,
                        'log_id' => $log->id
                    ];

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

                    $results[] = [
                        'command' => $commandName,
                        'status' => 'failed',
                        'output' => null,
                        'error' => $e->getMessage(),
                        'execution_time' => $executionTime,
                        'log_id' => $log->id
                    ];
                }
            }

            $totalExecutionTime = round((microtime(true) - $totalStartTime) * 1000);

            // Log the batch execution
            Log::info('Multiple commands executed', [
                'type' => $type,
                'commands' => $commands,
                'total_execution_time' => $totalExecutionTime,
                'admin_id' => Auth::id(),
                'results' => $results
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Multiple commands executed successfully',
                'type' => $type,
                'total_execution_time' => $totalExecutionTime,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to execute multiple commands', [
                'type' => $type,
                'commands' => $commands,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to execute multiple commands: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get predefined command groups for batch execution
     */
    public function getCommandGroups()
    {
        $groups = [
            'maintenance' => [
                'name' => 'System Maintenance',
                'description' => 'Complete system maintenance and cleanup',
                'commands' => [
                    'system:cleanup',
                    'system:health-check',
                    'system:optimize-cache',
                    'system:clear-expired-reports'
                ]
            ],
            'update' => [
                'name' => 'System Update',
                'description' => 'Update system data and generate reports',
                'commands' => [
                    'system:generate-reports',
                    'system:backup-database',
                    'subscriptions:check-expiry',
                    'commissions:check-eligibility'
                ]
            ],
            'cleanup' => [
                'name' => 'Deep Cleanup',
                'description' => 'Aggressive system cleanup and optimization',
                'commands' => [
                    'system:cleanup',
                    'system:clear-expired-reports',
                    'system:optimize-cache'
                ]
            ],
            'business' => [
                'name' => 'Business Operations',
                'description' => 'Essential business logic commands',
                'commands' => [
                    'subscriptions:check-expiry',
                    'commissions:check-eligibility',
                    'commissions:re-evaluate-eligibility'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'groups' => $groups
        ]);
    }

    /**
     * Get scheduled commands for AJAX calls
     */
    public function getScheduledCommands()
    {
        try {
            $commands = ScheduledCommand::with(['latestLog'])
                ->orderBy('status', 'asc')
                ->orderBy('command', 'asc')
                ->get();

            $formattedCommands = $commands->map(function ($command) {
                return [
                    'id' => $command->id,
                    'command' => $command->command,
                    'description' => $command->description,
                    'frequency' => $command->frequency,
                    'status' => $command->status,
                    'last_run' => $command->latestLog ? $command->latestLog->executed_at->format('Y-m-d H:i:s') : 'Never',
                    'last_status' => $command->latestLog ? $command->latestLog->status : 'N/A',
                    'execution_time' => $command->latestLog ? $command->latestLog->getExecutionTimeFormatted() : 'N/A',
                    'next_run' => $command->getNextRunTime(),
                    'status_badge_class' => $command->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ];
            });

            return response()->json([
                'success' => true,
                'commands' => $formattedCommands
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get scheduled commands', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load scheduled commands: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get command execution statistics
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_commands' => ScheduledCommand::count(),
                'active_commands' => ScheduledCommand::where('status', 'active')->count(),
                'total_executions' => CommandLog::count(),
                'avg_execution_time' => CommandLog::avg('execution_time_ms') ?? 0,
                'success_rate' => CommandLog::count() > 0 
                    ? round((CommandLog::where('status', 'success')->count() / CommandLog::count()) * 100, 2)
                    : 0,
                'recent_failures' => CommandLog::where('status', 'failed')
                    ->where('executed_at', '>=', now()->subDays(7))
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get command statistics', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent command logs
     */
    public function getLogs(Request $request)
    {
        try {
            $limit = $request->get('limit', 20);
            $command = $request->get('command');
            
            $query = CommandLog::with('executedByAdmin')
                ->latest('executed_at');
            
            if ($command) {
                $query->where('command', $command);
            }
            
            $logs = $query->take($limit)->get();

            $formattedLogs = $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'command' => $log->command,
                    'status' => $log->status,
                    'executed_at' => $log->executed_at->format('Y-m-d H:i:s'),
                    'execution_time' => $log->getExecutionTimeFormatted(),
                    'executed_by' => $log->executedByAdmin ? $log->executedByAdmin->name : 'System',
                    'output_preview' => Str::limit($log->output, 100),
                    'has_error' => !empty($log->error_message),
                    'status_badge_class' => $log->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ];
            });

            return response()->json([
                'success' => true,
                'logs' => $formattedLogs
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get command logs', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle command status (active/inactive)
     */
    public function toggleCommandStatus(Request $request, ScheduledCommand $scheduledCommand)
    {
        try {
            $newStatus = $scheduledCommand->status === 'active' ? 'inactive' : 'active';
            $scheduledCommand->update(['status' => $newStatus]);

            Log::info('Command status toggled', [
                'command' => $scheduledCommand->command,
                'old_status' => $scheduledCommand->status,
                'new_status' => $newStatus,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Command status updated successfully',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle command status', [
                'command_id' => $scheduledCommand->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update command status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a scheduled command
     */
    public function deleteCommand(ScheduledCommand $scheduledCommand)
    {
        try {
            $commandName = $scheduledCommand->command;
            $scheduledCommand->delete();

            Log::info('Scheduled command deleted', [
                'command' => $commandName,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Command deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete scheduled command', [
                'command_id' => $scheduledCommand->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete command: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear old command logs
     */
    public function clearLogs(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $cutoffDate = now()->subDays($days);
            
            $deletedCount = CommandLog::where('executed_at', '<', $cutoffDate)->delete();

            Log::info('Command logs cleared', [
                'days' => $days,
                'deleted_count' => $deletedCount,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Cleared {$deletedCount} logs older than {$days} days"
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear command logs', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export command logs to CSV
     */
    public function exportLogs(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $command = $request->get('command');
            
            $query = CommandLog::with('executedByAdmin')
                ->where('executed_at', '>=', now()->subDays($days))
                ->latest('executed_at');
            
            if ($command) {
                $query->where('command', $command);
            }
            
            $logs = $query->get();
            
            $filename = 'command-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($logs) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'Command',
                    'Status',
                    'Executed At',
                    'Execution Time (ms)',
                    'Executed By',
                    'Output',
                    'Error Message'
                ]);

                // Add data rows
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->command,
                        $log->status,
                        $log->executed_at->format('Y-m-d H:i:s'),
                        $log->execution_time_ms,
                        $log->executedByAdmin ? $log->executedByAdmin->name : 'System',
                        Str::limit($log->output, 500),
                        $log->error_message
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Failed to export command logs', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export logs: ' . $e->getMessage()
            ], 500);
        }
    }
}
