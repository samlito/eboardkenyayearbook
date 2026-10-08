<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Board Paper Status Update</title>
</head>
<body style="font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; padding: 30px;">

<div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="background-color: #099139; color: white; padding: 20px; text-align: center;">
        <h2>Board Paper Status Updated</h2>
    </div>

    <div style="padding: 20px; color: #333;">
        <p>Dear <strong>{{ $paper->user->name }}</strong>,</p>

        <p>Your board paper titled:</p>
        <p style="font-weight: bold;">"{{ $paper->title }}"</p>

        <p>has been reviewed by the CEO.</p>

        <p>
            <strong>Status:</strong>
            @if($paper->status === 'Approved')
                <span style="color: green;">Approved</span>
            @elseif($paper->status === 'Needs Revision')
                <span style="color: red;">Needs Revision</span>
            @else
                <span style="color: orange;">Pending</span>
            @endif
        </p>

        @if($paper->ceo_comments)
            <p><strong>Comments:</strong></p>
            <blockquote style="background-color: #f0f0f0; padding: 10px; border-left: 5px solid #099139;">
                {{ $paper->ceo_comments }}
            </blockquote>
        @endif

        <div style="margin: 20px 0; text-align: center;">
            <a href="{{ url('/board-papers/' . $paper->id) }}"
               style="display: inline-block; background-color: #099139; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;">
                View Paper
            </a>
        </div>

        <p>Thank you,</p>
        <p><strong>{{ config('app.name') }}</strong></p>
    </div>
</div>

</body>
</html>
