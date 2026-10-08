<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TwwdaMeetingMinute extends Model
{
    protected $table = 'twwda_meeting_minutes';

    protected $fillable = [
        'meeting_id',
        'minutes_title',
        'present_members',
        'absent_members',
        'in_attendance',
        'status',
        'created_by',
    ];

    protected $casts = [
        'present_members' => 'array',
        'absent_members' => 'array',
    ];

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function items()
    {
        return $this->hasMany(TwwdaMinuteItem::class, 'meeting_minutes_id')->orderBy('display_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
