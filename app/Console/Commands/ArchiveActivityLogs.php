<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:archive-activity-logs {--months=12 : Number of months to keep logs}')]
#[Description('Archive activity logs older than specified months')]
class ArchiveActivityLogs extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = (int) $this->option('months');
        $cutoffDate = now()->subMonths($months);

        $this->info("Archiving activity logs older than {$cutoffDate->format('Y-m-d')}...");

        $count = ActivityLog::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No logs to archive.');
            return;
        }

        DB::transaction(function () use ($cutoffDate, $count) {
            DB::insert("
                INSERT INTO activity_log_archives (user_id, action, description, ip_address, metadata, created_at, updated_at, archived_at)
                SELECT user_id, action, description, ip_address, metadata, created_at, updated_at, NOW()
                FROM activity_logs
                WHERE created_at < ?
            ", [$cutoffDate]);

            ActivityLog::where('created_at', '<', $cutoffDate)->delete();
        });

        $this->info("Archived {$count} activity logs.");
    }
}
