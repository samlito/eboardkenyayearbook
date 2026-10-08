<p>Dear <strong>{{ $name }}</strong>,</p>

<p>Your one-time verification code to log in to the <strong>{{ setting('institution_name', 'eBoard System') }}</strong> Board Portal is:</p>

<div style="text-align: center; margin: 24px 0;">
    <div style="display: inline-block; background: #fafafa; border: 2px solid {{ setting('primary_color', '#1a1a1a') }}; border-radius: 12px; padding: 16px 32px;">
        <span style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: {{ setting('primary_color', '#1a1a1a') }}; font-family: monospace;">{{ $otp }}</span>
    </div>
</div>

<p style="padding: 12px 16px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px; font-size: 13px;">
    This code expires in <strong>5 minutes</strong>. Do not share it with anyone. If you did not initiate this login, please ignore this email.
</p>

<p>Best Regards,<br>
<strong>{{ setting('institution_name', 'eBoard System') }}</strong></p>
