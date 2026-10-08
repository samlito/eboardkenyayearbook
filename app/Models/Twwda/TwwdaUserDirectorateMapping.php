<?php

namespace App\Models\Twwda;

use App\Models\User;
use App\Models\Directorate;
use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaUserDirectorateMapping extends Model
{
    use HasFactory;

    protected $table = 'twwda_user_directorate_mappings';

    protected $fillable = [
        'user_id',
        'directorate_id',
        'division_id',
        'role_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDirectors($query)
    {
        return $query->where('role_type', 'director');
    }

    public function scopeDeputyDirectors($query)
    {
        return $query->where('role_type', 'deputy_director');
    }
}
