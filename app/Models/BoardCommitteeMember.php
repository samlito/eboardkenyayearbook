<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardCommitteeMember extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'board_committee_members';

    protected $fillable = [
        'board_member_id',
        'board_committee_id',
    ];

    // Relationships

    public function boardMember()
    {
        return $this->belongsTo(BoardMember::class);
    }

    public function boardCommittee()
    {
        return $this->belongsTo(BoardCommittee::class);
    }
}
