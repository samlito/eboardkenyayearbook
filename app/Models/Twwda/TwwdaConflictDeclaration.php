<?php

namespace App\Models\Twwda;

use App\Models\BoardMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaConflictDeclaration extends Model
{
    use HasFactory;

    protected $table = 'twwda_conflict_declarations';

    protected $fillable = [
        'meeting_id',
        'board_member_id',
        'has_conflict',
        'conflict_details',
        'person_name',
        'person_address',
        'interest_nature',
        'interest_value',
        'interest_ceased_on',
        'remarks',
        'otp_code',
        'otp_sent_at',
        'otp_verified_at',
        'signature',
        'signed_at',
        'ip_address',
    ];

    protected $casts = [
        'has_conflict' => 'boolean',
        'interest_ceased_on' => 'date',
        'otp_sent_at' => 'datetime',
        'otp_verified_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    protected $hidden = ['otp_code'];

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function boardMember()
    {
        return $this->belongsTo(BoardMember::class);
    }

    public function isVerified()
    {
        return $this->otp_verified_at !== null;
    }

    public function isSigned()
    {
        return $this->signed_at !== null;
    }

    public function isComplete()
    {
        return $this->isVerified() && $this->isSigned();
    }
}
