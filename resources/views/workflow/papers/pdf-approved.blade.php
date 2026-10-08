<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $paper->paper_number }} - Approved Board Paper</title>
    <style>
        @page {
            margin: 25mm 20mm 30mm 20mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #222;
        }

        /* Letterhead */
        .letterhead {
            text-align: center;
            margin-bottom: 10px;
        }
        .letterhead img {
            max-height: 80px;
        }
        .letterhead-line {
            border: none;
            border-top: 3px solid {{ setting('primary_color', '#1a1a1a') }};
            margin: 8px 0 15px;
        }

        /* Paper header */
        .paper-meta {
            width: 100%;
            margin-bottom: 15px;
        }
        .paper-meta td {
            vertical-align: top;
            padding: 2px 0;
        }
        .paper-meta .label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .paper-meta .value {
            font-size: 13px;
            font-weight: bold;
            color: {{ setting('primary_color', '#1a1a1a') }};
        }

        /* Title */
        .paper-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: {{ setting('primary_color', '#1a1a1a') }};
            margin: 15px 0 5px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .agenda-badge {
            text-align: center;
            margin-bottom: 20px;
        }
        .agenda-badge span {
            background: #f3f3f3;
            color: {{ setting('primary_color', '#1a1a1a') }};
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 11px;
        }

        /* Content */
        .paper-content {
            text-align: justify;
            font-size: 12px;
            line-height: 1.7;
        }
        .paper-content h1, .paper-content h2, .paper-content h3,
        .paper-content h4, .paper-content h5, .paper-content h6 {
            color: {{ setting('primary_color', '#1a1a1a') }};
            margin-top: 15px;
            margin-bottom: 8px;
        }
        .paper-content h1 { font-size: 16px; }
        .paper-content h2 { font-size: 14px; }
        .paper-content h3 { font-size: 13px; }
        .paper-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .paper-content table td,
        .paper-content table th {
            border: 1px solid #ccc;
            padding: 6px 10px;
            font-size: 11px;
        }
        .paper-content table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .paper-content ul, .paper-content ol {
            padding-left: 25px;
            margin: 8px 0;
        }
        .paper-content li {
            margin-bottom: 4px;
        }
        .paper-content img {
            max-width: 100%;
            height: auto;
        }

        /* Approval stamp */
        .approval-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid {{ setting('primary_color', '#1a1a1a') }};
        }
        .approval-stamp {
            border: 2px solid #099139;
            border-radius: 8px;
            padding: 15px 20px;
            background: #f0faf3;
            text-align: center;
            width: 320px;
            margin-left: auto;
        }
        .approval-stamp .stamp-title {
            font-size: 11px;
            font-weight: bold;
            color: #099139;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .approval-stamp .signature-img {
            max-height: 70px;
            margin-bottom: 5px;
        }
        .approval-stamp .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 5px auto;
        }
        .approval-stamp .ceo-label {
            font-weight: bold;
            font-size: 12px;
            margin-top: 5px;
        }
        .approval-stamp .org-name {
            font-size: 10px;
            color: #666;
        }
        .approval-stamp .approval-date {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        /* Annexures */
        .annexures-section {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .annexures-section h4 {
            color: {{ setting('primary_color', '#1a1a1a') }};
            font-size: 13px;
            margin-bottom: 8px;
        }
        .annexure-item {
            font-size: 11px;
            padding: 4px 0;
            color: #444;
        }

        /* Footer */
        .pdf-footer {
            position: fixed;
            bottom: -20mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        /* Watermark for approved status */
        .watermark {
            position: fixed;
            top: 35%;
            left: 15%;
            font-size: 80px;
            color: rgba(9, 145, 57, 0.06);
            transform: rotate(-35deg);
            font-weight: bold;
            letter-spacing: 10px;
            z-index: -1;
        }
    </style>
</head>
<body>

    {{-- Watermark --}}
    <div class="watermark">APPROVED</div>

    @include('workflow.components.letterhead')

    {{-- Paper Meta --}}
    <table class="paper-meta">
        <tr>
            <td style="width: 50%;">
                <div class="label">Paper Number</div>
                <div class="value">{{ $paper->paper_number }}</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="label">Date</div>
                <div class="value" style="color: #333;">{{ \Carbon\Carbon::parse($paper->created_at)->format('d F Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- Title --}}
    <div class="paper-title">{{ $paper->title }}</div>
    @if($paper->agenda_item_number)
        <div class="agenda-badge">
            <span>Agenda Item {{ $paper->agenda_item_number }}</span>
        </div>
    @endif

    {{-- Meeting / Committee Reference --}}
    @if($paper->isStructuredContent())
        <div style="text-align: center; margin: 12px 0 16px; font-size: 12px; color: #374151;">
            @if($paper->committee_name)
                <div style="font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">
                    MEETING OF THE {{ strtoupper($paper->committee_name) }}
                    @if($paper->meeting && $paper->meeting->meeting_date)
                        DATED {{ \Carbon\Carbon::parse($paper->meeting->meeting_date)->format('jS F Y') }}
                    @endif
                </div>
            @endif
            @if($paper->paper_type)
                <div style="font-style: italic; color: #6b7280;">Paper Type: {{ $paper->paper_type }}</div>
            @endif
            @if($paper->reporting_period)
                <div>Reporting Period: {{ $paper->reporting_period }}</div>
            @endif
            @if($paper->author_department)
                <div>Prepared by: {{ $paper->author->name ?? '' }} &mdash; {{ $paper->author_department }}</div>
            @endif
        </div>
    @endif

    {{-- Paper Content (structured or legacy) --}}
    <div class="paper-content">
        {!! $paper->rendered_content !!}
    </div>

    {{-- Annexures List --}}
    @if($paper->annexures && $paper->annexures->count())
        <div class="annexures-section">
            <h4>Annexures</h4>
            @foreach($paper->annexures as $i => $annexure)
                <div class="annexure-item">
                    {{ $i + 1 }}. {{ $annexure->title }} ({{ strtoupper(pathinfo($annexure->file_path, PATHINFO_EXTENSION)) }})
                </div>
            @endforeach
        </div>
    @endif

    {{-- Approval Section --}}
    @php
        $auditCommitteeIds = \App\Models\BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
        $isAuditCommittee = $paper->meeting && in_array($paper->meeting->board_committee_id, $auditCommitteeIds);
        $approverRoleFull = $isAuditCommittee ? 'Deputy Director Internal Audit' : setting('signatory_title_ceo', 'Chief Executive Officer');
        // Resolve the approver's name from the user who approved (approved_by or ceo_approved_by)
        $approverUser = null;
        if ($paper->ceo_approved_at) {
            // Try to find the user who approved - check audit logs or use the meeting context
            $approverUser = \App\Models\User::role($isAuditCommittee ? 'DD Internal Audit' : 'CEO')->first();
        }
        $approverName = $approverUser->name ?? $approverRoleFull;
    @endphp
    <div class="approval-section">
        <div class="approval-stamp">
            <div class="stamp-title">{{ $isAuditCommittee ? 'DDIA' : 'CEO' }} Approved</div>
            @if($paper->ceo_signature)
                <img src="{{ $paper->ceo_signature }}" alt="Approver Signature" class="signature-img">
            @endif
            <div class="signature-line"></div>
            <div class="ceo-label">{{ $approverName }}</div>
            <div class="org-name">{{ $approverRoleFull }}</div>
            <div class="org-name">{{ setting('institution_name', 'eBoard System') }}</div>
            @if($paper->ceo_approved_at)
                <div class="approval-date">Date: {{ \Carbon\Carbon::parse($paper->ceo_approved_at)->format('d F Y') }}</div>
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <div class="pdf-footer">
        {{ $paper->paper_number }} | Approved: {{ $paper->ceo_approved_at ? \Carbon\Carbon::parse($paper->ceo_approved_at)->format('d/m/Y H:i') : '' }} | {{ setting('institution_acronym', 'ORG') }} eBoard Management System | Page <span class="pagenum"></span>
    </div>

</body>
</html>
