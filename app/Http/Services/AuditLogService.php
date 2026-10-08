<?php

namespace App\Http\Services;

use App\Models\AuditLog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{

    public static function log(Request $request, $action, $table, $recordId = null, $oldValues = null, $newValues = null)
    {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'table_name' => $table,
            'record_id'  => $recordId,
            'action'     => $action,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'request_ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
    }
}

//public static function log0($action, $table, $recordId = null, $oldValues = null, $newValues = null)
//{
//    AuditLog::create([
//        'user_id'    => Auth::id(),
//        'table_name' => $table,
//        'record_id'  => $recordId,
//        'action'     => $action,
//        'old_values' => $oldValues ? json_encode($oldValues) : null,
//        'new_values' => $newValues ? json_encode($newValues) : null,
//        'request_ip' => Request::ip(),
//        'user_agent' => Request::header('User-Agent'),
//    ]);
//}
