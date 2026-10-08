<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardPaper extends Model
{
    use HasFactory;
    protected $fillable = [
        'meeting_id',
        'user_id',
        'title',
        'file',
        'supporting_docs',
        'status',
        'ceo_comments',
        'ceo_notified_at'
    ];

    public function meeting()
    {
        return $this->belongsTo(BoardMeeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function annexures()
    {
        return $this->hasMany(BoardPaperAnnexure::class);
    }

}
