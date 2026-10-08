<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almanac extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function financialyear()
    {
        return $this->belongsTo(FinancialYear::class, 'fy_id');
    }

    public function latestReviewComment()
    {
        return $this->hasOne(\App\Models\ReviewComment::class, 'almanac_id')->latestOfMany();
    }

    public function reviewComments()
    {
        return $this->hasMany(ReviewComment::class);
    }

    public function schedules()
    {
        return $this->hasMany(AlmanacSchedule::class, 'almanac_id');
    }

    protected $casts = [
        'ceo_approval_date' => 'datetime',
    ];
}
