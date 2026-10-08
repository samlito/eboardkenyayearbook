<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ setting('institution_acronym', 'eBoard') }} - Board Meeting Invitation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

<div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 6px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    @include('emails.partials._header')

    <div style="padding: 30px;">
        <p style="font-size: 16px;">Dear <strong>{{ $user->names }}</strong>,</p>

        <p style="font-size: 16px;">You are invited to a board meeting as scheduled below:</p>

        <table style="width: 100%; font-size: 16px; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td style="padding: 8px 0; width: 30%; font-weight: bold;">Title:</td>
                <td style="padding: 8px 0;">{{ $meeting->title }}</td>
            </tr>
            <tr style="background-color: #f9f9f9;">
                <td style="padding: 8px 0; font-weight: bold;">Date:</td>
                <td style="padding: 8px 0;">{{ \Carbon\Carbon::parse($meeting->date)->format('F j, Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Venue:</td>
                <td style="padding: 8px 0;">{{ $meeting->venue }}</td>
            </tr>
            <tr style="background-color: #f9f9f9;">
                <td style="padding: 8px 0; font-weight: bold;">Type:</td>
                <td style="padding: 8px 0;">{{ $meeting->type }}</td>
            </tr>
        </table>

{{--        <p style="font-size: 16px; margin-top: 20px;">Please confirm your attendance by replying to this email.</p>--}}

        <p style="font-size: 16px;">Regards,<br><strong>CEO's Office</strong></p>
    </div>

    @include('emails.partials._footer')
</div>

</body>
</html>
