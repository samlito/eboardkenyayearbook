<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Board Paper Approved - {{ setting('institution_acronym', 'eBoard') }}</title>
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
                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">Dear Sir/Madam,</p>

                            {{-- Success Badge --}}
                            <div style="text-align: center; margin: 20px 0;">
                                <div style="display: inline-block; background-color: #e6f6ec; border-radius: 50%; width: 60px; height: 60px; line-height: 60px; text-align: center;">
                                    <span style="color: #099139; font-size: 28px;">&#10003;</span>
                                </div>
                            </div>

                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 0 0 20px; text-align: center;">
                                {{ $notificationMessage ?? 'Your board paper has been approved and will be included in the meeting agenda.' }}
                            </p>

                            @if(isset($notificationTitle))
                                <div style="background-color: #e6f6ec; border-left: 4px solid #099139; padding: 15px 20px; border-radius: 4px; margin: 20px 0;">
                                    <p style="color: #7e8299; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 5px;">Board Paper</p>
                                    <p style="color: #0f0f0f; font-size: 15px; font-weight: 600; margin: 0;">{{ $notificationTitle }}</p>
                                </div>
                            @endif

                            @if(isset($paperNumber))
                                <div style="background-color: #f8f8f8; padding: 15px 20px; border-radius: 4px; margin: 20px 0; border: 1px solid #eff2f5;">
                                    <p style="margin: 0;"><strong style="color: #0f0f0f;">Paper Number:</strong> <span style="color: #3f4254;">{{ $paperNumber }}</span></p>
                                </div>
                            @endif

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $actionUrl ?? url('/workflow/dashboard') }}"
                                   style="display: inline-block; background: linear-gradient(135deg, #099139, #077a2e); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    View Approved Paper
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
