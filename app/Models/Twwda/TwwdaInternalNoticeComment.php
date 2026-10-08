<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaInternalNoticeComment extends Model
{
    use HasFactory;

    protected $table = 'twwda_internal_notice_comments';

    protected $fillable = [
        'notice_id',
        'user_id',
        'comment',
        'action',
        'iteration',
    ];

    public function notice()
    {
        return $this->belongsTo(TwwdaInternalNotice::class, 'notice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
