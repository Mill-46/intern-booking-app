<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * @param array<string, mixed> $metadata
     */
    protected function logActivity(
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
