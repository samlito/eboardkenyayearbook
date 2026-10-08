<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'display_order', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function documents()
    {
        return $this->hasMany(RepositoryDocument::class, 'category_id');
    }

    public function activeDocuments()
    {
        return $this->hasMany(RepositoryDocument::class, 'category_id')->where('is_active', true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
