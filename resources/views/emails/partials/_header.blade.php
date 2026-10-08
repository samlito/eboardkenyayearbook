<div style="background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }}, {{ setting('secondary_color', '#2b2b2b') }}); padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0;">
    @if(setting('logo_main'))
        <img src="{{ asset(setting('logo_main')) }}" alt="{{ setting('institution_acronym', 'ORG') }}" style="max-height: 60px; margin-bottom: 10px;">
    @endif
    <h2 style="color: #ffffff; margin: 0; font-size: 18px; font-weight: 600;">
        {{ setting('institution_acronym', 'ORG') }} eBoard System
    </h2>
</div>
