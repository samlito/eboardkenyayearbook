<?php

namespace App\Models\Twwda;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwwdaBoardPaper extends Model
{
    use HasFactory;

    protected $table = 'twwda_board_papers';

    protected $fillable = [
        'meeting_id',
        'assignment_id',
        'prepared_by',
        'agenda_item_number',
        'paper_number',
        'title',
        'committee_name',
        'paper_type',
        'financial_year',
        'reporting_period',
        'author_department',
        'content',
        'file_path',
        'status',
        'submitted_at',
        'director_approved_by',
        'director_approved_at',
        'ceo_approved_at',
        'ceo_signature',
        'display_order',
        'is_auto_approved',
        'version',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'director_approved_at' => 'datetime',
        'ceo_approved_at' => 'datetime',
        'is_auto_approved' => 'boolean',
    ];

    // ─── Section Definitions ────────────────────────────────

    /**
     * Ordered map of section keys → display labels.
     */
    public static function sectionLabels(): array
    {
        return [
            'background'        => '1.0 Background / Introduction',
            'executive_summary' => '2.0 Executive Summary',
            'body'              => '2.1 Body of the Board Paper',
            'challenges'        => '3.0 Challenges Faced',
            'board_items'       => '4.0 Items for the Board to Note / Decisions / Proposal',
            'committee_request' => '5.0 Request to the Committee',
        ];
    }

    /**
     * Guidance text shown under each section in the form.
     */
    public static function sectionGuidance(): array
    {
        return [
            'background'        => 'Provide background guided by relevant Laws/Circulars. State the purpose of this Board Paper.',
            'executive_summary' => 'Provide highlights or summary of the Annexures of the Board Paper.',
            'body'              => 'Present key issues to the Board. The last item should be the workplan where applicable.',
            'challenges'        => 'Outline challenges encountered during the reporting period.',
            'board_items'       => 'List items the Board should note, decisions required, or proposals for consideration.',
            'committee_request' => 'State what Management is requesting the Committee to do. E.g. "Management requests the Committee to: Consider and Recommend to the Board to approve/adopt/note..."',
        ];
    }

    /**
     * Which sections are required vs optional.
     */
    public static function requiredSections(): array
    {
        return ['background', 'executive_summary', 'body', 'board_items', 'committee_request'];
    }

    // ─── Structured Content Helpers ──────────────────────────

    /**
     * Whether content is stored as structured JSON.
     */
    public function isStructuredContent(): bool
    {
        if (empty($this->content)) {
            return false;
        }
        $decoded = json_decode($this->content, true);
        return is_array($decoded) && !empty($decoded);
    }

    /**
     * Get content as a keyed section array.
     * Falls back to wrapping raw HTML in ['body' => html] for old papers.
     */
    public function getStructuredContentAttribute(): array
    {
        if (empty($this->content)) {
            return [];
        }

        $decoded = json_decode($this->content, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Legacy: raw HTML stored in content column
        return ['body' => $this->content];
    }

    /**
     * Render the full paper content as HTML with section headings.
     */
    public function getRenderedContentAttribute(): string
    {
        if (!$this->isStructuredContent()) {
            return $this->content ?? '';
        }

        $sections = $this->structured_content;
        $labels   = self::sectionLabels();
        $html     = '';

        foreach ($labels as $key => $label) {
            $value = $sections[$key] ?? '';
            // Skip empty or Quill-blank sections
            if (empty($value) || $value === '<p><br></p>' || trim(strip_tags($value)) === '') {
                continue;
            }
            $html .= '<h4 style="color:#005bac; margin-top:24px; margin-bottom:10px; font-weight:700;">' . e($label) . '</h4>';
            $html .= '<div style="margin-bottom:16px;">' . $value . '</div>';
        }

        return $html;
    }

    // ─── Relationships ──────────────────────────────────────

    public function meeting()
    {
        return $this->belongsTo(TwwdaMeeting::class, 'meeting_id');
    }

    public function assignment()
    {
        return $this->belongsTo(TwwdaOfficerAssignment::class, 'assignment_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function directorApprover()
    {
        return $this->belongsTo(User::class, 'director_approved_by');
    }

    public function annexures()
    {
        return $this->hasMany(TwwdaBoardPaperAnnexure::class, 'board_paper_id')->orderBy('sort_order');
    }

    public function comments()
    {
        return $this->hasMany(TwwdaBoardPaperComment::class, 'board_paper_id')->orderBy('created_at', 'desc');
    }

    public function stickyNotes()
    {
        return $this->hasMany(TwwdaStickyNote::class, 'board_paper_id');
    }

    public function agendaItem()
    {
        return $this->hasOne(TwwdaMeetingAgendaItem::class, 'board_paper_id');
    }

    // ─── Scopes ─────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 'ceo_approved');
    }

    public function scopePendingDirectorReview($query)
    {
        return $query->whereIn('status', ['submitted', 'director_review']);
    }

    public function scopePendingCeoReview($query)
    {
        return $query->whereIn('status', ['director_approved', 'ceo_review']);
    }

    public function isApprovedByCeo()
    {
        return $this->status === 'ceo_approved' || $this->is_auto_approved;
    }
}
