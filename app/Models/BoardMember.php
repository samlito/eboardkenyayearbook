<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMember extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function assignsalutation()
    {
        return $this->belongsTo(Salutation::class, 'salutation');
    }
    public function committees()
    {
        return $this->belongsToMany(BoardCommittee::class, 'board_committee_members', 'board_member_id', 'board_committee_id');
    }

    public function county()
    {
        return $this->belongsTo(County::class);
    }
//    public function user()
//    {
//        return $this->hasOne(User::class, 'email', 'email');
//    }
// App\Models\BoardMember.php

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
