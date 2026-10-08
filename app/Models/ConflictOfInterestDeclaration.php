<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConflictOfInterestDeclaration extends Model
{
    use HasFactory;
    protected $fillable = [
        'meeting_id',
        'board_member_id',
        'conflict_status',
        'details',
        'person_name',
        'person_address',
        'interest_nature',
        'interest_value',
        'interest_ceased_on',
        'remarks',
        'otp_code',
        'otp_verified_at',
    ];

    public function meeting()
    {
        return $this->belongsTo(BoardMeeting::class, 'meeting_id');
    }

    public function boardMember()
    {
        return $this->belongsTo(BoardMember::class, 'board_member_id');
    }
}
