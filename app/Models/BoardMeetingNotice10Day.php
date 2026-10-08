<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMeetingNotice10Day extends Model
{
    use HasFactory;
    protected $table = 'board_meeting_notices_10days';

    protected $fillable = [
        'meeting_id',
        'notice_text',
        'agenda_order',
        'reference_number',
        'reference',
        'submission_date',
        'published_at',
        'created_at',
        'updated_at',
        'document_path',
    ];

    protected $casts = [
        'agenda_order' => 'array',
        'submission_date' => 'date',
        'published_at' => 'datetime',
    ];

    // Relationship: A 10-day notice belongs to a meeting
    public function meeting()
    {
        return $this->belongsTo(BoardMeeting::class, 'meeting_id');
    }
}
