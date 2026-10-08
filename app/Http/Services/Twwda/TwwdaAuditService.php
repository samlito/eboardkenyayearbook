<?php

namespace App\Http\Services\Twwda;

use App\Models\Twwda\TwwdaAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class TwwdaAuditService
{
    public static function log(
        string $action,
        string $module,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $meetingId = null
    ): TwwdaAuditLog {
        return TwwdaAuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'meeting_id' => $meetingId,
            'created_at' => now(),
        ]);
    }
}
