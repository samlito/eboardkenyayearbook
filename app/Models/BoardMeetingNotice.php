<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMeetingNotice extends Model
{
    use HasFactory;
    protected $fillable = ['meeting_id', 'notice_text', 'agenda_order', 'published_at'];

    protected $casts = [
        'agenda_order' => 'array',
        'published_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(BoardMeeting::class);
    }

}
