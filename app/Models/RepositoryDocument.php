<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepositoryDocument extends Model
{
    protected $fillable = [
        'category_id', 'title', 'description', 'file_path', 'file_name',
        'file_type', 'file_size', 'visibility', 'is_active', 'uploaded_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function accessRules()
    {
        return $this->hasMany(RepositoryDocumentAccess::class, 'document_id');
    }

    public function committeeAccess()
    {
        return $this->hasMany(RepositoryDocumentAccess::class, 'document_id')
            ->whereNotNull('board_committee_id');
    }

    public function memberAccess()
    {
        return $this->hasMany(RepositoryDocumentAccess::class, 'document_id')
            ->whereNotNull('board_member_id');
    }

    public function isAccessibleBy(BoardMember $member): bool
    {
        if ($this->visibility === 'all') {
            return true;
        }

        if ($this->visibility === 'committee') {
            $memberCommitteeIds = $member->committees()->pluck('board_committees.id')->toArray();
            return $this->accessRules()
                ->whereIn('board_committee_id', $memberCommitteeIds)
                ->exists();
        }

        if ($this->visibility === 'specific') {
            return $this->accessRules()
                ->where('board_member_id', $member->id)
                ->exists();
        }

        return false;
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
