<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMeetingMinute extends Model
{
    use HasFactory;
    protected $fillable = [
        'meeting_id',
        'minutes_title',
        'present_members',
        'absent_members',
        'in_attendance',
    ];

    protected $casts = [
        'present_members' => 'array',
        'absent_members' => 'array',
    ];

    public function meeting()
    {
        return $this->belongsTo(BoardMeeting::class, 'meeting_id');
    }
    public function items()
    {
        return $this->hasMany(MinuteItem::class, 'board_meeting_minutes_id');
    }
    public function minuteItems()
    {
        return $this->hasMany(MinuteItem::class, 'board_meeting_minutes_id');
    }
    public function membersPresent()
    {
        $ids = json_decode($this->present_members, true);

        if (empty($ids) || !is_array($ids)) {
            return collect(); // Return an empty collection to avoid foreach() errors
        }

        return BoardMember::whereIn('id', $ids)->with('assignsalutation')->get();
    }

}
