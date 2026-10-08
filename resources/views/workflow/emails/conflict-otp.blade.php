<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conflict of Interest OTP - {{ setting('institution_acronym', 'eBoard') }}</title>
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

                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                {{ $notificationMessage ?? 'Please use the following One-Time Password (OTP) to verify your Conflict of Interest declaration.' }}
                            </p>

                            {{-- OTP Code --}}
                            <div style="text-align: center; margin: 30px 0;">
                                <div style="display: inline-block; background-color: #f8f8f8; border: 2px dashed {{ setting('primary_color', '#1a1a1a') }}; border-radius: 8px; padding: 20px 40px;">
                                    <p style="color: #7e8299; font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 8px;">Your Verification Code</p>
                                    <p style="color: {{ setting('primary_color', '#1a1a1a') }}; font-size: 36px; font-weight: 700; letter-spacing: 8px; margin: 0; font-family: 'Courier New', monospace;">
                                        {{ $otpCode ?? '------' }}
                                    </p>
                                </div>
                            </div>

                            @if(isset($notificationTitle))
                                <div style="background-color: #f8f8f8; border-left: 4px solid {{ setting('primary_color', '#1a1a1a') }}; padding: 15px 20px; border-radius: 4px; margin: 20px 0;">
                                    <p style="color: #7e8299; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 5px;">Meeting</p>
                                    <p style="color: #0f0f0f; font-size: 15px; font-weight: 600; margin: 0;">{{ $notificationTitle }}</p>
                                </div>
                            @endif

                            <div style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 12px 20px; border-radius: 4px; margin: 20px 0;">
                                <p style="color: #856404; font-size: 13px; margin: 0;">
                                    <strong>Important:</strong> This OTP is valid for 10 minutes. Do not share this code with anyone. If you did not request this verification, please contact the Board Secretary immediately.
                                </p>
                            </div>

                            <p style="color: #7e8299; font-size: 13px; line-height: 1.6; margin: 20px 0 0;">
                                Enter this code in the eBoard system to verify your declaration and proceed to sign.
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
