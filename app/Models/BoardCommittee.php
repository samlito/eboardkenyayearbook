<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardCommittee extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'is_audit_committee'];

    protected $casts = [
        'is_audit_committee' => 'boolean',
    ];

    public function members()
    {
//        return $this->belongsToMany(BoardMember::class, 'board_committee_member');
        return $this->belongsToMany(BoardMember::class, 'board_committee_members', 'board_committee_id', 'board_member_id');

    }

    public function meetings()
    {
        return $this->hasMany(BoardMeeting::class);
    }
    public function boardMembers()
    {
        return $this->belongsToMany(BoardMember::class, 'board_committee_members', 'board_committee_id', 'board_member_id');
    }

}
