{{-- Official Letterhead Component --}}
{{-- Usage: @include('workflow.components.letterhead') --}}
{{-- Uses public_path() for DomPDF compatibility (asset() URLs don't resolve in PDF rendering) --}}

@php
    $letterheadLogo = setting('logo_letterhead') ?: setting('logo_main');
    $letterheadLogoPath = $letterheadLogo ? public_path($letterheadLogo) : '';
    $letterheadLogoExists = $letterheadLogoPath && file_exists($letterheadLogoPath);
@endphp

<div class="institution-letterhead mb-4">
    <div class="text-center">
        @if($letterheadLogoExists)
            <img src="{{ $letterheadLogoPath }}" alt="{{ setting('institution_acronym', 'ORG') }} Letterhead"
                 style="max-width: 100%; height: auto; max-height: 100px;" class="mb-2">
        @elseif(file_exists(public_path('images/banner.png')))
            <img src="{{ public_path('images/banner.png') }}" alt="{{ setting('institution_acronym', 'ORG') }} Letterhead"
                 style="max-width: 100%; height: auto; max-height: 100px;" class="mb-2">
        @else
            <h3 style="margin: 5px 0; color: {{ setting('primary_color', '#1a1a1a') }};">{{ setting('institution_name', 'Organization') }}</h3>
        @endif
    </div>
    <hr style="border-top: 2px solid {{ setting('primary_color', '#1a1a1a') }}; margin: 10px 0;">
</div>
