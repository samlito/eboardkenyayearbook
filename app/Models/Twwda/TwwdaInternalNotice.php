<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaInternalNotice extends Model
{
    use HasFactory;

    protected $table = 'twwda_internal_notices';

    protected $fillable = [
        'meeting_id',
        'reference_number',
        'reference_date',
        'subject',
        'body',
        'agenda_items',
        'meeting_venue',
        'meeting_date',
        'meeting_time',
        'status',
        'created_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'is_special_approval',
        'special_approval_reason',
        'ceo_signature',
        'version',
    ];

    protected $casts = [
        'reference_date' => 'date',
        'meeting_date' => 'date',
        'agenda_items' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_special_approval' => 'boolean',
    ];

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function comments()
    {
        return $this->hasMany(TwwdaInternalNoticeComment::class, 'notice_id')->orderBy('created_at', 'desc');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'special_approved']);
    }

    public function isApproved()
    {
        return in_array($this->status, ['approved', 'special_approved']);
    }
}
