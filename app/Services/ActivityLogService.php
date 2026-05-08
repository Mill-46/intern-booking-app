<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function log(
        Request $request,
        string $action,
        string $description,
        ?int $userId = null,
        array $metadata = []
    ): void {
        try {
            ActivityLog::create([
                'user_id' => $userId ?? $request->user()?->id,
                'action' => $action,
                'description' => $description,
                'ip_address' => $request->ip(),
                'metadata' => $metadata === [] ? null : $metadata,
            ]);
        } catch (Throwable $throwable) {
            Log::warning('Failed to persist activity log.', [
                'action' => $action,
                'description' => $description,
                'error' => $throwable->getMessage(),
            ]);
        }
    }
}
