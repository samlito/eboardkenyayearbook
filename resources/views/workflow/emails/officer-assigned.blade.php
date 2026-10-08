<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Assignment - {{ setting('institution_acronym', 'eBoard') }}</title>
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
                                {{ $notificationMessage ?? 'You have been assigned as an Action Officer for an upcoming board meeting. You are required to prepare and submit a board paper for your designated agenda item.' }}
                            </p>

                            @if(isset($notificationTitle))
                                <div style="background-color: #ececec; border-left: 4px solid {{ setting('primary_color', '#1a1a1a') }}; padding: 15px 20px; border-radius: 4px; margin: 20px 0;">
                                    <p style="color: #7e8299; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 5px;">Meeting</p>
                                    <p style="color: #0f0f0f; font-size: 15px; font-weight: 600; margin: 0;">{{ $notificationTitle }}</p>
                                </div>
                            @endif

                            @if(isset($agendaItemNumber) || isset($agendaItemTitle))
                                <div style="background-color: #f8f8f8; padding: 15px 20px; border-radius: 4px; margin: 20px 0; border: 1px solid #eff2f5;">
                                    @if(isset($agendaItemNumber))
                                        <p style="margin: 0 0 5px;"><strong style="color: #0f0f0f;">Agenda Item:</strong> <span style="color: #3f4254;">{{ $agendaItemNumber }}</span></p>
                                    @endif
                                    @if(isset($agendaItemTitle))
                                        <p style="margin: 0;"><strong style="color: #0f0f0f;">Topic:</strong> <span style="color: #3f4254;">{{ $agendaItemTitle }}</span></p>
                                    @endif
                                </div>
                            @endif

                            <p style="color: #3f4254; font-size: 15px; line-height: 1.6; margin: 20px 0;">
                                Please log in to the eBoard system to view the details of your assignment and begin preparing your board paper.
                            </p>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $actionUrl ?? url('/workflow/dashboard') }}"
                                   style="display: inline-block; background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }}, {{ setting('secondary_color', '#2b2b2b') }}); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    View Assignment
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
