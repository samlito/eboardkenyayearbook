<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewComment extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function almanac()
    {

        return $this->belongsTo(Almanac::class);
    }
}
