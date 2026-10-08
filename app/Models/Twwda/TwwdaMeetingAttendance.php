<?php

namespace App\Models\Twwda;

use App\Models\User;
use App\Models\BoardMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaMeetingAttendance extends Model
{
    use HasFactory;

    protected $table = 'twwda_meeting_attendance';

    protected $fillable = [
        'meeting_id',
        'board_member_id',
        'user_id',
        'attendee_name',
        'attendee_designation',
        'attendance_type',
        'joined_at',
        'is_present',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'is_present' => 'boolean',
    ];

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function boardMember()
    {
        return $this->belongsTo(BoardMember::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayNameAttribute()
    {
        if ($this->boardMember) return $this->boardMember->name ?? $this->boardMember->first_name . ' ' . $this->boardMember->last_name;
        if ($this->user) return $this->user->name;
        return $this->attendee_name;
    }
}
