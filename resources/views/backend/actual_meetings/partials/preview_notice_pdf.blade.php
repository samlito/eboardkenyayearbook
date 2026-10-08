<style>
    .official-letter {
        background: #fdfcf9; /* subtle conqueror paper look */
        padding: 40px;
        border: 1px solid #ccc;
        font-family: 'Georgia', serif;
        max-width: 900px;
        margin: auto;
    }

    .letterhead {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 3px solid #099139;
        padding-bottom: 10px;
    }

    .letterhead img {
        height: 80px;
        margin-bottom: 10px;
    }

    .letterhead h2 {
        font-size: 24px;
        margin-bottom: 0;
        color: #099139;
    }

    .letterhead p {
        font-size: 14px;
        color: #333;
        margin-bottom: 0;
    }

    .notice-content p {
        font-size: 16px;
        line-height: 1.6;
    }

    .signature-block {
        margin-top: 50px;
        text-align: left;
    }

    .signature-block img {
        height: 60px;
        margin-bottom: 5px;
    }

    .signature-name {
        font-weight: bold;
        font-size: 16px;
    }

    .signature-title {
        font-size: 15px;
        color: #333;
    }
</style>

<div class="official-letter">
    <!-- Header Letterhead -->
    <div class="letterhead text-center">
        @php
            $pdfLogo = setting('logo_letterhead') ?: setting('logo_main');
            $pdfLogoPath = $pdfLogo ? public_path($pdfLogo) : public_path('images/banner.png');
        @endphp
        <img src="{{ file_exists($pdfLogoPath) ? $pdfLogoPath : public_path('images/banner.png') }}" alt="{{ setting('institution_acronym', 'ORG') }} Logo">
        <h2>{{ strtoupper(setting('institution_name', 'eBoard System')) }}</h2>
        <p>{{ setting('postal_address', '') }}{{ setting('postal_address') && setting('website_url') ? ' | ' : '' }}@if(setting('website_url'))Website: <a href="{{ setting('website_url') }}">{{ setting('website_url') }}</a>@endif</p>
    </div>

    <!-- Title -->
    <h4 class="text-center mb-4">📄 Official Board Meeting Notice</h4>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            @if($meeting->committee && $meeting->committee->members->count())
                <div class="mb-4">
                    <h6 class="fw-bold">📋 Board Members Attending:</h6>
                    <ul class="list-group">
                        @foreach($meeting->committee->members as $member)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        {{ $member->assignsalutation->name ?? '' }} {{ $member->names }}<br>
                        <small class="text-muted">{{ $member->designation }}</small>
                    </span>
                                <span class="text-muted">{{ $member->phone }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- insert the above list-group block here -->
        </div>
    </div>


    <!-- Meeting Details -->
    <p><strong>Title:</strong> {{ $meeting->title }}</p>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}</p>
    <p><strong>Venue:</strong> {{ $meeting->venue }}</p>
    <p><strong>Committee:</strong> {{ $meeting->committee->name ?? 'N/A' }}</p>

    <hr>

    <!-- Notice Body -->
    <div class="notice-content mb-4">
        {!! $meeting->officialNotice->notice_text !!}
    </div>

    <!-- Signature -->
    <div class="signature-block mt-5">
        <img src="{{ asset('images/signature_ceo.png') }}" alt="CEO Signature" style="height: 60px;">
        <div class="fw-bold">Eng. Philip Gichuki, MBS</div>
        <div class="text-muted">{{ setting('signatory_title_ceo', 'Chief Executive Officer') }}</div>
    </div>
    <!-- Signature -->


</div>
