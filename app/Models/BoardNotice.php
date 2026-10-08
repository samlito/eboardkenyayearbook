<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardNotice extends Model
{
    use HasFactory;
    protected $fillable = [
        'meeting_id',
        'content',
        'status',
        'ceo_comments',
        'reference_number','reference_date'
    ];

    public function meeting()
    {
        return $this->belongsTo(BoardMeeting::class, 'meeting_id');
    }
}
