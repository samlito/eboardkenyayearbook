<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaAuditLog extends Model
{
    use HasFactory;

    protected $table = 'twwda_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'meeting_id',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function scopeForEntity($query, $type, $id)
    {
        return $query->where('entity_type', $type)->where('entity_id', $id);
    }

    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }
}
