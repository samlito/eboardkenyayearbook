<?php

namespace App\Models\Twwda;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaBoardPaperAnnexure extends Model
{
    use HasFactory;

    protected $table = 'twwda_board_paper_annexures';

    protected $fillable = [
        'board_paper_id',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'sort_order',
    ];

    public function boardPaper()
    {
        return $this->belongsTo(TwwdaBoardPaper::class, 'board_paper_id');
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }
}
