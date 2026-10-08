@extends('admin_dashboard')

@section('admin')
@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;

    $totalPapers = $meetings->sum(fn($m) => $m->boardPapers->count());
    $totalAnnexures = $meetings->flatMap->boardPapers->sum(fn($p) => $p->annexures->count());
    $ceoSignedCount = $meetings->flatMap->boardPapers->filter(fn($p) => $p->ceo_signature)->count();

    $noteColors = [
        'yellow' => '#fff9c4',
        'blue'   => '#dbdbdb',
        'green'  => '#c8e6c9',
        'pink'   => '#f8bbd0',
    ];
@endphp

<div class="bp-papers">

    {{-- ═══════════════════════════════════════════════════════════════
         HERO HEADER
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="bp-hero">
        <div class="bp-hero-pattern"></div>
        <div class="bp-hero-content">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <a href="{{ route('board.portal.dashboard') }}" class="bp-back-link">
                        <i class="mdi mdi-arrow-left"></i> Dashboard
                    </a>
                    <h2 class="bp-hero-title">
                        <i class="mdi mdi-file-document-multiple-outline"></i> Board Papers
                    </h2>
                    <p class="bp-hero-subtitle">CEO-approved papers for your pre-meeting review</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="bp-hero-chip">
                        <i class="mdi mdi-calendar-text"></i>
                        {{ $meetings->count() }} {{ Str::plural('Meeting', $meetings->count()) }}
                    </div>
                    <div class="bp-hero-chip">
                        <i class="mdi mdi-file-document"></i>
                        {{ $totalPapers }} {{ Str::plural('Paper', $totalPapers) }}
                    </div>
                    @if($totalAnnexures > 0)
                        <div class="bp-hero-chip bp-hero-chip-amber">
                            <i class="mdi mdi-paperclip"></i>
                            {{ $totalAnnexures }} {{ Str::plural('Annexure', $totalAnnexures) }}
                        </div>
                    @endif
                    @if($ceoSignedCount > 0)
                        <div class="bp-hero-chip bp-hero-chip-green">
                            <i class="mdi mdi-check-decagram"></i>
                            {{ $ceoSignedCount }} CEO Signed
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         MEETINGS + PAPERS
         ═══════════════════════════════════════════════════════════════ --}}
    @if($meetings->isEmpty())
        <div class="bp-empty-state">
            <div class="bp-empty-icon">
                <i class="mdi mdi-file-document-outline"></i>
            </div>
            <h4>No Papers Available</h4>
            <p>There are no upcoming meetings with approved board papers for your review at this time.</p>
            <a href="{{ route('board.portal.dashboard') }}" class="bp-btn-primary">
                <i class="mdi mdi-arrow-left me-1"></i> Return to Dashboard
            </a>
        </div>
    @else
        <div class="bp-meetings-list">
            @foreach($meetings as $mIdx => $meeting)
                @php
                    $committeeName = optional($meeting->boardCommittee)->name ?? 'Board Committee';
                    $meetingDate = $meeting->meeting_date ? Carbon::parse($meeting->meeting_date) : null;
                    $meetingTime = $meeting->meeting_time ?? null;
                    $paperCount = $meeting->boardPapers->count();
                    $isUpcoming = $meetingDate && $meetingDate->isFuture();
                    $collapseId = 'meeting-collapse-' . $meeting->id;
                @endphp

                <div class="bp-meeting-section">
                    {{-- Meeting Header (collapsible toggle) --}}
                    <div class="bp-meeting-header" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $mIdx === 0 ? 'true' : 'false' }}" role="button">
                        <div class="bp-meeting-date-block">
                            @if($meetingDate)
                                <span class="bp-date-day">{{ $meetingDate->format('d') }}</span>
                                <span class="bp-date-month">{{ $meetingDate->format('M') }}</span>
                                <span class="bp-date-year">{{ $meetingDate->format('Y') }}</span>
                            @else
                                <span class="bp-date-day">?</span>
                                <span class="bp-date-month">TBD</span>
                            @endif
                        </div>
                        <div class="bp-meeting-info">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="bp-committee-chip">
                                    <i class="mdi mdi-shield-account"></i> {{ $committeeName }}
                                </span>
                                @if($isUpcoming)
                                    <span class="bp-upcoming-chip">
                                        <i class="mdi mdi-clock-outline"></i> Upcoming
                                    </span>
                                @endif
                            </div>
                            <h4 class="bp-meeting-title">{{ $meeting->title ?? 'Board Meeting' }}</h4>
                            <div class="bp-meeting-meta">
                                @if($meetingDate)
                                    <span>
                                        <i class="mdi mdi-calendar-clock"></i>
                                        {{ $meetingDate->isoFormat('dddd, MMMM D, YYYY') }}
                                        @if($meetingTime) at {{ Carbon::parse($meetingTime)->format('g:i A') }} @endif
                                    </span>
                                @endif
                                @if($meeting->venue)
                                    <span>
                                        <i class="mdi mdi-map-marker"></i> {{ $meeting->venue }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="bp-meeting-right">
                            <div class="bp-paper-count-badge">
                                <span class="bp-paper-count-number">{{ $paperCount }}</span>
                                <span class="bp-paper-count-label">{{ Str::plural('Paper', $paperCount) }}</span>
                            </div>
                            <i class="mdi mdi-chevron-down bp-collapse-icon"></i>
                        </div>
                    </div>

                    {{-- Papers Grid (collapsible body) --}}
                    <div class="collapse {{ $mIdx === 0 ? 'show' : '' }}" id="{{ $collapseId }}">
                        <div class="bp-papers-grid">
                            @foreach($meeting->boardPapers as $paper)
                                @php
                                    $annexureCount = $paper->annexures->count();
                                    $hasCeoSig = !empty($paper->ceo_signature);
                                @endphp
                                <div class="bp-paper-card" data-bs-toggle="modal" data-bs-target="#paperModal-{{ $paper->id }}" role="button" tabindex="0">
                                    {{-- CEO stamp overlay --}}
                                    @if($hasCeoSig)
                                        <div class="bp-card-ceo-stamp">
                                            <i class="mdi mdi-check-decagram"></i>
                                        </div>
                                    @endif

                                    <div class="bp-paper-top">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="bp-paper-number">{{ $paper->paper_number ?? 'N/A' }}</span>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($paper->agenda_item_number)
                                                    <span class="bp-agenda-tag">
                                                        <i class="mdi mdi-format-list-numbered"></i>
                                                        Agenda {{ $paper->agenda_item_number }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <h5 class="bp-paper-title">{{ $paper->title }}</h5>

                                        <div class="bp-paper-author">
                                            <div class="bp-author-avatar">
                                                {{ strtoupper(substr(optional($paper->author)->name ?? 'N', 0, 1)) }}
                                            </div>
                                            <span>{{ optional($paper->author)->name ?? 'Unknown Author' }}</span>
                                        </div>
                                    </div>

                                    <div class="bp-paper-bottom">
                                        <div class="bp-paper-meta">
                                            @if($hasCeoSig)
                                                <span class="bp-ceo-chip">
                                                    <i class="mdi mdi-check-decagram"></i> CEO Signed
                                                </span>
                                            @endif
                                            @if($annexureCount > 0)
                                                <span class="bp-annexure-chip">
                                                    <i class="mdi mdi-paperclip"></i>
                                                    {{ $annexureCount }} {{ Str::plural('Annexure', $annexureCount) }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="bp-read-btn">
                                            <i class="mdi mdi-book-open-page-variant"></i>
                                            <span>Read</span>
                                            <i class="mdi mdi-arrow-right bp-read-arrow"></i>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     FULLSCREEN READING MODALS (one per paper)
     ═══════════════════════════════════════════════════════════════════════ --}}
@foreach($meetings as $meeting)
    @foreach($meeting->boardPapers as $paper)
        @php
            $hasCeoSig = !empty($paper->ceo_signature);
            $paperNotes = $paper->stickyNotes->where('user_id', Auth::id())->sortBy('created_at');
            $ext = fn($fileName) => strtolower(pathinfo($fileName ?? '', PATHINFO_EXTENSION));
            $fileTypeClass = function($fileName) {
                $e = strtolower(pathinfo($fileName ?? '', PATHINFO_EXTENSION));
                if ($e === 'pdf') return 'bp-file-pdf';
                if (in_array($e, ['doc','docx'])) return 'bp-file-word';
                if (in_array($e, ['xls','xlsx'])) return 'bp-file-excel';
                if (in_array($e, ['png','jpg','jpeg','gif','svg','webp'])) return 'bp-file-image';
                if (in_array($e, ['ppt','pptx'])) return 'bp-file-ppt';
                return 'bp-file-other';
            };
            $fileIcon = function($fileName) {
                $e = strtolower(pathinfo($fileName ?? '', PATHINFO_EXTENSION));
                if ($e === 'pdf') return 'mdi-file-pdf-box';
                if (in_array($e, ['doc','docx'])) return 'mdi-file-word-box';
                if (in_array($e, ['xls','xlsx'])) return 'mdi-file-excel-box';
                if (in_array($e, ['png','jpg','jpeg','gif','svg','webp'])) return 'mdi-file-image';
                if (in_array($e, ['ppt','pptx'])) return 'mdi-file-powerpoint-box';
                return 'mdi-file-outline';
            };
        @endphp

        <div class="modal fade" id="paperModal-{{ $paper->id }}" tabindex="-1" aria-labelledby="paperModalLabel-{{ $paper->id }}" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content bp-reader-modal">

                    {{-- Modal Header Bar --}}
                    <div class="bp-reader-header">
                        <div class="bp-reader-header-left">
                            <span class="bp-reader-paper-num">{{ $paper->paper_number ?? 'N/A' }}</span>
                            <h5 class="bp-reader-title" id="paperModalLabel-{{ $paper->id }}">{{ $paper->title }}</h5>
                        </div>
                        <div class="bp-reader-header-right">
                            @if($hasCeoSig)
                                <span class="bp-reader-ceo-badge">
                                    <i class="mdi mdi-check-decagram"></i> CEO Approved
                                </span>
                            @endif
                            <button type="button" class="bp-reader-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body bp-reader-body p-0">
                        <div class="bp-reader-layout">

                            {{-- Main Content Area --}}
                            <div class="bp-reader-main">
                                {{-- Paper meta bar --}}
                                <div class="bp-reader-meta-bar">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        @if($paper->agenda_item_number)
                                            <span class="bp-rmeta-chip">
                                                <i class="mdi mdi-format-list-numbered"></i> Agenda {{ $paper->agenda_item_number }}
                                            </span>
                                        @endif
                                        <span class="bp-rmeta-chip">
                                            <i class="mdi mdi-account-outline"></i> {{ optional($paper->author)->name ?? 'Unknown' }}
                                        </span>
                                        <span class="bp-rmeta-chip">
                                            <i class="mdi mdi-shield-account"></i> {{ optional(optional($paper->meeting)->boardCommittee)->name ?? 'Committee' }}
                                        </span>
                                        @if($paper->ceo_approved_at)
                                            <span class="bp-rmeta-chip bp-rmeta-chip-green">
                                                <i class="mdi mdi-calendar-check"></i> Approved {{ Carbon::parse($paper->ceo_approved_at)->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Rendered content --}}
                                <div class="bp-reader-content">
                                    @if($hasCeoSig)
                                        <div class="bp-ceo-watermark">
                                            <img src="{{ $paper->ceo_signature }}" alt="CEO Signature" class="bp-ceo-watermark-img">
                                        </div>
                                    @endif
                                    <div class="bp-reader-document">
                                        {!! $paper->rendered_content !!}
                                    </div>
                                </div>

                                {{-- Original document download --}}
                                @if($paper->file_path)
                                    <div class="bp-reader-download-bar">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="mdi mdi-file-pdf-box bp-file-pdf" style="font-size:28px;"></i>
                                            <div>
                                                <div class="fw-semibold">Original Document</div>
                                                <div class="text-muted" style="font-size:13px;">Download the source PDF</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('workflow.papers.pdf', $paper) }}" target="_blank" class="bp-download-btn">
                                            <i class="mdi mdi-download"></i> Download PDF
                                        </a>
                                    </div>
                                @endif

                                {{-- Annexures Section --}}
                                @if($paper->annexures->isNotEmpty())
                                    <div class="bp-reader-annexures">
                                        <h5 class="bp-section-heading">
                                            <i class="mdi mdi-paperclip"></i>
                                            Annexures
                                            <span class="bp-section-count">{{ $paper->annexures->count() }}</span>
                                        </h5>
                                        <div class="bp-annexure-list">
                                            @foreach($paper->annexures->sortBy('sort_order') as $annexure)
                                                @php
                                                    $aExt = strtolower(pathinfo($annexure->file_name ?? '', PATHINFO_EXTENSION));
                                                    $isPdf = ($aExt === 'pdf');
                                                    $isImage = in_array($aExt, ['png','jpg','jpeg','gif','webp','svg']);
                                                    $annexureUrl = $annexure->file_path ? asset('storage/' . $annexure->file_path) : null;
                                                @endphp
                                                <div class="bp-annexure-item {{ $fileTypeClass($annexure->file_name) }}">
                                                    <div class="bp-annexure-header">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="bp-annexure-icon {{ $fileTypeClass($annexure->file_name) }}">
                                                                <i class="mdi {{ $fileIcon($annexure->file_name) }}"></i>
                                                            </div>
                                                            <div>
                                                                <div class="bp-annexure-title">{{ $annexure->title }}</div>
                                                                <div class="bp-annexure-file-info">
                                                                    {{ $annexure->file_name ?? 'Document' }}
                                                                    @if($annexure->file_size)
                                                                        &middot; {{ number_format($annexure->file_size / 1024, 0) }} KB
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if($annexureUrl && ($isPdf || $isImage))
                                                                <button class="bp-annexure-toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#annexPreview-{{ $paper->id }}-{{ $annexure->id }}" aria-expanded="false">
                                                                    <i class="mdi mdi-eye-outline"></i> Preview
                                                                </button>
                                                            @endif
                                                            @if($annexureUrl)
                                                                <a href="{{ $annexureUrl }}" target="_blank" class="bp-annexure-dl-btn">
                                                                    <i class="mdi mdi-download"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Inline preview (PDF or Image) --}}
                                                    @if($annexureUrl && ($isPdf || $isImage))
                                                        <div class="collapse" id="annexPreview-{{ $paper->id }}-{{ $annexure->id }}">
                                                            <div class="bp-annexure-preview">
                                                                @if($isPdf)
                                                                    <iframe src="{{ $annexureUrl }}#toolbar=1&navpanes=0" class="bp-pdf-preview" loading="lazy"></iframe>
                                                                @elseif($isImage)
                                                                    <img src="{{ $annexureUrl }}" alt="{{ $annexure->title }}" class="bp-image-preview" loading="lazy">
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Sidebar: Notes + Details --}}
                            <div class="bp-reader-sidebar">
                                {{-- Sticky Notes Panel --}}
                                <div class="bp-sidebar-panel">
                                    <h6 class="bp-sidebar-heading">
                                        <i class="mdi mdi-note-multiple"></i> My Notes
                                    </h6>

                                    <div class="bp-notes-list" id="notesList-{{ $paper->id }}">
                                        @forelse($paperNotes as $note)
                                            <div class="bp-note-item" id="note-{{ $note->id }}" style="background:{{ $noteColors[$note->color] ?? $noteColors['yellow'] }};">
                                                <button class="bp-note-delete" onclick="BPPapers.deleteNote({{ $note->id }})" title="Delete note">
                                                    <i class="mdi mdi-close"></i>
                                                </button>
                                                <div class="bp-note-content">{{ $note->content }}</div>
                                                <div class="bp-note-footer">
                                                    <span class="bp-note-color-dot" style="background:{{ $noteColors[$note->color] ?? $noteColors['yellow'] }};"></span>
                                                    {{ $note->created_at ? Carbon::parse($note->created_at)->diffForHumans() : '' }}
                                                </div>
                                            </div>
                                        @empty
                                            <div class="bp-notes-empty" id="notesEmpty-{{ $paper->id }}">
                                                <i class="mdi mdi-note-off-outline"></i>
                                                <p>No notes yet</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    <div class="bp-note-form">
                                        <textarea class="bp-note-textarea" id="noteContent-{{ $paper->id }}" rows="3" placeholder="Type your note..."></textarea>
                                        <div class="bp-color-selector" id="colorSelector-{{ $paper->id }}">
                                            @foreach($noteColors as $colorName => $colorHex)
                                                <div class="bp-color-opt {{ $colorName === 'yellow' ? 'active' : '' }}"
                                                     data-color="{{ $colorName }}"
                                                     style="background:{{ $colorHex }};"
                                                     title="{{ ucfirst($colorName) }}">
                                                    @if($colorName === 'yellow')
                                                        <i class="mdi mdi-check"></i>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="bp-save-note-btn" onclick="BPPapers.saveNote({{ $paper->id }})">
                                            <i class="mdi mdi-content-save"></i> Save Note
                                        </button>
                                    </div>
                                </div>

                                {{-- Paper Details Panel --}}
                                <div class="bp-sidebar-panel">
                                    <h6 class="bp-sidebar-heading">
                                        <i class="mdi mdi-information-outline"></i> Paper Details
                                    </h6>
                                    <div class="bp-detail-list">
                                        @if($paper->paper_type)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">Type</span>
                                                <span class="bp-detail-value">{{ $paper->paper_type }}</span>
                                            </div>
                                        @endif
                                        @if($paper->financial_year)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">Financial Year</span>
                                                <span class="bp-detail-value">{{ $paper->financial_year }}</span>
                                            </div>
                                        @endif
                                        @if($paper->reporting_period)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">Reporting Period</span>
                                                <span class="bp-detail-value">{{ $paper->reporting_period }}</span>
                                            </div>
                                        @endif
                                        @if($paper->author_department)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">Department</span>
                                                <span class="bp-detail-value">{{ $paper->author_department }}</span>
                                            </div>
                                        @endif
                                        @if($paper->committee_name)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">Committee</span>
                                                <span class="bp-detail-value">{{ $paper->committee_name }}</span>
                                            </div>
                                        @endif
                                        <div class="bp-detail-row">
                                            <span class="bp-detail-label">Author</span>
                                            <span class="bp-detail-value">{{ optional($paper->author)->name ?? '---' }}</span>
                                        </div>
                                        @if($hasCeoSig)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">CEO Signature</span>
                                                <span class="bp-detail-value">
                                                    <img src="{{ $paper->ceo_signature }}" alt="CEO Sig" class="bp-detail-sig">
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Meeting Context Panel --}}
                                <div class="bp-sidebar-panel">
                                    <h6 class="bp-sidebar-heading">
                                        <i class="mdi mdi-calendar-text"></i> Meeting
                                    </h6>
                                    <div class="bp-detail-list">
                                        <div class="bp-detail-row">
                                            <span class="bp-detail-label">Committee</span>
                                            <span class="bp-detail-value">{{ optional(optional($paper->meeting)->boardCommittee)->name ?? '---' }}</span>
                                        </div>
                                        @if(optional($paper->meeting)->title)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">Meeting</span>
                                                <span class="bp-detail-value">{{ $paper->meeting->title }}</span>
                                            </div>
                                        @endif
                                        @if(optional($paper->meeting)->meeting_date)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">Date</span>
                                                <span class="bp-detail-value">{{ Carbon::parse($paper->meeting->meeting_date)->isoFormat('ddd, MMM D, YYYY') }}</span>
                                            </div>
                                        @endif
                                        @if(optional($paper->meeting)->venue)
                                            <div class="bp-detail-row">
                                                <span class="bp-detail-label">Venue</span>
                                                <span class="bp-detail-value">{{ $paper->meeting->venue }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endforeach

@endsection

@push('styles')
<style>
/* Board Portal — Papers. Clean, logo-balanced (black / red / green on white) */
:root {
    --pp-black: #1a1a1a; --pp-ink: #111827; --pp-body: #374151; --pp-muted: #6b7280;
    --pp-line: #eef0f2; --pp-soft: #f7f8f9; --pp-grey: #f1f2f3;
    --pp-red: #cc1f2b; --pp-red-soft: #fdeced; --pp-green: #23803a; --pp-green-soft: #e9f6eb;
    --pp-amber: #b45309; --pp-amber-soft: #fef6e4;
}

.bp-papers { padding: 0 4px 32px; font-size: 15px; }

/* ── Page header ─────────────────────────────────────────── */
.bp-hero { position: relative; overflow: hidden; background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 16px;
    box-shadow: 0 1px 2px rgba(17,17,17,.04); margin-bottom: 22px; }
.bp-hero-pattern { position: absolute; left: 0; top: 0; bottom: 0; width: 5px;
    background: linear-gradient(180deg, #1a1a1a 0 33.33%, #cc1f2b 33.33% 66.66%, #3ca848 66.66% 100%); }
.bp-hero-content { padding: 22px 26px 20px 30px; }
.bp-back-link { display: inline-flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 600; color: var(--pp-muted); text-decoration: none; margin-bottom: 6px; }
.bp-back-link:hover { color: var(--pp-ink); }
.bp-hero-title { display: flex; align-items: center; gap: 10px; margin: 0 0 4px; font-size: 1.5rem !important; font-weight: 800 !important; color: var(--pp-ink) !important; letter-spacing: -.02em; }
.bp-hero-title i { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: var(--pp-grey); color: var(--pp-black); font-size: 20px; }
.bp-hero-subtitle { margin: 0; font-size: 14px; color: var(--pp-muted); }
.bp-hero-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; background: var(--pp-soft); border: 1px solid var(--pp-line);
    color: var(--pp-body); font-size: 13px; font-weight: 600; }
.bp-hero-chip i { color: var(--pp-muted); font-size: 15px; }
.bp-hero-chip-amber i { color: var(--pp-amber); }
.bp-hero-chip-green { background: var(--pp-green-soft); border-color: #cfe9d4; color: var(--pp-green); }
.bp-hero-chip-green i { color: var(--pp-green); }

/* ── Empty state ─────────────────────────────────────────── */
.bp-empty-state { text-align: center; padding: 56px 24px; background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 16px; }
.bp-empty-icon { width: 68px; height: 68px; margin: 0 auto 16px; border-radius: 50%; background: var(--pp-grey); color: var(--pp-muted); display: flex; align-items: center; justify-content: center; font-size: 32px; }
.bp-empty-state h4 { font-size: 1.15rem !important; font-weight: 700 !important; color: var(--pp-ink) !important; margin-bottom: 6px; }
.bp-empty-state p { max-width: 440px; margin: 0 auto 18px; color: var(--pp-muted); font-size: 14px; }
.bp-btn-primary { display: inline-flex; align-items: center; gap: 4px; height: 42px; padding: 0 18px; border-radius: 10px; background: var(--pp-black); color: #fff !important; font-weight: 600; font-size: 14px; text-decoration: none; }
.bp-btn-primary:hover { background: #000; }

/* ── Meeting groups ──────────────────────────────────────── */
.bp-meetings-list { display: flex; flex-direction: column; gap: 16px; }
.bp-meeting-section { background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 16px; overflow: hidden; box-shadow: 0 1px 2px rgba(17,17,17,.04); }
.bp-meeting-header { display: flex; align-items: center; gap: 18px; padding: 18px 22px; cursor: pointer; transition: background .15s; }
.bp-meeting-header:hover { background: #fafafa; }
.bp-meeting-header[aria-expanded="true"] { border-bottom: 1px solid var(--pp-line); }
.bp-meeting-date-block { width: 64px; flex-shrink: 0; padding: 8px 0; border-radius: 12px; border: 1px solid var(--pp-line); background: #fff; text-align: center; display: flex; flex-direction: column; align-items: center; }
.bp-date-day { font-size: 1.55rem; font-weight: 800; color: var(--pp-ink); line-height: 1; }
.bp-date-month { font-size: 11.5px; font-weight: 700; color: var(--pp-red); text-transform: uppercase; letter-spacing: .06em; margin-top: 4px; }
.bp-date-year { font-size: 11px; color: var(--pp-muted); }
.bp-meeting-info { flex: 1; min-width: 0; }
.bp-committee-chip { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: var(--pp-body); background: var(--pp-grey); padding: 3px 9px; border-radius: 999px; }
.bp-upcoming-chip { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: var(--pp-green); background: var(--pp-green-soft); padding: 3px 9px; border-radius: 999px; }
.bp-meeting-title { margin: 2px 0 4px; font-size: 1.05rem !important; font-weight: 700 !important; color: var(--pp-ink) !important; line-height: 1.35; }
.bp-meeting-meta { display: flex; flex-wrap: wrap; gap: 4px 16px; font-size: 13px; color: var(--pp-muted); }
.bp-meeting-meta i { margin-right: 3px; }
.bp-meeting-right { display: flex; align-items: center; gap: 14px; flex-shrink: 0; }
.bp-paper-count-badge { text-align: center; padding: 6px 14px; border-radius: 12px; background: var(--pp-soft); border: 1px solid var(--pp-line); }
.bp-paper-count-number { display: block; font-size: 1.2rem; font-weight: 800; color: var(--pp-ink); line-height: 1.1; }
.bp-paper-count-label { font-size: 11.5px; color: var(--pp-muted); font-weight: 600; }
.bp-collapse-icon { font-size: 22px; color: var(--pp-muted); transition: transform .2s; }
.bp-meeting-header[aria-expanded="true"] .bp-collapse-icon { transform: rotate(180deg); }

/* ── Paper cards ─────────────────────────────────────────── */
.bp-papers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; padding: 18px 22px 22px; background: var(--pp-soft); }
.bp-paper-card { position: relative; display: flex; flex-direction: column; justify-content: space-between; min-height: 190px; padding: 16px 18px; border-radius: 14px;
    background: #fff; border: 1px solid var(--pp-line); cursor: pointer; transition: transform .2s, box-shadow .2s, border-color .2s; outline: none; }
.bp-paper-card:hover, .bp-paper-card:focus-visible { transform: translateY(-2px); border-color: rgba(17,17,17,.14); box-shadow: 0 10px 26px rgba(17,17,17,.08); }
.bp-paper-card:focus-visible { box-shadow: 0 0 0 3px rgba(204,31,43,.2); }
.bp-card-ceo-stamp { position: absolute; top: 14px; right: 14px; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    background: var(--pp-green-soft); color: var(--pp-green); font-size: 15px; }
.bp-paper-number { font-size: 12px; font-weight: 700; color: var(--pp-body); background: var(--pp-grey); padding: 3px 9px; border-radius: 7px; font-variant-numeric: tabular-nums; }
.bp-agenda-tag { display: inline-flex; align-items: center; gap: 3px; font-size: 12px; font-weight: 600; color: var(--pp-muted); margin-right: 30px; }
.bp-paper-title { margin: 0 0 12px; font-size: 15px !important; font-weight: 700 !important; color: var(--pp-ink) !important; line-height: 1.45;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.bp-paper-author { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--pp-muted); }
.bp-author-avatar { width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--pp-black); color: #fff; font-size: 11.5px; font-weight: 700; }
.bp-paper-bottom { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--pp-line); }
.bp-paper-meta { display: flex; flex-wrap: wrap; gap: 6px; }
.bp-ceo-chip { display: inline-flex; align-items: center; gap: 3px; font-size: 11.5px; font-weight: 600; color: var(--pp-green); background: var(--pp-green-soft); padding: 2px 8px; border-radius: 999px; }
.bp-annexure-chip { display: inline-flex; align-items: center; gap: 3px; font-size: 11.5px; font-weight: 600; color: var(--pp-body); background: var(--pp-grey); padding: 2px 8px; border-radius: 999px; }
.bp-read-btn { display: inline-flex; align-items: center; gap: 5px; font-size: 13px; font-weight: 700; color: var(--pp-red); white-space: nowrap; }
.bp-read-arrow { transition: transform .2s; }
.bp-paper-card:hover .bp-read-arrow { transform: translateX(3px); }

/* ── Reader modal ────────────────────────────────────────── */
.bp-reader-modal { background: #f5f6f7; border: 0; border-radius: 0; display: flex; flex-direction: column; }
.bp-reader-header { position: sticky; top: 0; z-index: 5; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 24px;
    background: #fff; border-bottom: 1px solid var(--pp-line); }
.bp-reader-header::after { content: ""; position: absolute; left: 0; right: 0; bottom: -3px; height: 3px;
    background: linear-gradient(90deg, #1a1a1a 0 33.33%, #cc1f2b 33.33% 66.66%, #3ca848 66.66% 100%); }
.bp-reader-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
.bp-reader-paper-num { flex-shrink: 0; font-size: 12px; font-weight: 700; color: #fff; background: var(--pp-black); padding: 5px 11px; border-radius: 8px; }
.bp-reader-title { margin: 0; font-size: 1.05rem !important; font-weight: 700 !important; color: var(--pp-ink) !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bp-reader-header-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.bp-reader-ceo-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; color: var(--pp-green); background: var(--pp-green-soft); padding: 5px 11px; border-radius: 999px; }
.bp-reader-close { width: 38px; height: 38px; border-radius: 10px; border: 1px solid var(--pp-line); background: #fff; color: var(--pp-body); font-size: 20px; display: flex; align-items: center; justify-content: center; transition: all .15s; }
.bp-reader-close:hover { background: var(--pp-black); border-color: var(--pp-black); color: #fff; }

.bp-reader-body { overflow-y: auto; flex: 1; -webkit-overflow-scrolling: touch; }
.bp-reader-layout { display: flex; min-height: 100%; }
.bp-reader-main { flex: 1; min-width: 0; max-width: 900px; margin: 0 auto; padding: 28px 32px 48px; }
.bp-reader-sidebar { width: 340px; flex-shrink: 0; background: #fff; border-left: 1px solid var(--pp-line); padding: 24px 20px 48px; overflow-y: auto; display: flex; flex-direction: column; gap: 18px; }

.bp-reader-meta-bar { margin-bottom: 18px; }
.bp-rmeta-chip { display: inline-flex; align-items: center; gap: 5px; padding: 5px 11px; border-radius: 999px; background: #fff; border: 1px solid var(--pp-line); color: var(--pp-body); font-size: 13px; font-weight: 600; white-space: nowrap; }
.bp-rmeta-chip i { font-size: 15px; color: var(--pp-muted); }
.bp-rmeta-chip-green { background: var(--pp-green-soft); border-color: #cfe9d4; color: var(--pp-green); }
.bp-rmeta-chip-green i { color: var(--pp-green); }

.bp-reader-content { position: relative; overflow: hidden; background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 14px; padding: 44px 52px; margin-bottom: 20px;
    box-shadow: 0 1px 2px rgba(17,17,17,.04), 0 10px 30px rgba(17,17,17,.04); }
.bp-ceo-watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-25deg); opacity: .05; pointer-events: none; z-index: 0; }
.bp-ceo-watermark-img { max-width: 400px; max-height: 300px; width: auto; height: auto; }

/* Reading typography */
.bp-reader-document { position: relative; z-index: 1; font-family: Georgia, 'Times New Roman', 'Noto Serif', serif; font-size: 16.5px; line-height: 1.85; color: #1f2937; word-break: break-word; }
.bp-reader-document h1, .bp-reader-document h2, .bp-reader-document h3,
.bp-reader-document h4, .bp-reader-document h5, .bp-reader-document h6 {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: var(--pp-ink) !important; margin-top: 1.8rem; margin-bottom: .8rem; font-weight: 700 !important; line-height: 1.35; letter-spacing: -.01em; }
.bp-reader-document h1 { font-size: 1.6rem !important; }
.bp-reader-document h2 { font-size: 1.35rem !important; }
.bp-reader-document h3 { font-size: 1.18rem !important; }
.bp-reader-document h4 { font-size: 1.08rem !important; }
.bp-reader-document p { margin-bottom: 1rem; }
.bp-reader-document table { width: 100%; border-collapse: collapse; margin: 1.2rem 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; }
.bp-reader-document table th, .bp-reader-document table td { border: 1px solid #e5e7eb; padding: 10px 14px; }
.bp-reader-document table thead th { background: #f7f7f8; color: var(--pp-body); font-weight: 700; font-size: 12.5px; text-transform: uppercase; letter-spacing: .04em; }
.bp-reader-document table tbody tr:nth-child(even) { background: #fbfbfb; }
.bp-reader-document ul, .bp-reader-document ol { padding-left: 1.8rem; margin-bottom: 1rem; }
.bp-reader-document li { margin-bottom: .4rem; }
.bp-reader-document img { max-width: 100%; height: auto; border-radius: 8px; margin: 1rem 0; }
.bp-reader-document blockquote { border-left: 3px solid var(--pp-red); padding: 12px 20px; margin: 1.2rem 0; background: #fafafa; border-radius: 0 8px 8px 0; font-style: italic; color: var(--pp-body); }
.bp-reader-document a { color: var(--pp-red); text-decoration: underline; }

/* Download bar */
.bp-reader-download-bar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 20px; margin-bottom: 20px; background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 14px; }
.bp-download-btn { display: inline-flex; align-items: center; gap: 6px; height: 40px; padding: 0 16px; border-radius: 10px; background: var(--pp-black); color: #fff !important; font-weight: 600; font-size: 14px; text-decoration: none; transition: background .15s; }
.bp-download-btn:hover { background: #000; }

/* File type colours (muted) */
.bp-file-pdf { color: var(--pp-red); }
.bp-file-word { color: var(--pp-black); }
.bp-file-excel { color: var(--pp-green); }
.bp-file-image { color: var(--pp-amber); }
.bp-file-ppt { color: #c2410c; }
.bp-file-other { color: var(--pp-muted); }

/* Annexures */
.bp-reader-annexures { margin-bottom: 20px; }
.bp-section-heading { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; font-size: 15px !important; font-weight: 700 !important; color: var(--pp-ink) !important; }
.bp-section-count { font-size: 12px; font-weight: 700; color: var(--pp-body); background: var(--pp-grey); padding: 1px 8px; border-radius: 999px; }
.bp-annexure-list { display: flex; flex-direction: column; gap: 10px; }
.bp-annexure-item { background: #fff; border: 1px solid rgba(17,17,17,.06); border-radius: 12px; overflow: hidden; color: inherit; }
.bp-annexure-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 16px; }
.bp-annexure-icon { width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 22px; background: var(--pp-grey); }
.bp-annexure-title { font-size: 14px; font-weight: 600; color: var(--pp-ink); }
.bp-annexure-file-info { font-size: 12.5px; color: var(--pp-muted); }
.bp-annexure-toggle-btn { display: inline-flex; align-items: center; gap: 4px; height: 34px; padding: 0 12px; border-radius: 9px; border: 1px solid var(--pp-line); background: #fff; color: var(--pp-body); font-size: 13px; font-weight: 600; transition: all .15s; }
.bp-annexure-toggle-btn:hover { border-color: var(--pp-black); color: var(--pp-ink); }
.bp-annexure-dl-btn { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--pp-line); color: var(--pp-body); text-decoration: none; font-size: 17px; transition: all .15s; }
.bp-annexure-dl-btn:hover { background: var(--pp-black); border-color: var(--pp-black); color: #fff; }
.bp-annexure-preview { border-top: 1px solid var(--pp-line); background: var(--pp-soft); padding: 12px; }
.bp-pdf-preview { width: 100%; height: 600px; border: 0; border-radius: 8px; background: #fff; }
.bp-image-preview { display: block; max-width: 100%; margin: 0 auto; border-radius: 8px; }

/* Sidebar panels */
.bp-sidebar-panel { padding: 16px; border: 1px solid var(--pp-line); border-radius: 14px; background: #fff; }
.bp-sidebar-heading { display: flex; align-items: center; gap: 7px; margin-bottom: 12px; font-size: 14px !important; font-weight: 700 !important; color: var(--pp-ink) !important; }
.bp-sidebar-heading i { color: var(--pp-muted); font-size: 17px; }

.bp-notes-list { display: flex; flex-direction: column; gap: 8px; max-height: 320px; overflow-y: auto; margin-bottom: 12px; }
.bp-note-item { position: relative; padding: 10px 30px 8px 12px; border-radius: 10px; box-shadow: 0 1px 2px rgba(17,17,17,.06); }
.bp-note-delete { position: absolute; top: 6px; right: 6px; width: 22px; height: 22px; border: 0; border-radius: 6px; background: rgba(0,0,0,.06); color: #374151; font-size: 13px; display: flex; align-items: center; justify-content: center; opacity: .6; transition: opacity .15s; }
.bp-note-item:hover .bp-note-delete { opacity: 1; }
.bp-note-content { font-size: 13.5px; color: #1f2937; line-height: 1.5; white-space: pre-wrap; word-break: break-word; }
.bp-note-footer { display: flex; align-items: center; gap: 6px; margin-top: 6px; font-size: 11.5px; color: #4b5563; }
.bp-note-color-dot { width: 8px; height: 8px; border-radius: 50%; border: 1px solid rgba(0,0,0,.15); }
.bp-notes-empty { text-align: center; padding: 18px 8px; color: #9ca3af; }
.bp-notes-empty i { font-size: 26px; }
.bp-notes-empty p { margin: 4px 0 0; font-size: 13px; }

.bp-note-form { display: flex; flex-direction: column; gap: 10px; }
.bp-note-textarea { width: 100%; resize: vertical; min-height: 80px; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #e5e7eb; background: #fafafa; font-size: 14px; color: var(--pp-ink); transition: border-color .15s, box-shadow .15s, background .15s; }
.bp-note-textarea:focus { outline: none; border-color: var(--pp-black); background: #fff; box-shadow: 0 0 0 3px rgba(17,17,17,.06); }
.bp-color-selector { display: flex; gap: 8px; }
.bp-color-opt { width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #1f2937;
    border: 2px solid #fff; box-shadow: 0 0 0 1px #d1d5db; transition: box-shadow .15s, transform .15s; }
.bp-color-opt:hover { transform: scale(1.08); }
.bp-color-opt.active { box-shadow: 0 0 0 2px var(--pp-black); }
.bp-save-note-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 40px; border: 0; border-radius: 10px; background: var(--pp-green); color: #fff; font-weight: 600; font-size: 14px; transition: background .15s; }
.bp-save-note-btn:hover { background: #19662c; }

.bp-detail-list { display: flex; flex-direction: column; }
.bp-detail-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 8px 0; border-bottom: 1px dashed var(--pp-line); }
.bp-detail-row:last-child { border-bottom: 0; }
.bp-detail-label { font-size: 12.5px; color: var(--pp-muted); flex-shrink: 0; }
.bp-detail-value { font-size: 13px; font-weight: 600; color: var(--pp-ink); text-align: right; word-break: break-word; }
.bp-detail-sig { max-height: 40px; max-width: 140px; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 1024px) {
    .bp-reader-layout { flex-direction: column; }
    .bp-reader-sidebar { width: 100%; border-left: 0; border-top: 1px solid var(--pp-line); padding: 20px; }
    .bp-reader-main { padding: 20px; max-width: 100%; }
    .bp-reader-content { padding: 28px 24px; }
    .bp-reader-document { font-size: 15.5px; }
    .bp-pdf-preview { height: 450px; }
}
@media (max-width: 768px) {
    .bp-hero-content { padding: 18px 16px 16px 20px; }
    .bp-hero-title { font-size: 1.25rem !important; }
    .bp-meeting-header { flex-wrap: wrap; gap: 12px; padding: 16px; }
    .bp-meeting-date-block { width: 56px; }
    .bp-meeting-info { flex-basis: calc(100% - 70px); }
    .bp-meeting-right { width: 100%; justify-content: space-between; }
    .bp-papers-grid { grid-template-columns: 1fr; padding: 14px; }
    .bp-reader-header { padding: 12px 14px; }
    .bp-reader-title { font-size: .95rem !important; }
    .bp-reader-ceo-badge { display: none; }
    .bp-reader-main { padding: 14px; }
    .bp-reader-content { padding: 20px 16px; }
    .bp-reader-document { font-size: 15px; line-height: 1.75; }
    .bp-annexure-header { flex-direction: column; align-items: flex-start; }
    .bp-pdf-preview { height: 350px; }
    .bp-reader-download-bar { flex-direction: column; align-items: stretch; }
    .bp-download-btn { justify-content: center; }
    .bp-ceo-watermark-img { max-width: 250px; }
}
@media (min-width: 1400px) {
    .bp-reader-main { max-width: 960px; padding: 36px 48px 60px; }
    .bp-reader-sidebar { width: 380px; }
    .bp-reader-content { padding: 52px 60px; }
}
@media (prefers-reduced-motion: reduce) {
    .bp-paper-card, .bp-read-arrow, .bp-collapse-icon { transition: none; }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    var noteColors = {
        yellow: '#fff9c4',
        blue:   '#dbdbdb',
        green:  '#c8e6c9',
        pink:   '#f8bbd0'
    };

    // Track selected color per paper
    var selectedColors = {};

    // ── Color Selector ─────────────────────────────────────────────
    $(document).on('click', '.bp-color-opt', function() {
        var $this = $(this);
        var $selector = $this.closest('.bp-color-selector');
        var paperId = $selector.attr('id').replace('colorSelector-', '');

        $selector.find('.bp-color-opt').removeClass('active').find('i').remove();
        $this.addClass('active').html('<i class="mdi mdi-check"></i>');

        selectedColors[paperId] = $this.data('color');
    });

    // ── Expose global namespace ────────────────────────────────────
    window.BPPapers = {

        // Save a sticky note
        saveNote: function(paperId) {
            var content = $.trim($('#noteContent-' + paperId).val());
            if (!content) {
                toastr.warning('Please enter a note before saving.');
                return;
            }

            var color = selectedColors[paperId] || 'yellow';
            var $btn = $('#noteContent-' + paperId).closest('.bp-note-form').find('.bp-save-note-btn');

            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');

            $.ajax({
                url: '/board/portal/papers/' + paperId + '/notes',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: {
                    content: content,
                    color: color
                },
                success: function(res) {
                    if (res.success && res.note) {
                        // Remove empty state
                        $('#notesEmpty-' + paperId).remove();

                        var bgColor = noteColors[res.note.color] || noteColors['yellow'];

                        var noteHtml =
                            '<div class="bp-note-item" id="note-' + res.note.id + '" style="background:' + bgColor + ';">' +
                                '<button class="bp-note-delete" onclick="BPPapers.deleteNote(' + res.note.id + ')" title="Delete note">' +
                                    '<i class="mdi mdi-close"></i>' +
                                '</button>' +
                                '<div class="bp-note-content">' + $('<div>').text(res.note.content).html() + '</div>' +
                                '<div class="bp-note-footer">' +
                                    '<span class="bp-note-color-dot" style="background:' + bgColor + ';"></span>' +
                                    'just now' +
                                '</div>' +
                            '</div>';

                        $('#notesList-' + paperId).append(noteHtml);

                        // Scroll to bottom of notes
                        var $list = $('#notesList-' + paperId);
                        $list.scrollTop($list[0].scrollHeight);

                        $('#noteContent-' + paperId).val('');
                        toastr.success('Note saved.');
                    } else {
                        toastr.error(res.message || 'Failed to save note.');
                    }
                },
                error: function() {
                    toastr.error('Failed to save note. Please try again.');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-content-save"></i> Save Note');
                }
            });
        },

        // Delete a sticky note
        deleteNote: function(noteId) {
            Swal.fire({
                title: 'Delete Note?',
                text: 'This note will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/board/portal/notes/' + noteId,
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        success: function(res) {
                            if (res.success) {
                                var $note = $('#note-' + noteId);
                                var $list = $note.closest('.bp-notes-list');
                                var paperId = $list.attr('id').replace('notesList-', '');

                                $note.fadeOut(300, function() {
                                    $(this).remove();

                                    // Show empty state if no notes left
                                    if ($list.find('.bp-note-item').length === 0) {
                                        $list.html(
                                            '<div class="bp-notes-empty" id="notesEmpty-' + paperId + '">' +
                                                '<i class="mdi mdi-note-off-outline"></i>' +
                                                '<p>No notes yet</p>' +
                                            '</div>'
                                        );
                                    }
                                });
                                toastr.success('Note deleted.');
                            } else {
                                toastr.error(res.message || 'Failed to delete note.');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 403) {
                                toastr.error('You are not authorized to delete this note.');
                            } else {
                                toastr.error('Failed to delete note. Please try again.');
                            }
                        }
                    });
                }
            });
        }
    };

    // ── Keyboard shortcuts for modal ───────────────────────────────
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            var $openModal = $('.modal.show');
            if ($openModal.length) {
                $openModal.modal('hide');
            }
        }
    });

    // ── Collapse icon rotation sync ────────────────────────────────
    $('.bp-meeting-header').each(function() {
        var $header = $(this);
        var targetId = $header.data('bs-target');

        if (targetId) {
            $(targetId).on('show.bs.collapse', function() {
                $header.attr('aria-expanded', 'true');
            }).on('hide.bs.collapse', function() {
                $header.attr('aria-expanded', 'false');
            });
        }
    });

})();
</script>
@endpush
