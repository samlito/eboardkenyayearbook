<!DOCTYPE html>
<html>
<head>
    <title>Board Almanac PDF</title>
    <style>
        @page { margin: 40px 30px 120px 30px; } /* extra bottom space for footer */

        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .logo { width: 100%; text-align: center; margin-bottom: 20px; }
        .logo img { height: 80px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        .intro p { margin: 5px 0; text-align: justify; }

        /* Signatures (in-body) */
        .signatures { width: 100%; border: 0; border-collapse: collapse; margin-top: 40px; }
        .signatures td { border: 0; vertical-align: top; padding: 10px 5px; width: 50%; }
        .sig-block { margin-top: 40px; }
        .sig-line { border-bottom: 1px solid #000; height: 24px; }
        .sig-label { margin-top: 6px; font-weight: bold; text-transform: uppercase; }
        .sig-title { margin-top: 2px; }
        .sig-date { margin-top: 8px; }
        .avoid-break { page-break-inside: avoid; }

        /* Fixed footer for PDF */
        .pdf-footer {
            position: fixed;
            left: 0; right: 0; bottom: 20px; /* 20px above page edge */
            width: 100%;
            text-align: center;
            font-size: 11px;
        }
        .pdf-footer .line {
            letter-spacing: 2px; /* spreads the dots a little */
            margin: 4px 0 2px 0;
        }
        .pdf-footer .who {
            margin: 0 0 8px 0;
        }
    </style>
</head>
<body>
@php
    $pdfLogo = setting('logo_letterhead') ?: setting('logo_main');
    $logoPath = $pdfLogo ? public_path($pdfLogo) : public_path('images/banner.png');
    if (!file_exists($logoPath)) {
        $logoPath = public_path('images/banner.png');
    }
    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
    $logoMime = str_ends_with(strtolower($logoPath), '.png') ? 'image/png' : 'image/jpeg';
@endphp

@if($logoData)
    <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="{{ setting('institution_acronym', 'ORG') }} Logo" style="height: 180px; width: 100%">
@else
    <h3 style="text-align:center; color: {{ setting('primary_color', '#1a1a1a') }};">{{ setting('institution_name', 'eBoard System') }}</h3>
@endif

<h2>{{ strtoupper(setting('institution_name', 'eBoard System')) }}</h2>
<h3 style="text-align: center;">BOARD ALMANAC FOR THE FY {{ $almanac->financialYear->year }}</h3>

<div class="intro">
    <p><strong>Introduction</strong></p>
    <p>In line with the Mwongozo Code of Governance guidelines and related circulars, the Board has approved the Committee and Board work plan for the FY {{ $almanac->financialYear->name }}. The same is designed to accommodate-</p>
    <ul>
        <li>a) The financial reporting timelines as set out in the Public Finance Management Act;</li>
        <li>b) Board evaluation timelines as communicated by State Corporations Advisory Committee;</li>
        <li>c) Board trainings as guided by Mwongozo Code of Governance;</li>
        <li>d) The Board’s oversight/supervisory role.</li>
        <li>e) The Board Planning role. Accordingly, adequate time has been allocated for meetings to discuss and approve the proposed and on-going projects, develop new policies; discuss and approve budget and procurement plans, and for reviewing the Agency’s policies; and</li>
        <li>f) Risk Management, compliance and the Audit function.</li>
    </ul>
    <p>It is further recommended that:</p>
    <ul>
        <li>a) Committee meetings to be held on the third Tuesday – Thursday of the first month following the closure of the reporting period;</li>
        <li>b) The Board meetings to be held on the first Tuesday or Thursday of the second month following the closure of the reporting period; and</li>
        <li>c) The trainings and field visits will be 5 days’ activities.</li>
    </ul>
</div>

<table>
    <thead>
    <tr>
        <th>SN.</th>
        <th>DATE</th>
        <th>MEETING</th>
        <th>PURPOSE / AGENDA</th>
        <th>BUDGET – KES.</th>
    </tr>
    </thead>
    <tbody>
    @foreach($almanac->schedules as $index => $schedule)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($schedule->meeting_date)->format('l, jS F, Y') }}</td>
            <td>{{ $schedule->boardCommittee->name ?? 'N/A' }}</td>
            <td>{{ $schedule->purpose }}</td>
            <td>{{ number_format($schedule->budget, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Signatures (in-body, if you still want them above the footer) -->
<table class="signatures avoid-break">
    <tr>
        <td>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">SIGNED BY ENG. PHILIP GICHUKI, MBS</div>
                <div class="sig-title">{{ strtoupper(setting('signatory_title_ceo', 'Chief Executive Officer')) }}</div>
                <div class="sig-date">DATED: ____________________________</div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">SIGNED BY MR. TEOBALD MUKUNDI WAMBUGU</div>
                <div class="sig-title">{{ strtoupper(setting('signatory_title_chairperson', 'Chairperson')) }}, BOARD OF DIRECTORS</div>
                <div class="sig-date">DATED: ____________________________</div>
            </div>
        </td>
    </tr>
</table>

<!-- Fixed footer (prints on every page) -->
<!-- Fixed footer (prints on every page) -->
<div class="pdf-footer">
    <table style="margin: 0 auto; border:0; border-collapse: collapse; width: 100%;">
        <tr>
            <td style="border:0; text-align:center; width:50%; padding:1px;">
                <div style="margin-bottom:1px;">………………………………………………</div>
                <div>Signed by Eng. Philip Gichuki<br>
                    {{ setting('signatory_title_ceo', 'Chief Executive Officer') }} / Secretary to the Board</div>
            </td>
            <td style="border:0; text-align:center; width:50%; padding:1px;">
                <div style="margin-bottom:1px;">……………………………………………</div>
                <div>Signed by Mr. Teobald Mukundi Wambugu<br>
                    {{ setting('signatory_title_chairperson', 'Chairperson') }}, Board of Directors</div>
            </td>
        </tr>
    </table>
</div>


</body>
</html>
