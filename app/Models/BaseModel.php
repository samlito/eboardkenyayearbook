<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BaseModel extends Authenticatable
{
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            self::logAudit($model, 'INSERT');
        });

        static::updated(function ($model) {
            self::logAudit($model, 'UPDATE');
        });

        static::deleted(function ($model) {
            self::logAudit($model, 'DELETE');
        });
    }

    private static function logAudit($model, $action)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'table_name' => $model->getTable(),
            'record_id' => $model->id,
            'action' => $action,
            'old_values' => $action === 'UPDATE' ? json_encode($model->getOriginal()) : null,
            'new_values' => $action === 'INSERT' || $action === 'UPDATE' ? json_encode($model->getAttributes()) : null,
            'request_ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }
}
