<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function log(
        Request $request,
        string $action,
        string $description,
        ?int $userId = null,
        array $metadata = []
    ): void {
        ActivityLog::create([
            'user_id' => $userId ?? $request->user()?->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'metadata' => $metadata === [] ? null : $metadata,
        ]);
    }
}
