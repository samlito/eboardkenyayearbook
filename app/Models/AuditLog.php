<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
        'request_ip',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
