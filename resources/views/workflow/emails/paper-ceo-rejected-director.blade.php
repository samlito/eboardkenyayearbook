<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Board Paper Rejected by CEO - Director Notification - {{ setting('institution_acronym', 'eBoard') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f8f8; font-family: 'Segoe UI', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f8f8; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    {{-- Header --}}
                    <tr>
                        <td>@include('emails.partials._header')</td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">Dear Director,</p>

                            {{-- Alert Badge --}}
                            <div style="text-align: center; margin: 20px 0;">
                                <div style="display: inline-block; background-color: #fff3cd; border-radius: 50%; width: 60px; height: 60px; line-height: 60px; text-align: center;">
                                    <span style="color: #856404; font-size: 28px;">&#9888;</span>
                                </div>
                            </div>

                            <h2 style="text-align: center; color: #dc3545; font-size: 18px; margin: 0 0 15px;">Board Paper Rejected by CEO</h2>

                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 0 0 20px; text-align: center;">
                                {{ $notificationMessage ?? 'A board paper you reviewed has been rejected by the CEO. Please coordinate with the author for revisions.' }}
                            </p>

                            @if(isset($paper))
                                <div style="background-color: #fce4ec; border-left: 4px solid #dc3545; padding: 15px 20px; border-radius: 4px; margin: 20px 0;">
                                    <p style="color: #7e8299; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 5px;">Board Paper</p>
                                    <p style="color: #0f0f0f; font-size: 15px; font-weight: 600; margin: 0;">{{ $paper->title }}</p>
                                    <p style="color: #7e8299; font-size: 12px; margin: 5px 0 0;">{{ $paper->paper_number }}</p>
                                </div>
                            @endif

                            @if(isset($comment) && $comment)
                                <div style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px 20px; border-radius: 4px; margin: 20px 0;">
                                    <p style="color: #7e8299; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 5px;">CEO Feedback</p>
                                    <p style="color: #0f0f0f; font-size: 14px; margin: 0;">{{ $comment }}</p>
                                </div>
                            @endif

                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 20px 0;">
                                Please review the CEO's comments and coordinate with the paper author to make the necessary revisions before resubmission.
                            </p>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $actionUrl ?? url('/workflow/dashboard') }}"
                                   style="display: inline-block; background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }}, {{ setting('secondary_color', '#2b2b2b') }}); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    View Paper Details
                                </a>
                            </div>

                            <p style="color: #7e8299; font-size: 13px; line-height: 1.6; margin: 20px 0 0;">
                                If you are unable to click the button, copy and paste this link:
                                <br><a href="{{ $actionUrl ?? url('/workflow/dashboard') }}" style="color: {{ setting('primary_color', '#1a1a1a') }}; word-break: break-all;">{{ $actionUrl ?? url('/workflow/dashboard') }}</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td>@include('emails.partials._footer')</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
