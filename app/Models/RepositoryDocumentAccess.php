<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepositoryDocumentAccess extends Model
{
    protected $table = 'repository_document_access';

    protected $fillable = [
        'document_id', 'board_committee_id', 'board_member_id',
    ];

    public function document()
    {
        return $this->belongsTo(RepositoryDocument::class, 'document_id');
    }

    public function committee()
    {
        return $this->belongsTo(BoardCommittee::class, 'board_committee_id');
    }

    public function member()
    {
        return $this->belongsTo(BoardMember::class, 'board_member_id');
    }
}
