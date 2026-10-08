<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Conflict of Interest Register</title>
    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #099139;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo {
            width: 100%;
            height: auto;
            margin-bottom: 5px;
        }

        .agency-name {
            font-size: 16px;
            font-weight: bold;
            color: #099139;
            text-transform: uppercase;
        }

        .doc-title {
            font-size: 15px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .meeting-info {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th {
            background-color: #099139;
            color: #fff;
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
        }

        td {
            padding: 6px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: center;
            color: #555;
        }
    </style>
</head>
<body>

<div class="header">
    @php
        $pdfLogo = setting('logo_letterhead') ?: setting('logo_main');
        $pdfLogoPath = $pdfLogo ? public_path($pdfLogo) : public_path('images/banner.png');
    @endphp
    <img src="{{ file_exists($pdfLogoPath) ? $pdfLogoPath : public_path('images/banner.png') }}" alt="{{ setting('institution_acronym', 'ORG') }} Logo" class="logo">
    <div class="agency-name">{{ setting('institution_name', 'eBoard System') }}</div>
    <div class="doc-title">Conflict of Interest Register</div>
</div>

<div class="meeting-info">
    <strong>Meeting Title:</strong> {{ $meeting->title }} <br>
    <strong>Committee:</strong> {{ $meeting->committee->name ?? 'N/A' }}
</div>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Board Member</th>
        <th>Conflict Status</th>
        <th>Declared Interest (if any)</th>
        <th>Signed At</th>
    </tr>
    </thead>
    <tbody>
    @forelse($declarations as $index => $decl)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                {{ $decl->boardMember->assignsalutation->name ?? '' }}
                {{ $decl->boardMember->names }}
            </td>
            <td>{{ $decl->conflict_status }}</td>
            <td>{{ $decl->details ?? '—' }}</td>
            <td>{{ $decl->created_at ? $decl->created_at->format('d M Y, h:i A') : '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" style="text-align: center;">No declarations found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    Generated on {{ now()->format('d M Y, h:i A') }} <br>
    {{ setting('institution_name', 'eBoard System') }}
</div>

</body>
</html>
