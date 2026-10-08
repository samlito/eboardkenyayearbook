<!DOCTYPE html>
<html>
<head>
    <title>Board Meeting Notice Preview</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            color: #000;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
        }

        .section {
            margin-bottom: 30px;
        }

        ul {
            padding-left: 20px;
        }

        .agenda-item {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<h2>Official Board Meeting Notice</h2>

<div class="section">
    <strong>Meeting Title:</strong> {{ $meeting->title }}<br>
    <strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}<br>
    <strong>Venue:</strong> {{ $meeting->venue }}<br>
    <strong>Committee:</strong> {{ $meeting->committee->name ?? 'N/A' }}
</div>

<div class="section">
    <strong>Notice:</strong>
    <div style="margin-top: 10px;">
        {!! $meeting->officialNotice->notice_text !!}
    </div>
</div>

{{--<div class="section">--}}
{{--    <strong>Agenda Items:</strong>--}}
{{--    <ul>--}}
{{--        @foreach($orderedPapers as $index => $paper)--}}
{{--            <li class="agenda-item">--}}
{{--                <strong>Agenda {{ $index + 1 }}:</strong> {{ $paper->title }}--}}
{{--            </li>--}}
{{--        @endforeach--}}
{{--    </ul>--}}
{{--</div>--}}

<div class="section" style="text-align: right; margin-top: 60px;">
    Signed,<br>
    <strong>Chief Executive Officer</strong>
</div>

</body>
</html>
