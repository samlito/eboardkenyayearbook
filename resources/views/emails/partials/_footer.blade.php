<div style="background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e9ecef;">
    <p style="color: #6c757d; font-size: 12px; margin: 0;">
        &copy; {{ date('Y') }} {{ setting('institution_name', 'eBoard System') }}. All rights reserved.
    </p>
    <p style="color: #adb5bd; font-size: 11px; margin: 5px 0 0;">
        {{ setting('institution_acronym', 'ORG') }} eBoard System &bull;
        @if(setting('website_url'))
            <a href="{{ setting('website_url') }}" style="color: {{ setting('primary_color', '#1a1a1a') }};">{{ setting('website_url') }}</a>
        @endif
    </p>
</div>
