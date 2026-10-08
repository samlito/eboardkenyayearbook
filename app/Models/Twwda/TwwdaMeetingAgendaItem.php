<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaMeetingAgendaItem extends Model
{
    use HasFactory;

    protected $table = 'twwda_meeting_agenda_items';

    protected $fillable = [
        'meeting_id',
        'source',
        'original_notice_text',
        'title',
        'description',
        'item_number',
        'display_order',
        'status',
        'board_paper_id',
        'requires_ceo_approval',
        'pulldown_justification',
        'pulldown_requested_by',
        'pulldown_requested_at',
        'pulldown_approved_by',
        'pulldown_approved_at',
        'added_by',
        'approved_by',
        'approved_at',
        'rejected_reason',
    ];

    protected $casts = [
        'requires_ceo_approval' => 'boolean',
        'pulldown_requested_at' => 'datetime',
        'pulldown_approved_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function boardPaper()
    {
        return $this->belongsTo(TwwdaBoardPaper::class, 'board_paper_id');
    }

    public function addedByUser()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function pulldownRequestedByUser()
    {
        return $this->belongsTo(User::class, 'pulldown_requested_by');
    }

    public function pulldownApprovedByUser()
    {
        return $this->belongsTo(User::class, 'pulldown_approved_by');
    }

    // ── Scopes ─────────────────────────────────────────────

    /**
     * Items visible to board members: active + approved only.
     */
    public function scopeVisibleToBoard($query)
    {
        return $query->whereIn('status', ['active', 'approved']);
    }

    /**
     * Items visible to CS/DDIA: everything except rejected.
     */
    public function scopeVisibleToAdmin($query)
    {
        return $query->where('status', '!=', 'rejected');
    }

    /**
     * Items from the original notice.
     */
    public function scopeFromNotice($query)
    {
        return $query->where('source', 'notice');
    }

    /**
     * Newly added items (CS or DDIA).
     */
    public function scopeNewlyAdded($query)
    {
        return $query->whereIn('source', ['cs_added', 'ddia_added']);
    }

    /**
     * Items awaiting CEO approval (new items or pulldown requests).
     */
    public function scopePendingCeoApproval($query)
    {
        return $query->whereIn('status', ['pending_approval', 'pending_pulldown']);
    }

    /**
     * Items that have been pulled down (hidden from board members).
     */
    public function scopePulledDown($query)
    {
        return $query->where('status', 'pulled_down');
    }

    /**
     * Active items in display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    // ── Helpers ─────────────────────────────────────────────

    /**
     * Is this a notice-sourced (original) agenda item?
     */
    public function isFromNotice(): bool
    {
        return $this->source === 'notice';
    }

    /**
     * Was this item added by Corporation Secretary?
     */
    public function isCSAdded(): bool
    {
        return $this->source === 'cs_added';
    }

    /**
     * Was this item added by DDIA (audit)?
     */
    public function isDDIAAdded(): bool
    {
        return $this->source === 'ddia_added';
    }

    /**
     * Is this item visible to board members?
     */
    public function isVisibleToBoard(): bool
    {
        return in_array($this->status, ['active', 'approved']);
    }

    /**
     * Is this item waiting for CEO approval?
     */
    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Has this item been pulled down?
     */
    public function isPulledDown(): bool
    {
        return $this->status === 'pulled_down';
    }

    /**
     * Is pulldown pending CEO approval?
     */
    public function isPendingPulldown(): bool
    {
        return $this->status === 'pending_pulldown';
    }

    /**
     * Can this item be reordered? (only active/approved items can be reordered)
     */
    public function isReorderable(): bool
    {
        return in_array($this->status, ['active', 'approved']);
    }

    /**
     * Can this item be pulled down?
     */
    public function canBePulledDown(): bool
    {
        return in_array($this->status, ['active', 'approved']);
    }

    /**
     * Has a board paper attached?
     */
    public function hasBoardPaper(): bool
    {
        return $this->board_paper_id !== null;
    }

    /**
     * Status badge colour.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'pending_approval' => 'warning',
            'approved' => 'primary',
            'rejected' => 'danger',
            'pulled_down' => 'secondary',
            'pending_pulldown' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Status icon.
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'active' => 'mdi-check-circle-outline',
            'pending_approval' => 'mdi-clock-outline',
            'approved' => 'mdi-check-decagram',
            'rejected' => 'mdi-close-circle-outline',
            'pulled_down' => 'mdi-eye-off-outline',
            'pending_pulldown' => 'mdi-eye-remove-outline',
            default => 'mdi-help-circle-outline',
        };
    }

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'pending_approval' => 'Pending CEO Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'pulled_down' => 'Pulled Down',
            'pending_pulldown' => 'Pulldown Pending',
            default => ucfirst($this->status),
        };
    }

    /**
     * Human-readable source label.
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'notice' => 'Official Notice',
            'cs_added' => 'Added by CS',
            'ddia_added' => 'Added by DDIA',
            default => ucfirst($this->source),
        };
    }
}
