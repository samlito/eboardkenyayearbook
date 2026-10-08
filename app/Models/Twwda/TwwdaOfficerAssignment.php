<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaOfficerAssignment extends Model
{
    use HasFactory;

    protected $table = 'twwda_officer_assignments';

    protected $fillable = [
        'meeting_id',
        'user_id',
        'director_id',
        'agenda_item_number',
        'agenda_item_title',
        'notified_at',
        'email_read_at',
        'system_read_at',
        'status',
        'assigned_by',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'email_read_at' => 'datetime',
        'system_read_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function director()
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function boardPapers()
    {
        return $this->hasMany(TwwdaBoardPaper::class, 'assignment_id');
    }

    public function isRead()
    {
        return $this->email_read_at !== null || $this->system_read_at !== null;
    }
}
