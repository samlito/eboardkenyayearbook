<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Conflict of Interest Register — {{ $meeting->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #111111;
            line-height: 1.4;
        }

        /* ── Header ───────────────────────────────────────────── */
        .header {
            text-align: center;
            border-bottom: 3px solid {{ setting('primary_color', '#1a1a1a') }};
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 700;
            color: {{ setting('primary_color', '#1a1a1a') }};
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 2px;
        }
        .header .subtitle {
            font-size: 11px;
            color: #6b7280;
        }

        /* ── Meeting Info ─────────────────────────────────────── */
        .meeting-info {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meeting-info td {
            padding: 5px 10px;
            font-size: 11px;
            vertical-align: top;
        }
        .meeting-info .label {
            font-weight: 700;
            color: {{ setting('primary_color', '#1a1a1a') }};
            width: 120px;
            white-space: nowrap;
        }
        .meeting-info .value {
            color: #111111;
        }

        /* ── Data Table ───────────────────────────────────────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table thead th {
            background: {{ setting('primary_color', '#1a1a1a') }};
            color: #ffffff;
            font-weight: 700;
            font-size: 10px;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #9b0c23;
        }
        .data-table tbody td {
            padding: 7px 6px;
            border: 1px solid #dee2e6;
            font-size: 10px;
            vertical-align: top;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* ── Badges ───────────────────────────────────────────── */
        .badge-yes {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
        }
        .badge-no {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
        }
        .badge-signed {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
        }

        /* ── Signature Image ──────────────────────────────────── */
        .signature-img {
            max-width: 100px;
            max-height: 40px;
        }

        /* ── Footer ───────────────────────────────────────────── */
        .footer {
            margin-top: 30px;
            border-top: 2px solid #e5e7eb;
            padding-top: 12px;
        }
        .footer-summary span {
            display: inline-block;
            margin-right: 24px;
            font-size: 12px;
        }
        .footer-summary strong {
            font-size: 14px;
            color: {{ setting('primary_color', '#1a1a1a') }};
        }
        .footer-note {
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
            margin-top: 16px;
        }

        /* ── Signature Block ──────────────────────────────────── */
        .signature-block {
            margin-top: 40px;
            width: 100%;
        }
        .signature-block td {
            width: 50%;
            padding: 30px 20px 0;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #374151;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
        }
        .signature-sub {
            font-size: 10px;
            color: #6b7280;
            font-weight: 400;
        }

        /* ── Detail Box (per-member conflict) ─────────────────── */
        .conflict-detail {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 4px;
            padding: 4px 6px;
            margin-top: 4px;
            font-size: 9px;
            color: #78350f;
        }
    </style>
</head>
<body>

    {{-- ══════════════════════════════════════════════════════════
         HEADER
         ══════════════════════════════════════════════════════════ --}}
    <div class="header">
        @include('workflow.components.letterhead')
        <h2>Board Meeting — Conflict of Interest Register</h2>
        <div class="subtitle">Official Record of Conflict Declarations</div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MEETING DETAILS
         ══════════════════════════════════════════════════════════ --}}
    <table class="meeting-info">
        <tr>
            <td class="label">Meeting:</td>
            <td class="value">{{ $meeting->title }}</td>
            <td class="label">Committee:</td>
            <td class="value">{{ optional($meeting->boardCommittee)->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Date:</td>
            <td class="value">{{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->format('l, d F Y') : '—' }}</td>
            <td class="label">Time:</td>
            <td class="value">{{ $meeting->meeting_time ? \Carbon\Carbon::parse($meeting->meeting_time)->format('h:i A') : '—' }}</td>
        </tr>
        <tr>
            <td class="label">Venue:</td>
            <td class="value" colspan="3">{{ $meeting->venue ?? '—' }}</td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════════════════════════
         DECLARATIONS TABLE
         ══════════════════════════════════════════════════════════ --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24px;">#</th>
                <th>Board Member</th>
                <th>Designation</th>
                <th style="width: 55px; text-align: center;">Conflict?</th>
                <th>Person / Entity</th>
                <th>Nature of Interest</th>
                <th style="width: 65px;">Value (KES)</th>
                <th>Remarks</th>
                <th style="width: 75px;">Signed At</th>
                <th style="width: 85px; text-align: center;">Signature</th>
            </tr>
        </thead>
        <tbody>
            @forelse($declarations as $i => $d)
                <tr>
                    <td style="text-align: center; font-weight: 600;">{{ $i + 1 }}</td>
                    <td style="font-weight: 600;">{{ optional($d->boardMember)->names ?? '—' }}</td>
                    <td>{{ optional($d->boardMember)->designation ?? '—' }}</td>
                    <td style="text-align: center;">
                        @if($d->has_conflict)
                            <span class="badge-yes">YES</span>
                        @else
                            <span class="badge-no">NO</span>
                        @endif
                    </td>
                    <td>
                        @if($d->has_conflict && $d->person_name)
                            {{ $d->person_name }}
                            @if($d->person_address)
                                <br><small style="color: #6b7280;">{{ $d->person_address }}</small>
                            @endif
                        @else
                            <span style="color: #9ca3af;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($d->has_conflict && $d->interest_nature)
                            {{ $d->interest_nature }}
                        @else
                            <span style="color: #9ca3af;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($d->has_conflict && $d->interest_value)
                            {{ $d->interest_value }}
                        @else
                            <span style="color: #9ca3af;">—</span>
                        @endif
                    </td>
                    <td>{{ $d->remarks ?? '—' }}</td>
                    <td>
                        @if($d->signed_at)
                            {{ \Carbon\Carbon::parse($d->signed_at)->format('d M Y') }}<br>
                            <small style="color: #6b7280;">{{ \Carbon\Carbon::parse($d->signed_at)->format('h:i A') }}</small>
                        @else
                            <span style="color: #9ca3af;">—</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($d->signature)
                            <img src="{{ $d->signature }}" class="signature-img" alt="Signature">
                        @else
                            <span style="color: #9ca3af;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px; color: #9ca3af;">
                        No signed conflict declarations found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ══════════════════════════════════════════════════════════
         SUMMARY & SIGNATURES
         ══════════════════════════════════════════════════════════ --}}
    <div class="footer">
        <div class="footer-summary">
            <span><strong>{{ $declarations->count() }}</strong> Total Declarations</span>
            <span><strong>{{ $declarations->where('has_conflict', true)->count() }}</strong> With Conflict</span>
            <span><strong>{{ $declarations->where('has_conflict', false)->count() }}</strong> No Conflict</span>
        </div>

        <table class="signature-block">
            <tr>
                <td>
                    <div class="signature-line">
                        Corporation Secretary
                        <div class="signature-sub">Signature & Date</div>
                    </div>
                </td>
                <td>
                    <div class="signature-line">
                        Chairperson
                        <div class="signature-sub">Signature & Date</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            Generated on {{ now()->format('d F Y \a\t h:i A') }} &mdash; {{ setting('institution_acronym', 'ORG') }} eBoard Management System
        </div>
    </div>

</body>
</html>
