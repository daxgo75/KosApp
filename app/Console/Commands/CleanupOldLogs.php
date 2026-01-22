<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CleanupOldLogs extends Command
{
    protected $signature = 'app:cleanup-old-logs {--days=30 : Number of days to retain logs}';
    protected $description = 'Clean up old log files';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $logsPath = storage_path('logs');
        $cutoffTime = now()->subDays($days)->timestamp;

        try {
            if (!is_dir($logsPath)) {
                $this->warn('Logs directory not found');
                return self::SUCCESS;
            }

            $files = glob($logsPath . '/*.log');
            $deletedCount = 0;

            foreach ($files as $file) {
                if (filemtime($file) < $cutoffTime) {
                    unlink($file);
                    $deletedCount++;
                }
            }

            $this->info("✅ Deleted {$deletedCount} old log files (older than {$days} days)");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
