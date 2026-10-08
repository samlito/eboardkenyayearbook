<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Board Meeting Notice - {{ setting('institution_acronym', 'eBoard') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f8f8; font-family: 'Segoe UI', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f8f8; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <tr>
                        <td>@include('emails.partials._header')</td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                Dear {{ isset($member) ? ($member->names ?? $member->name ?? 'Board Member') : 'Board Member' }},
                            </p>
                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                {{ $notificationMessage ?? 'You have received an official board meeting notice.' }}
                            </p>
                            @if(isset($notice))
                                <div style="background-color: #f8f8f8; border-left: 4px solid {{ setting('primary_color', '#1a1a1a') }}; padding: 15px 20px; border-radius: 4px; margin: 20px 0;">
                                    <p style="color: #7e8299; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 5px;">Official Board Notice</p>
                                    <p style="color: #0f0f0f; font-size: 15px; font-weight: 600; margin: 0;">{{ $notice->subject ?? '' }}</p>
                                    <p style="color: #7e8299; font-size: 13px; margin: 5px 0 0;">Ref: {{ $notice->reference_number ?? '' }}</p>
                                    @if($notice->meeting)
                                        <p style="color: #7e8299; font-size: 13px; margin: 5px 0 0;">
                                            Meeting: {{ $notice->meeting->title ?? '' }}
                                            @if($notice->meeting->meeting_date)
                                                | {{ \Carbon\Carbon::parse($notice->meeting->meeting_date)->format('d M Y') }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            @endif
                            <div style="background-color: #f4f4f4; border-radius: 6px; padding: 15px 20px; margin: 20px 0;">
                                <p style="color: #3f4254; font-size: 14px; margin: 0;">
                                    <strong style="color: {{ setting('primary_color', '#1a1a1a') }};">📎 PDF Attached</strong><br>
                                    The official board notice is attached to this email as a PDF document. You may also view it online in the eBoard system.
                                </p>
                            </div>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $actionUrl ?? url('/workflow/dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }}, {{ setting('secondary_color', '#2b2b2b') }}); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    View Board Notice
                                </a>
                            </div>
                            <p style="color: #7e8299; font-size: 13px; line-height: 1.6; margin: 20px 0 0;">
                                If you are unable to click the button, copy and paste this link:<br>
                                <a href="{{ $actionUrl ?? url('/workflow/dashboard') }}" style="color: {{ setting('primary_color', '#1a1a1a') }}; word-break: break-all;">{{ $actionUrl ?? url('/workflow/dashboard') }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>@include('emails.partials._footer')</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
