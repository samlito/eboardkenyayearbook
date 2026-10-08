<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaBoardNoticeComment extends Model
{
    use HasFactory;

    protected $table = 'twwda_board_notice_comments';

    protected $fillable = [
        'board_notice_id',
        'user_id',
        'comment',
        'action',
        'iteration',
    ];

    public function boardNotice()
    {
        return $this->belongsTo(TwwdaBoardNotice::class, 'board_notice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
