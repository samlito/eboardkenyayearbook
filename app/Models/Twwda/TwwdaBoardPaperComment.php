<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaBoardPaperComment extends Model
{
    use HasFactory;

    protected $table = 'twwda_board_paper_comments';

    protected $fillable = [
        'board_paper_id',
        'user_id',
        'comment',
        'action',
        'reviewer_role',
        'iteration',
    ];

    public function boardPaper()
    {
        return $this->belongsTo(TwwdaBoardPaper::class, 'board_paper_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
