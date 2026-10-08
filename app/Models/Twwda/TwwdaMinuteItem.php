<?php

namespace App\Models\Twwda;

use Illuminate\Database\Eloquent\Model;

class TwwdaMinuteItem extends Model
{
    protected $table = 'twwda_minute_items';

    protected $fillable = [
        'meeting_minutes_id',
        'agenda_item_id',
        'minute_number',
        'agenda_title',
        'discussion',
        'comments',
        'resolution',
        'proposer',
        'seconder',
        'display_order',
    ];

    public function meetingMinutes()
    {
        return $this->belongsTo(TwwdaMeetingMinute::class, 'meeting_minutes_id');
    }

    public function agendaItem()
    {
        return $this->belongsTo(TwwdaMeetingAgendaItem::class, 'agenda_item_id');
    }
}
