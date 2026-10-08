<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardPaperVersion extends Model
{
    use HasFactory;


    protected $fillable = [
        'board_paper_id',
        'status',
        'ceo_comments',
        'acted_by',
        'file_path',
        'supporting_docs_path',
    ];

    public function boardPaper()
    {
        return $this->belongsTo(BoardPaper::class);
    }

    public function actedBy()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }
}
