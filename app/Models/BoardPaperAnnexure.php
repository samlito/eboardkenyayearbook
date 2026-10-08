<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardPaperAnnexure extends Model
{
    use HasFactory;
    protected $fillable = [
        'board_paper_id',
        'title',
        'description',
        'file',
    ];

    public function boardPaper()
    {
        return $this->belongsTo(BoardPaper::class);
    }
}
