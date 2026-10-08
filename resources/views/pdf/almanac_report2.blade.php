<!DOCTYPE html>
<html>
<head>
    <title>Almanac Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid black; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<div class="header">
    <h2>Almanac Report</h2>
    <p><strong>Financial Year:</strong> {{ $almanac->financial_year }}</p>
    <p><strong>Budget:</strong> {{ number_format($almanac->budget, 2) }}</p>
    <p><strong>Status:</strong> {{ $almanac->status }}</p>
    <p><strong>Generated On:</strong> {{ $timestamp }}</p>
</div>

<h3>Schedules</h3>
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Meeting Type</th>
        <th>Activity</th>

        <th>Budget</th>
    </tr>
    </thead>
    <tbody>
    @foreach($schedules as $key => $schedule)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $schedule->meeting_date }}</td>
            <td>{{ $schedule->meeting->name }}</td>
            <td>{{ $schedule->purpose }}</td>

            <td>{{ number_format($schedule->budget, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
