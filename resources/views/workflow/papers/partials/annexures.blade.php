{{-- Shared Annexures display partial --}}
{{-- Usage: @include('workflow.papers.partials.annexures', ['paper' => $paper]) --}}
{{-- Optional: 'compact' => true for smaller display in modals --}}
@if($paper->annexures && $paper->annexures->count())
    <div class="twwda-annexures-section mt-4 pt-3 border-top">
        <h5 class="mb-3" style="font-size: 15px; font-weight: 700; color: #121212;">
            <i class="mdi mdi-attachment me-1" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i>
            Annexures
            <span class="badge bg-soft-primary text-primary ms-1" style="font-size: 11px; font-weight: 600;">{{ $paper->annexures->count() }}</span>
        </h5>
        <div class="row g-2">
            @foreach($paper->annexures as $annexure)
                @php
                    $ext = strtolower(pathinfo($annexure->file_path, PATHINFO_EXTENSION));
                    $iconMap = [
                        'pdf'  => ['icon' => 'mdi-file-pdf-box', 'color' => '#dc2626', 'bg' => '#fef2f2'],
                        'doc'  => ['icon' => 'mdi-file-word-box', 'color' => '#d41323', 'bg' => '#f7f7f7'],
                        'docx' => ['icon' => 'mdi-file-word-box', 'color' => '#d41323', 'bg' => '#f7f7f7'],
                        'xls'  => ['icon' => 'mdi-file-excel-box', 'color' => '#16a34a', 'bg' => '#f0fdf4'],
                        'xlsx' => ['icon' => 'mdi-file-excel-box', 'color' => '#16a34a', 'bg' => '#f0fdf4'],
                        'ppt'  => ['icon' => 'mdi-file-powerpoint-box', 'color' => '#d97706', 'bg' => '#fffbeb'],
                        'pptx' => ['icon' => 'mdi-file-powerpoint-box', 'color' => '#d97706', 'bg' => '#fffbeb'],
                        'jpg'  => ['icon' => 'mdi-file-image', 'color' => '#0baf44', 'bg' => '#ecfeff'],
                        'jpeg' => ['icon' => 'mdi-file-image', 'color' => '#0baf44', 'bg' => '#ecfeff'],
                        'png'  => ['icon' => 'mdi-file-image', 'color' => '#0baf44', 'bg' => '#ecfeff'],
                    ];
                    $meta = $iconMap[$ext] ?? ['icon' => 'mdi-file-outline', 'color' => '#64748b', 'bg' => '#fafafa'];
                    $sizeFormatted = $annexure->file_size > 0 ? $annexure->file_size_formatted : null;
                @endphp
                <div class="{{ isset($compact) && $compact ? 'col-12' : 'col-md-6' }}">
                    <a href="{{ asset('storage/' . $annexure->file_path) }}" target="_blank"
                       class="twwda-annexure-item d-flex align-items-center text-decoration-none"
                       style="padding: 10px 14px; border: 1px solid #e9e9e9; border-radius: 8px; transition: all 0.15s; background: #fff;">
                        <div class="twwda-annexure-icon flex-shrink-0 d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px; border-radius: 8px; background: {{ $meta['bg'] }};">
                            <i class="mdi {{ $meta['icon'] }}" style="font-size: 22px; color: {{ $meta['color'] }};"></i>
                        </div>
                        <div class="ms-3 flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-dark text-truncate" style="font-size: 13px;">{{ $annexure->title ?: $annexure->file_name }}</div>
                            <div class="d-flex align-items-center gap-2" style="font-size: 11.5px; color: #94a3b8;">
                                <span class="text-uppercase fw-semibold" style="color: {{ $meta['color'] }};">{{ $ext }}</span>
                                @if($sizeFormatted)
                                    <span>&bull;</span>
                                    <span>{{ $sizeFormatted }}</span>
                                @endif
                                <span>&bull;</span>
                                <span><i class="mdi mdi-download me-1"></i>Download</span>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <i class="mdi mdi-open-in-new" style="font-size: 16px; color: #94a3b8;"></i>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .twwda-annexure-item:hover {
            border-color: {{ setting('primary_color', '#1a1a1a') }} !important;
            box-shadow: 0 2px 8px rgba(200, 16, 46,0.1);
            transform: translateY(-1px);
        }
    </style>
@endif
