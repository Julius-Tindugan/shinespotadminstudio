<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to a file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');
        
        try {
            // Create backup directory if it doesn't exist
            if (!Storage::disk('local')->exists('backups')) {
                Storage::disk('local')->makeDirectory('backups');
            }
            
            // Generate backup filename with timestamp
            $filename = 'shinedb_backup_' . Carbon::now()->format('Y_m_d_His') . '.sql';
            $filepath = storage_path('app/backups/' . $filename);
            
            // Get database connection details from .env
            $dbName = env('DB_DATABASE', 'shinedb');
            $dbUser = env('DB_USERNAME', 'root');
            $dbPass = env('DB_PASSWORD', '');
            $dbHost = env('DB_HOST', '127.0.0.1');
            
            // Build mysqldump command
            $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > {$filepath}";
            
            // For Windows, use double quotes for the file path
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $filepath = str_replace('/', '\\', $filepath);
                $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"{$filepath}\"";
            }
            
            // Execute the command
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                $this->info('Database backup completed successfully.');
                $this->info('Backup saved to: ' . $filepath);
                
                // Remove old backups (keep last 5)
                $this->removeOldBackups();
                
                return 0;
            } else {
                $this->error('Database backup failed.');
                $this->error('Return code: ' . $returnVar);
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Database backup failed with error: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Remove old backups, keeping only the last 5
     */
    protected function removeOldBackups()
    {
        $files = Storage::disk('local')->files('backups');
        
        // Filter only SQL files
        $sqlFiles = array_filter($files, function($file) {
            return str_ends_with($file, '.sql');
        });
        
        // Sort by name (which includes timestamp)
        rsort($sqlFiles);
        
        // Keep only the latest 5
        $filesToKeep = array_slice($sqlFiles, 0, 5);
        
        // Delete the rest
        foreach ($sqlFiles as $file) {
            if (!in_array($file, $filesToKeep)) {
                Storage::disk('local')->delete($file);
                $this->info('Deleted old backup: ' . $file);
            }
        }
    }
}
