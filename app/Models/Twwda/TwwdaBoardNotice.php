<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaBoardNotice extends Model
{
    use HasFactory;

    protected $table = 'twwda_board_notices';

    protected $fillable = [
        'meeting_id',
        'notice_type',
        'reference_number',
        'reference_date',
        'notice_date',
        'subject',
        'body',
        'addressees',
        'agenda_items',
        'closing_text',
        'cc',
        'status',
        'created_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'ceo_signature',
        'signatory_name',
        'signatory_title',
        'sent_at',
        'sent_by',
        'reminder_sent_at',
        'reminder_count',
        'document_path',
        'version',
        'parent_notice_id',
        'requires_ceo_approval',
    ];

    protected $casts = [
        'reference_date' => 'date',
        'notice_date' => 'date',
        'addressees' => 'array',
        'agenda_items' => 'array',
        'cc' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'requires_ceo_approval' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function comments()
    {
        return $this->hasMany(TwwdaBoardNoticeComment::class, 'board_notice_id')->orderBy('created_at', 'desc');
    }

    public function parentNotice()
    {
        return $this->belongsTo(self::class, 'parent_notice_id');
    }

    public function replacementNotices()
    {
        return $this->hasMany(self::class, 'parent_notice_id');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['draft']);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function is21Day(): bool
    {
        return $this->notice_type === '21_day';
    }

    public function is7Day(): bool
    {
        return $this->notice_type === '7_day';
    }

    public function isAuditNotice(): bool
    {
        return !$this->requires_ceo_approval;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function isSendable(): bool
    {
        return $this->status === 'approved';
    }

    public function canSendReminder(): bool
    {
        return $this->status === 'sent';
    }

    public function isRescheduled(): bool
    {
        return $this->parent_notice_id !== null;
    }

    /**
     * Get the effective notice date for display.
     */
    public function getEffectiveDateAttribute()
    {
        return $this->notice_date ?? $this->reference_date ?? $this->created_at;
    }

    /**
     * Get status badge colour.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'warning',
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'sent' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Get status icon.
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'mdi-file-edit-outline',
            'submitted' => 'mdi-clock-outline',
            'approved' => 'mdi-check-circle-outline',
            'rejected' => 'mdi-close-circle-outline',
            'sent' => 'mdi-email-check-outline',
            default => 'mdi-help-circle-outline',
        };
    }
}
