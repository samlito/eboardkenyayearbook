@component('mail::message')
    # Official Board Meeting Invitation

    Dear Board Member,

    You are officially invited to attend the board meeting detailed below:

    **Title:** {{ $meeting->title }}
    **Date:** {{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}
    **Venue:** {{ $meeting->venue }}

    Please find the attached official notice for further details.

    @component('mail::button', ['url' => url('/')])
        Visit Portal
    @endcomponent

    Regards,
    {{ setting('signatory_title_ceo', 'Chief Executive Officer') }}
    {{ setting('institution_name', 'eBoard System') }}
@endcomponent
