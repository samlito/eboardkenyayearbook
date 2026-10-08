<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaStickyNote extends Model
{
    use HasFactory;

    protected $table = 'twwda_sticky_notes';

    protected $fillable = [
        'board_paper_id',
        'user_id',
        'page_number',
        'x_position',
        'y_position',
        'content',
        'color',
    ];

    protected $casts = [
        'x_position' => 'decimal:4',
        'y_position' => 'decimal:4',
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
