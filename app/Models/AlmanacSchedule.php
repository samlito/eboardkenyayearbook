<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlmanacSchedule extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];
    public function financialyear()
    {
        return $this->belongsTo(FinancialYear::class, 'fy_id');
    }

    public function meeting()
    {
        return $this->belongsTo(BoardCommittee::class, 'board_committee_id');
    }

    public function almanac()
    {
        return $this->belongsTo(Almanac::class, 'almanac_id');
    }

    public function boardCommittee()
    {
        return $this->belongsTo(BoardCommittee::class);
    }

    public function versions()
    {
        return $this->hasMany(AlmanacScheduleVersion::class);
    }
//    public function boardMeeting()
//    {
//        return $this->belongsTo(BoardMeeting::class, 'meeting_id');
//    }
    public function boardMeeting()
    {
        return $this->hasOne(\App\Models\BoardMeeting::class, 'almanac_schedule_id');
    }
// App\Models\AlmanacSchedule.php
    protected $casts = [
        'meeting_date' => 'date', // or 'datetime'
    ];

}
