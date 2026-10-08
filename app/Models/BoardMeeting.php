<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMeeting extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'date',
        'venue',
        'type',
        'status',
        'ceo_notified_at',
        'board_committee_id',
        'board_notified_at',
        'almanac_schedule_id',
        'is_started',
        'started_at',
        'started_by',
        'reference_number',
        'final_board_notice_sent_at',
        'final_notice_sent_by',
        'board_notice_sent_by',

    ];


    public function officialNotice()
    {
        return $this->hasOne(BoardMeetingNotice::class, 'meeting_id');
    }
    public function officialNotice10()
    {
        return $this->hasOne(BoardMeetingNotice10Day::class, 'meeting_id');
    }
    public function notice()
    {
        return $this->hasOne(BoardNotice::class, 'meeting_id');
    }
    public function assignments()
    {
        return $this->hasMany(BoardMeetingAssignment::class, 'meeting_id');
    }

    public function papers()
    {
        return $this->hasMany(BoardPaper::class, 'meeting_id');
    }
    public function committee()
    {
        return $this->belongsTo(BoardCommittee::class, 'board_committee_id');
    }
    protected $casts = [
        'board_notified_at' => 'datetime',
        'ceo_notified_at' => 'datetime',
        'final_board_notice_sent_at' => 'datetime',
//        'started_at' => 'datetime',
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime',
        'date' => 'date',  //
    ];
    public function boardCommittee()
    {
        return $this->belongsTo(BoardCommittee::class);
    }
// App\Models\BoardMeeting.php

    public function boardPapers()
    {
        return $this->hasMany(BoardPaper::class, 'meeting_id');
    }
    public function almanacSchedule()
    {
        return $this->hasOne(AlmanacSchedule::class, 'meeting_id');
    }
// In app/Models/BoardMeeting.php

    public function minutes()
    {
        return $this->hasOne(BoardMeetingMinute::class, 'meeting_id');
    }
// BoardMeeting.php
    public function conflictDeclarations()
    {
        return $this->hasMany(ConflictOfInterestDeclaration::class, 'meeting_id');
    }


}
