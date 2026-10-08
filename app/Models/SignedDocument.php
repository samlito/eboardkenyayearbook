<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignedDocument extends Model
{
    protected $guarded = [];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
