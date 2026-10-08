<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaMeetingPreparation extends Model
{
    use HasFactory;

    protected $table = 'twwda_meeting_preparations';

    protected $fillable = [
        'meeting_id',
        'prepared_by',
        'paper_order',
        'additional_agenda_items',
        'removed_papers',
        'notes',
        'finalized_at',
        'agenda_items_seeded_at',
    ];

    protected $casts = [
        'paper_order' => 'array',
        'additional_agenda_items' => 'array',
        'removed_papers' => 'array',
        'finalized_at' => 'datetime',
        'agenda_items_seeded_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function isFinalized()
    {
        return $this->finalized_at !== null;
    }
}
