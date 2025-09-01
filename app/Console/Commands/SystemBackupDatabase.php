<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemBackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:backup-database {--compress : Compress backup files} {--keep-days=30 : Number of days to keep backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');
        
        try {
            $compress = $this->option('compress');
            $keepDays = (int) $this->option('keep-days');
            
            // Get database configuration
            $connection = config('database.default');
            $config = config("database.connections.{$connection}");
            
            $this->line("Backing up database: {$config['database']}");
            $this->line("Connection: {$connection}");
            
            // Create backup filename
            $timestamp = now()->format('Y-m-d-H-i-s');
            $filename = "backup-{$connection}-{$timestamp}.sql";
            $backupPath = storage_path('app/backups/' . $filename);
            
            // Ensure backup directory exists
            $backupDir = dirname($backupPath);
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
                $this->line("Created backup directory: {$backupDir}");
            }
            
            // Create backup based on database type
            $success = $this->createBackup($config, $backupPath, $connection);
            
            if (!$success) {
                throw new \Exception('Database backup creation failed');
            }
            
            // Compress backup if requested
            if ($compress) {
                $backupPath = $this->compressBackup($backupPath);
                $filename = basename($backupPath);
            }
            
            // Get backup file size
            $fileSize = $this->formatBytes(filesize($backupPath));
            $this->info("Database backup created successfully: {$filename} ({$fileSize})");
            
            // Clean old backups
            $cleanedCount = $this->cleanOldBackups($backupDir, $keepDays);
            if ($cleanedCount > 0) {
                $this->line("Cleaned up {$cleanedCount} old backup files");
            }
            
            // Log successful backup
            Log::info('Database backup completed successfully', [
                'filename' => $filename,
                'size' => $fileSize,
                'connection' => $connection,
                'compressed' => $compress
            ]);
            
            $this->info('Database backup completed successfully');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Database backup failed: ' . $e->getMessage());
            Log::error('Database backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
    
    /**
     * Create database backup based on connection type
     */
    private function createBackup(array $config, string $backupPath, string $connection): bool
    {
        switch ($connection) {
            case 'mysql':
                return $this->createMySQLBackup($config, $backupPath);
            
            case 'pgsql':
                return $this->createPostgreSQLBackup($config, $backupPath);
            
            case 'sqlite':
                return $this->createSQLiteBackup($config, $backupPath);
            
            default:
                throw new \Exception("Unsupported database connection: {$connection}");
        }
    }
    
    /**
     * Create MySQL backup using mysqldump
     */
    private function createMySQLBackup(array $config, string $backupPath): bool
    {
        $this->line('Creating MySQL backup using mysqldump...');
        
        // Build mysqldump command
        $command = 'mysqldump';
        $command .= ' --single-transaction';
        $command .= ' --routines';
        $command .= ' --triggers';
        $command .= ' --add-drop-table';
        $command .= ' --create-options';
        $command .= ' --extended-insert';
        $command .= ' --set-charset';
        
        // Add connection parameters
        if (!empty($config['host'])) {
            $command .= ' -h' . escapeshellarg($config['host']);
        }
        
        if (!empty($config['port'])) {
            $command .= ' -P' . escapeshellarg($config['port']);
        }
        
        if (!empty($config['username'])) {
            $command .= ' -u' . escapeshellarg($config['username']);
        }
        
        if (!empty($config['password'])) {
            $command .= ' -p' . escapeshellarg($config['password']);
        }
        
        $command .= ' ' . escapeshellarg($config['database']);
        $command .= ' > ' . escapeshellarg($backupPath);
        
        // Execute command
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->error('mysqldump command failed with return code: ' . $returnCode);
            if (!empty($output)) {
                $this->error('Command output: ' . implode("\n", $output));
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Create PostgreSQL backup using pg_dump
     */
    private function createPostgreSQLBackup(array $config, string $backupPath): bool
    {
        $this->line('Creating PostgreSQL backup using pg_dump...');
        
        // Build pg_dump command
        $command = 'pg_dump';
        $command .= ' --verbose';
        $command .= ' --clean';
        $command .= ' --no-owner';
        $command .= ' --no-privileges';
        
        // Add connection parameters
        if (!empty($config['host'])) {
            $command .= ' -h ' . escapeshellarg($config['host']);
        }
        
        if (!empty($config['port'])) {
            $command .= ' -p ' . escapeshellarg($config['port']);
        }
        
        if (!empty($config['username'])) {
            $command .= ' -U ' . escapeshellarg($config['username']);
        }
        
        if (!empty($config['database'])) {
            $command .= ' -d ' . escapeshellarg($config['database']);
        }
        
        $command .= ' > ' . escapeshellarg($backupPath);
        
        // Set PGPASSWORD environment variable
        if (!empty($config['password'])) {
            putenv('PGPASSWORD=' . $config['password']);
        }
        
        // Execute command
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->error('pg_dump command failed with return code: ' . $returnCode);
            if (!empty($output)) {
                $this->error('Command output: ' . implode("\n", $output));
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Create SQLite backup by copying the database file
     */
    private function createSQLiteBackup(array $config, string $backupPath): bool
    {
        $this->line('Creating SQLite backup by copying database file...');
        
        $databasePath = $config['database'];
        
        if (!file_exists($databasePath)) {
            $this->error("SQLite database file not found: {$databasePath}");
            return false;
        }
        
        // Copy database file
        if (!copy($databasePath, $backupPath)) {
            $this->error("Failed to copy SQLite database file to: {$backupPath}");
            return false;
        }
        
        return true;
    }
    
    /**
     * Compress backup file
     */
    private function compressBackup(string $backupPath): string
    {
        $this->line('Compressing backup file...');
        
        $compressedPath = $backupPath . '.gz';
        
        $command = 'gzip -f ' . escapeshellarg($backupPath);
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->warn('Failed to compress backup file, keeping uncompressed version');
            return $backupPath;
        }
        
        $this->line('Backup compressed successfully');
        return $compressedPath;
    }
    
    /**
     * Clean old backup files
     */
    private function cleanOldBackups(string $backupDir, int $keepDays): int
    {
        $this->line("Cleaning backups older than {$keepDays} days...");
        
        $cutoffTime = now()->subDays($keepDays)->timestamp;
        $cleanedCount = 0;
        
        $files = glob($backupDir . '/*');
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                $filename = basename($file);
                if (unlink($file)) {
                    $this->line("Removed old backup: {$filename}");
                    $cleanedCount++;
                } else {
                    $this->warn("Failed to remove old backup: {$filename}");
                }
            }
        }
        
        return $cleanedCount;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
