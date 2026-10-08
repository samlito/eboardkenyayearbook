<?php

namespace App\Models\Twwda;

use App\Models\User;
use App\Models\BoardCommittee;
use App\Models\AlmanacSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaMeeting extends Model
{
    use HasFactory;

    protected $table = 'twwda_meetings';

    protected $fillable = [
        'almanac_schedule_id',
        'board_committee_id',
        'title',
        'meeting_date',
        'meeting_time',
        'venue',
        'reference_number',
        'status',
        'initiated_by',
        'initiated_at',
        'current_stage',
        'is_started',
        'started_at',
        'started_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'initiated_at' => 'datetime',
        'started_at' => 'datetime',
        'is_started' => 'boolean',
    ];

    // Relationships
    public function almanacSchedule()
    {
        return $this->belongsTo(AlmanacSchedule::class);
    }

    public function boardCommittee()
    {
        return $this->belongsTo(BoardCommittee::class);
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function starter()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function internalNotice()
    {
        return $this->hasOne(TwwdaInternalNotice::class, 'meeting_id');
    }

    public function officerAssignments()
    {
        return $this->hasMany(TwwdaOfficerAssignment::class, 'meeting_id');
    }

    public function boardPapers()
    {
        return $this->hasMany(TwwdaBoardPaper::class, 'meeting_id');
    }

    public function preparation()
    {
        return $this->hasOne(TwwdaMeetingPreparation::class, 'meeting_id');
    }

    public function attendance()
    {
        return $this->hasMany(TwwdaMeetingAttendance::class, 'meeting_id');
    }

    public function conflictDeclarations()
    {
        return $this->hasMany(TwwdaConflictDeclaration::class, 'meeting_id');
    }

    public function boardNotices()
    {
        return $this->hasMany(TwwdaBoardNotice::class, 'meeting_id');
    }

    public function agendaItems()
    {
        return $this->hasMany(TwwdaMeetingAgendaItem::class, 'meeting_id')->orderBy('display_order');
    }

    public function auditLogs()
    {
        return $this->hasMany(TwwdaAuditLog::class, 'meeting_id');
    }

    public function minutes()
    {
        return $this->hasOne(TwwdaMeetingMinute::class, 'meeting_id');
    }

    public function notifications()
    {
        return $this->hasMany(TwwdaNotification::class, 'meeting_id');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('meeting_date', '>=', now()->toDateString())->orderBy('meeting_date');
    }

    public function scopeByCommittee($query, $committeeId)
    {
        return $query->where('board_committee_id', $committeeId);
    }

    // Helpers
    public function getDaysToMeetingAttribute()
    {
        return now()->diffInDays($this->meeting_date, false);
    }

    public function getStatusLabelAttribute()
    {
        return str_replace('_', ' ', ucfirst($this->status));
    }

    public function isAuditCommittee()
    {
        return $this->boardCommittee && strtolower($this->boardCommittee->name) === 'audit';
    }
}
