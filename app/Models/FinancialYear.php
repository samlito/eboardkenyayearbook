<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    use HasFactory;

    protected $fillable = ['year', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function almanacs()
    {
        return $this->hasMany(\App\Models\Almanac::class, 'fy_id');
    }
}
