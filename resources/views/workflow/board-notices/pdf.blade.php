<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $notice->reference_number }} - Board Notice</title>
    <style>
        @page {
            margin: 20mm 20mm 25mm 20mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.7;
            color: #222;
        }

        /* ── Letterhead ─────────────────────────────────────── */
        .letterhead {
            text-align: center;
            margin-bottom: 8px;
        }
        .letterhead img {
            max-height: 80px;
        }
        .letterhead-line {
            border: none;
            border-top: 3px solid {{ setting('primary_color', '#1a1a1a') }};
            margin: 8px 0 20px;
        }

        /* ── Reference & Date Row ───────────────────────────── */
        .ref-row {
            width: 100%;
            margin-bottom: 20px;
        }
        .ref-row td {
            vertical-align: top;
            font-size: 12px;
        }
        .ref-label {
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .ref-value {
            font-weight: bold;
            font-size: 12px;
        }

        /* ── Addressee Blocks ───────────────────────────────── */
        .addressee-block {
            margin-bottom: 14px;
            line-height: 1.6;
            font-size: 12px;
        }
        .addressee-name {
            font-weight: bold;
        }
        .addressee-town {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        /* ── Subject Line ───────────────────────────────────── */
        .subject-line {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 20px 0 16px;
            font-size: 12px;
        }

        /* ── Body ───────────────────────────────────────────── */
        .notice-body {
            text-align: justify;
            font-size: 12px;
            line-height: 1.8;
            margin-bottom: 16px;
        }
        .notice-body strong, .notice-body b {
            font-weight: bold;
        }

        /* ── Agenda List ────────────────────────────────────── */
        .agenda-intro {
            margin-bottom: 8px;
            font-size: 12px;
        }
        .agenda-list {
            padding-left: 22px;
            margin-bottom: 18px;
        }
        .agenda-list li {
            padding: 2px 0;
            font-size: 12px;
        }

        /* ── Closing ────────────────────────────────────────── */
        .closing-text {
            margin: 18px 0 40px;
            font-size: 12px;
            line-height: 1.8;
        }

        /* ── Signature Block ────────────────────────────────── */
        .signature-block {
            margin-top: 40px;
        }
        .signature-img {
            max-height: 60px;
            max-width: 200px;
        }
        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 4px 0;
        }
        .signatory-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .signatory-title {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 12px;
        }

        /* ── CC Section ─────────────────────────────────────── */
        .cc-section {
            margin-top: 30px;
            font-size: 12px;
            line-height: 1.6;
        }
        .cc-label {
            font-weight: bold;
            vertical-align: top;
            padding-right: 10px;
            white-space: nowrap;
        }

        /* ── Footer ─────────────────────────────────────────── */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .footer img {
            max-height: 40px;
        }
        .footer p {
            color: #999;
            font-size: 9px;
            margin: 4px 0 0;
        }
    </style>
</head>
<body>

    {{-- ═══ LETTERHEAD ═══ --}}
    @include('workflow.components.letterhead')

    {{-- ═══ REFERENCE & DATE ═══ --}}
    @php
        $dateObj = \Carbon\Carbon::parse($notice->notice_date ?? $notice->reference_date ?? $notice->created_at);
        $day = $dateObj->day;
        $ordSuffix = match(true) {
            in_array($day, [11,12,13]) => 'th',
            $day % 10 === 1 => 'st',
            $day % 10 === 2 => 'nd',
            $day % 10 === 3 => 'rd',
            default => 'th',
        };
        $formattedDate = $day . $ordSuffix . ' ' . $dateObj->format('F Y');
    @endphp

    <table class="ref-row" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60%;">
                <span class="ref-label">Our Ref:</span><br>
                <span class="ref-value">{{ $notice->reference_number }}</span>
            </td>
            <td style="width: 40%; text-align: right;">
                <span class="ref-label">Date:</span><br>
                <span class="ref-value">{{ $formattedDate }}</span>
            </td>
        </tr>
    </table>

    {{-- ═══ ADDRESSEES ═══ --}}
    @php
        $addressees = is_array($notice->addressees) ? $notice->addressees : (json_decode($notice->addressees, true) ?? []);
        $resolvedAddressees = [];
        foreach ($addressees as $entry) {
            if (is_array($entry)) {
                $resolvedAddressees[] = $entry;
            } elseif (is_numeric($entry) && isset($boardMembers)) {
                $member = $boardMembers->firstWhere('id', $entry);
                if ($member) {
                    $resolvedAddressees[] = [
                        'name'              => $member->names ?? $member->name ?? '',
                        'designation'       => $member->designation ?? '',
                        'postal_address'    => $member->postal_address ?? '',
                        'place_of_residence'=> $member->place_of_residence ?? '',
                        'email'             => $member->email ?? '',
                    ];
                }
            }
        }
    @endphp

    @foreach($resolvedAddressees as $addr)
        <div class="addressee-block">
            <span class="addressee-name">{{ $addr['name'] ?? '' }}</span>
            @if(!empty($addr['designation']))
                <br>{{ $addr['designation'] }}
            @endif
            @if(!empty($addr['postal_address']))
                <br>{{ $addr['postal_address'] }}
            @endif
            @if(!empty($addr['place_of_residence']))
                <br><span class="addressee-town">{{ $addr['place_of_residence'] }}</span>
            @endif
            @if(!empty($addr['email']))
                <br>Email: {{ $addr['email'] }}
            @endif
        </div>
    @endforeach

    {{-- ═══ SUBJECT ═══ --}}
    <div class="subject-line">
        RE: {{ $notice->subject }}
    </div>

    {{-- ═══ BODY ═══ --}}
    <div class="notice-body">
        {!! $notice->body !!}
    </div>

    {{-- ═══ AGENDA ITEMS ═══ --}}
    @php
        $agendaItems = is_array($notice->agenda_items) ? $notice->agenda_items : (json_decode($notice->agenda_items, true) ?? []);
    @endphp

    @if(count($agendaItems))
        <p class="agenda-intro">The agenda of the meeting will be: -</p>
        <ol class="agenda-list">
            @foreach($agendaItems as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ol>
    @endif

    {{-- ═══ CLOSING ═══ --}}
    <div class="closing-text">
        {{ $notice->closing_text ?? 'Kindly take note and plan to attend.' }}
    </div>

    {{-- ═══ SIGNATURE BLOCK ═══ --}}
    <div class="signature-block">
        @if($notice->ceo_signature)
            <img src="{{ $notice->ceo_signature }}" alt="Signature" class="signature-img"><br>
        @else
            <div style="height: 50px;"></div>
        @endif
        <div class="signature-line"></div>
        @if($notice->signatory_name)
            <div class="signatory-name">{{ $notice->signatory_name }}</div>
        @endif
        @if($notice->signatory_title)
            <div class="signatory-title">{{ $notice->signatory_title }}</div>
        @endif
    </div>

    {{-- ═══ CC ═══ --}}
    @php
        $ccEntries = is_array($notice->cc) ? $notice->cc : (json_decode($notice->cc, true) ?? []);
    @endphp

    @if(count($ccEntries))
        <div class="cc-section">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td class="cc-label">CC:</td>
                    <td>
                        @foreach($ccEntries as $idx => $cc)
                            @if($idx > 0)<br><br>@endif
                            @if(is_array($cc))
                                {{ $cc['name'] ?? '' }}
                                @if(!empty($cc['address']))
                                    <br>{{ $cc['address'] }}
                                @endif
                            @else
                                {{ $cc }}
                            @endif
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- ═══ FOOTER ═══ --}}
    <div class="footer">
        @if(file_exists(public_path('backend/assets/images/kebs-badge.png')))
            <img src="{{ public_path('backend/assets/images/kebs-badge.png') }}" alt="KEBS">
        @endif
        <p>ISO 9001:2015 Certified</p>
    </div>

</body>
</html>
