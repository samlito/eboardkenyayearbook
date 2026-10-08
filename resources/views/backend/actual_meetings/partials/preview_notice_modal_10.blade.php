{{--<style>--}}
{{--    .official-letter {--}}
{{--        background: #fdfcf9; /* subtle conqueror paper look */--}}
{{--        padding: 40px;--}}
{{--        border: 1px solid #ccc;--}}
{{--        font-family: 'Georgia', serif;--}}
{{--        max-width: 900px;--}}
{{--        margin: auto;--}}
{{--    }--}}

{{--    .letterhead {--}}
{{--        text-align: center;--}}
{{--        margin-bottom: 20px;--}}
{{--        border-bottom: 3px solid #099139;--}}
{{--        padding-bottom: 10px;--}}
{{--    }--}}

{{--    .letterhead img {--}}
{{--        height: 80px;--}}
{{--        margin-bottom: 10px;--}}
{{--    }--}}

{{--    .letterhead h2 {--}}
{{--        font-size: 24px;--}}
{{--        margin-bottom: 0;--}}
{{--        color: #099139;--}}
{{--    }--}}

{{--    .letterhead p {--}}
{{--        font-size: 14px;--}}
{{--        color: #333;--}}
{{--        margin-bottom: 0;--}}
{{--    }--}}

{{--    .notice-content p {--}}
{{--        font-size: 16px;--}}
{{--        line-height: 1.6;--}}
{{--    }--}}

{{--    .signature-block {--}}
{{--        margin-top: 50px;--}}
{{--        text-align: left;--}}
{{--    }--}}

{{--    .signature-block img {--}}
{{--        height: 60px;--}}
{{--        margin-bottom: 5px;--}}
{{--    }--}}

{{--    .signature-name {--}}
{{--        font-weight: bold;--}}
{{--        font-size: 16px;--}}
{{--    }--}}

{{--    .signature-title {--}}
{{--        font-size: 15px;--}}
{{--        color: #333;--}}
{{--    }--}}
{{--</style>--}}

{{--<div class="official-letter">--}}
{{--    <!-- Header Letterhead -->--}}
{{--    <div class="letterhead text-center">--}}
{{--        <img src="{{ asset('images/banner.png') }}" alt="TANA Logo"style="width: auto;height: auto">--}}
{{--        <h2>TANA WATER WORKS DEVELOPMENT AGENCY</h2>--}}
{{--        <p>P.O. Box 1292 – 10100, Nyeri | Website: <a href="https://tanawwda.go.ke">www.tanawwda.go.ke</a></p>--}}
{{--    </div>--}}



{{--    <!-- Notice Body -->--}}
{{--    <div class="notice-content mb-4">--}}
{{--        {!! $meeting->officialNotice->notice_text !!}--}}
{{--    </div>--}}

{{--    <!-- Signature -->--}}
{{--    <div class="signature-block mt-5">--}}
{{--        <img src="{{ asset('images/signature_ceo.png') }}" alt="CEO Signature" style="height: 90px;">--}}
{{--        <div class="fw-bold">Eng. Philip Gichuki, MBS</div>--}}
{{--        <div class="text-muted">Chief Executive Officer</div>--}}
{{--    </div>--}}
{{--    <!-- Signature -->--}}


{{--</div>--}}


<style>
    .official-letter {
        background: #fdfcf9; /* subtle conqueror paper look */
        padding: 40px;
        border: 1px solid #ccc;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.5;
        max-width: 900px;
        margin: auto;
        position: relative;
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
        font-size: 20pt;
        margin-bottom: 0;
        color: #099139;
    }

    .letterhead p {
        font-size: 11pt;
        color: #333;
        margin-bottom: 0;
    }

    /* Reference and Date row */
    .ref-date {
        display: flex;
        justify-content: space-between;
        margin: 20px 0;
        font-size: 12pt;
    }

    .notice-content p {
        font-size: 12pt;
        line-height: 1.5;
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
        font-size: 12pt;
    }

    .signature-title {
        font-size: 11pt;
        color: #333;
    }

    /* Footer */
    .footer {
        margin-top: 40px;
        text-align: center;
        border-top: 1px solid #ccc;
        padding-top: 10px;
    }

    .footer img {
        max-width: 100%;
        height: auto;
    }
</style>



<div class="official-letter">
    <!-- Header Letterhead -->
    <div class="letterhead text-center">
        <img src="{{ asset(setting('logo_main', 'backend/assets/images/logo.png')) }}" alt="{{ setting('institution_acronym', 'ORG') }} Logo" style="width: auto;height: auto">
    </div>

    <!-- Reference and Date -->
    <div class="ref-date">
        <div><strong>Reference:</strong> {{ $meeting->officialNotice10->reference_number ?? 'Ref No.' }}</div>
        <div>
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->officialNotice10->submission_date)->format('d F Y') ?? now()->format('d F Y') }}
        </div>
    </div>

    <!-- Notice Body -->
    <div class="notice-content mb-4">
        {!! $meeting->officialNotice10->notice_text !!}
    </div>

    <!-- Signature -->
    <div class="signature-block mt-5">
        <img src="{{ asset('images/signature_ceo.png') }}" alt="CEO Signature" style="height: 90px;">
        <div class="signature-name">Eng. Philip Gichuki, MBS</div>
        <div class="signature-title" style="text-decoration: underline;font-weight: bold">{{ strtoupper(setting('signatory_title_ceo', 'Chief Executive Officer')) }}</div>

    </div>

    <!-- CC Block -->
    <style>
        .cc-block {
            display: flex;
            align-items: center;
            margin-top: 30px;
            font-size: 12pt;
        }

        .cc-left {
            flex: 1;
        }

        .cc-right {
            display: flex;
            align-items: center;
            font-size: 12pt;
            font-style: italic;
        }

        .cc-brace {
            font-size: 40px;   /* make it big */
            font-weight: bold;
            line-height: 1;
            margin-right: 8px; /* space between brace and text */
        }
    </style>

    <div class="cc-block">
        <div class="cc-left">
            <strong>CC:</strong> Winfred Njoroge<br>
            <strong>Representative of the</strong> Inspector General<br>
            State Corporations - Harambee Avenue<br>
            P. O. Box 74434 – 00200<br>
            <u><b>NAIROBI</b></u>
        </div>

        <div class="cc-right">
            <div class="cc-brace">}</div>
            <div>Please plan to attend</div>
        </div>
    </div>


    <!-- Footer -->
    <div class="footer">
        <img src="{{ asset('images/kebsfooter.png') }}" alt="Footer Image">
    </div>
</div>
