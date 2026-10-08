<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'directorate_id',
        'requires_ceo_approval',
    ];

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }
}
