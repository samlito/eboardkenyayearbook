<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f6f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f6f6f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">

                    {{-- Header --}}
                    @include('emails.partials._header')

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px 36px;">

                            {{-- Icon --}}
                            <div style="text-align: center; margin-bottom: 24px;">
                                <div style="display: inline-block; width: 72px; height: 72px; background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }}15, {{ setting('primary_color', '#1a1a1a') }}25); border-radius: 50%; line-height: 72px; text-align: center;">
                                    <span style="font-size: 32px;">&#9993;</span>
                                </div>
                            </div>

                            {{-- Greeting --}}
                            <h1 style="color: #0e0e0e; font-size: 22px; font-weight: 700; text-align: center; margin: 0 0 8px;">
                                Verify Your Email Address
                            </h1>
                            <p style="color: #6b7280; font-size: 14px; text-align: center; margin: 0 0 28px; line-height: 1.5;">
                                You're almost ready to start using the {{ setting('institution_acronym', 'ORG') }} eBoard System
                            </p>

                            {{-- Divider --}}
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 0 0 28px;">

                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 0 0 8px;">
                                Hello <strong>{{ $user->name ?? 'there' }}</strong>,
                            </p>

                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 0 0 20px;">
                                Thank you for registering on the <strong>{{ setting('institution_name', 'eBoard System') }}</strong> board management platform. To complete your account setup and access the system, please verify your email address by clicking the button below.
                            </p>

                            {{-- CTA Button --}}
                            <div style="text-align: center; margin: 32px 0;">
                                <a href="{{ $verificationUrl }}"
                                   style="display: inline-block; background: {{ setting('primary_color', '#1a1a1a') }}; color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px; box-shadow: 0 4px 12px {{ setting('primary_color', '#1a1a1a') }}40;">
                                    &#10003;&nbsp; Verify Email Address
                                </a>
                            </div>

                            {{-- Expiry Note --}}
                            <div style="background: #fefce8; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 18px; margin: 24px 0;">
                                <p style="color: #92400e; font-size: 13px; margin: 0; line-height: 1.5;">
                                    <strong>&#9202; This link expires in 60 minutes.</strong>
                                    If you did not create an account, no further action is required.
                                </p>
                            </div>

                            {{-- Help Section --}}
                            <div style="background: #f9fafb; border-radius: 8px; padding: 18px 20px; margin: 24px 0 0;">
                                <p style="color: #6b7280; font-size: 13px; margin: 0 0 10px; font-weight: 600;">
                                    Having trouble with the button?
                                </p>
                                <p style="color: #6b7280; font-size: 12px; margin: 0; line-height: 1.6; word-break: break-all;">
                                    Copy and paste this URL into your browser:<br>
                                    <a href="{{ $verificationUrl }}" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ $verificationUrl }}</a>
                                </p>
                            </div>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    @include('emails.partials._footer')

                </table>

                {{-- Sub-footer --}}
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%;">
                    <tr>
                        <td style="padding: 20px 36px; text-align: center;">
                            <p style="color: #9ca3af; font-size: 11px; margin: 0; line-height: 1.5;">
                                This is an automated message from the {{ setting('institution_acronym', 'ORG') }} eBoard System.
                                Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
