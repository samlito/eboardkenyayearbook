<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinuteItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'board_meeting_minutes_id',
        'minute_number',
        'agenda_title',
        'description',
        'board_comments',
        'board_resolution',
        'proposer_id',
        'seconder_id',
    ];

    public function proposer()
    {
        return $this->belongsTo(BoardMember::class, 'proposer_id');
    }

    public function seconder()
    {
        return $this->belongsTo(BoardMember::class, 'seconder_id');
    }
    public function minute()
    {
        return $this->belongsTo(BoardMeetingMinute::class, 'board_meeting_minutes_id');
    }

}
