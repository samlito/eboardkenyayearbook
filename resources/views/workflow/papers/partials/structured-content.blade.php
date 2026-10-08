{{--
    Renders board paper content — handles both structured JSON and legacy HTML.
    Usage: @include('workflow.papers.partials.structured-content', ['paper' => $paper])
--}}

@if($paper->isStructuredContent())
    {{-- ═══ Official Template Header Block ═══ --}}
    <div class="text-center mb-4 pb-3" style="border-bottom: 2px solid {{ setting('primary_color', '#1a1a1a') }};">
        <p class="text-uppercase fw-bold mb-1" style="font-size: 13px; color: {{ setting('primary_color', '#1a1a1a') }}; letter-spacing: 1px;">
            {{ setting('institution_name', 'eBoard System') }} ({{ setting('institution_acronym', 'ORG') }})
        </p>

        @if($paper->committee_name)
            <p class="fw-bold mb-1" style="font-size: 14px; color: #111111;">
                MEETING OF THE {{ strtoupper($paper->committee_name) }}
                @if($paper->meeting && $paper->meeting->meeting_date)
                    DATED {{ \Carbon\Carbon::parse($paper->meeting->meeting_date)->format('jS F Y') }}
                @endif
            </p>
        @endif

        @if($paper->agenda_item_number)
            <p class="mb-1" style="font-size: 13px; color: #374151;">
                Agenda Item No. <strong>{{ $paper->agenda_item_number }}</strong>
            </p>
        @endif

        <p class="mb-1" style="font-size: 13px; color: #374151;">
            Agenda Item: <strong>{{ $paper->title }}</strong>
            @if($paper->reporting_period)
                for {{ $paper->reporting_period }}
            @endif
        </p>

        @if($paper->paper_type)
            <p class="mb-0 fst-italic" style="font-size: 12.5px; color: #6b7280;">
                Paper Type: {{ $paper->paper_type }}
            </p>
        @endif
    </div>

    {{-- Author / Department --}}
    @if($paper->author_department || $paper->author)
        <div class="mb-3 pb-2 border-bottom">
            <small class="text-muted">
                <strong>Prepared by:</strong>
                {{ $paper->author->name ?? '' }}
                @if($paper->author_department)
                    &mdash; {{ $paper->author_department }}
                @endif
            </small>
        </div>
    @endif

    {{-- ═══ Structured Sections ═══ --}}
    @php
        $sections = $paper->structured_content;
        $labels   = \App\Models\Twwda\TwwdaBoardPaper::sectionLabels();
    @endphp

    @foreach($labels as $key => $label)
        @php $sectionHtml = $sections[$key] ?? ''; @endphp
        @if(!empty($sectionHtml) && $sectionHtml !== '<p><br></p>' && trim(strip_tags($sectionHtml)) !== '')
            <div class="mb-4">
                <h5 style="color: {{ setting('primary_color', '#1a1a1a') }}; font-weight: 700; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb;">
                    {{ $label }}
                </h5>
                <div class="paper-section-content" style="font-size: 14px; line-height: 1.8; text-align: justify;">
                    {!! $sectionHtml !!}
                </div>
            </div>
        @endif
    @endforeach

@else
    {{-- Legacy: raw HTML content (old papers) --}}
    <div class="paper-content" style="font-size: 14px; line-height: 1.8; text-align: justify;">
        {!! $paper->content !!}
    </div>
@endif
