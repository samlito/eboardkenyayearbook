<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickSignature extends Model
{
    protected $guarded = [];

    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
