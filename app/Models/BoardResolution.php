<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardResolution extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function boardMeeting()
    {
        return $this->belongsTo(BoardMeeting::class);
    }

    public function assignedOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
