<div style="padding: 0;">

    <p style="font-size: 16px; color: #333; margin: 0 0 16px;">Dear <strong>{{ $name }}</strong>,</p>

    <p style="font-size: 15px; color: #444; line-height: 1.7; margin: 0 0 20px;">
        Welcome! Your account on the <strong>{{ setting('institution_name', 'eBoard System') }}</strong> Board Management System has been created successfully.
    </p>

    @if(!empty($roleName))
        <div style="background: {{ setting('primary_color', '#1a1a1a') }}12; border-left: 4px solid {{ setting('primary_color', '#1a1a1a') }}; padding: 12px 16px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
            <span style="font-size: 13px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Assigned Role</span><br>
            <span style="font-size: 18px; font-weight: 700; color: {{ setting('primary_color', '#1a1a1a') }};">{{ $roleName }}</span>
        </div>
    @endif

    <div style="background: #fafafa; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; margin-bottom: 24px;">
        <div style="background: {{ setting('primary_color', '#1a1a1a') }}; padding: 10px 16px;">
            <span style="color: #fff; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Your Login Credentials</span>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #374151; font-size: 14px; width: 130px; background: #f9fafb;">
                    <span style="display: inline-flex; align-items: center; gap: 6px;">&#9993; Email</span>
                </td>
                <td style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-size: 15px; color: #0d0d0d; background: #fff;">
                    {{ $email }}
                </td>
            </tr>
            <tr>
                <td style="padding: 14px 16px; font-weight: 600; color: #374151; font-size: 14px; background: #f9fafb;">
                    <span style="display: inline-flex; align-items: center; gap: 6px;">&#128274; Password</span>
                </td>
                <td style="padding: 14px 16px; font-size: 16px; color: #0d0d0d; background: #fff;">
                    <code style="background: #f3f4f6; padding: 6px 14px; border-radius: 6px; font-family: 'Courier New', monospace; font-size: 16px; letter-spacing: 2px; font-weight: 700; color: {{ setting('primary_color', '#1a1a1a') }}; border: 1px solid #e5e7eb;">{{ $password }}</code>
                </td>
            </tr>
        </table>
    </div>

    <div style="background: #fef3c7; border: 1px solid #fcd34d; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 14px 16px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #92400e; line-height: 1.6;">
            <strong>&#9888; Security Notice:</strong> For your protection, you will be required to <strong>change your password</strong> when you log in for the first time. Please do not share your credentials with anyone.
        </p>
    </div>

    <div style="text-align: center; margin: 28px 0;">
        <a href="{{ url('/login') }}"
           style="display: inline-block; background: {{ setting('primary_color', '#1a1a1a') }}; color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 15px; letter-spacing: 0.3px;">
            Login to Your Account &rarr;
        </a>
    </div>

    <p style="font-size: 12px; color: #9ca3af; text-align: center; margin: 0 0 20px; line-height: 1.6;">
        If the button above doesn't work, copy and paste this link into your browser:<br>
        <a href="{{ url('/login') }}" style="color: {{ setting('primary_color', '#1a1a1a') }}; word-break: break-all;">{{ url('/login') }}</a>
    </p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">

    <p style="font-size: 14px; color: #6b7280; margin: 0;">
        Best Regards,<br>
        <strong style="color: #374151;">{{ setting('institution_name', 'eBoard System') }}</strong>
    </p>

</div>
