<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMeetingAssignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'meeting_id',
        'user_id',
        'notified',
    ];

    public function meeting()
    {
        return $this->belongsTo(BoardMeeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
