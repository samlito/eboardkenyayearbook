@extends('admin_dashboard')

@section('admin')

@php
    use Carbon\Carbon;

    $fileTypeConfig = function($fileType) {
        $type = strtolower($fileType ?? '');
        switch ($type) {
            case 'pdf':
                return ['icon' => 'mdi-file-pdf-box', 'color' => '#dc2626', 'bg' => '#fef2f2', 'label' => 'PDF'];
            case 'doc':
            case 'docx':
                return ['icon' => 'mdi-file-word-box', 'color' => '#d41323', 'bg' => '#f7f7f7', 'label' => 'Word'];
            case 'xls':
            case 'xlsx':
            case 'csv':
                return ['icon' => 'mdi-file-excel-box', 'color' => '#16a34a', 'bg' => '#f0fdf4', 'label' => 'Excel'];
            case 'ppt':
            case 'pptx':
                return ['icon' => 'mdi-file-powerpoint-box', 'color' => '#ea580c', 'bg' => '#fff7ed', 'label' => 'PowerPoint'];
            case 'jpg':
            case 'jpeg':
            case 'png':
                return ['icon' => 'mdi-file-image-box', 'color' => '#109d42', 'bg' => '#f6fef9', 'label' => 'Image'];
            case 'txt':
                return ['icon' => 'mdi-file-document-outline', 'color' => '#6b7280', 'bg' => '#f5f6f8', 'label' => 'Text'];
            default:
                return ['icon' => 'mdi-file-outline', 'color' => '#6b7280', 'bg' => '#f5f6f8', 'label' => strtoupper($type ?: 'File')];
        }
    };
@endphp

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════════════
   BOARD PORTAL — CATEGORY DOCUMENTS
   Premium government portal design with depth & polish
   ══════════════════════════════════════════════════════════════════════ */
:root {
    --brand: {{ setting('primary_color', '#1a1a1a') }};
}

.bp-category-docs {
    padding: 0 4px;
    font-size: 15px;
    -webkit-font-smoothing: antialiased;
}

/* ── Hero ────────────────────────────────────────────────────────────── */
.bp-hero {
    background: linear-gradient(135deg, var(--brand) 0%, #2b2b2b 50%, #3a3a3a 100%);
    border-radius: 18px;
    padding: 32px 36px 28px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(200, 16, 46, 0.3);
}
.bp-hero-pattern {
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.bp-hero-content {
    position: relative;
    z-index: 1;
}
.bp-back-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 14px;
    transition: color 0.2s;
}
.bp-back-link:hover {
    color: #fff;
}
.bp-hero-title {
    font-size: 28px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
}
.bp-hero-title i {
    margin-right: 8px;
    font-size: 30px;
    vertical-align: middle;
}
.bp-hero-subtitle {
    color: rgba(255,255,255,0.75);
    font-size: 16px;
    margin: 0;
}
.bp-hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    white-space: nowrap;
}

/* ── Document Cards ──────────────────────────────────────────────────── */
.bp-documents-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.bp-document-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    overflow: hidden;
    position: relative;
    transition: all 0.25s ease;
}
.bp-document-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 4px;
    background: #e9ecef;
    transition: background 0.25s;
}
.bp-document-card:hover {
    box-shadow: 0 8px 36px rgba(0,0,0,0.1);
    border-color: #d0d5dd;
}
.bp-document-card:hover::before {
    background: var(--brand);
}
.bp-document-inner {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 22px 24px 22px 28px;
}
.bp-doc-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}
.bp-doc-info {
    flex: 1;
    min-width: 0;
}
.bp-doc-title {
    font-size: 17px;
    font-weight: 700;
    color: #0d0d0d;
    margin-bottom: 4px;
    line-height: 1.35;
    word-break: break-word;
}
.bp-doc-description {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 10px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.bp-doc-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    font-size: 13px;
    color: #9ca3af;
}
.bp-doc-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.bp-doc-meta-item i {
    font-size: 16px;
}
.bp-doc-type-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.bp-doc-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.bp-download-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--brand);
    color: #fff;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s;
    white-space: nowrap;
    min-height: 44px;
    border: none;
}
.bp-download-btn:hover {
    background: #2b2b2b;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(200, 16, 46, 0.3);
}
.bp-download-btn i {
    font-size: 18px;
}
.bp-download-arrow {
    font-size: 18px;
    transition: transform 0.25s;
}
.bp-document-card:hover .bp-download-arrow {
    transform: translateY(2px);
}

/* ── Empty State ────────────────────────────────────────────────────── */
.bp-empty-state {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    text-align: center;
    padding: 64px 32px;
}
.bp-empty-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f0f2f5, #e5e7eb);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}
.bp-empty-icon i {
    font-size: 48px;
    color: #9ca3af;
}
.bp-empty-state h4 {
    font-size: 22px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
}
.bp-empty-state p {
    font-size: 15px;
    color: #6b7280;
    max-width: 420px;
    margin: 0 auto 24px;
}
.bp-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--brand);
    color: #fff;
    border: none;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    transition: background 0.2s;
}
.bp-btn-primary:hover {
    background: #2b2b2b;
    color: #fff;
}

/* ── Responsive ──────────────────────────────────────────────────────── */
@media (max-width: 768px) {
    .bp-hero {
        padding: 24px 20px 20px;
        border-radius: 14px;
    }
    .bp-hero-title {
        font-size: 22px;
    }
    .bp-document-inner {
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
        padding: 20px 20px 20px 24px;
    }
    .bp-doc-icon-wrap {
        width: 44px;
        height: 44px;
        font-size: 24px;
        border-radius: 12px;
    }
    .bp-doc-actions {
        width: 100%;
    }
    .bp-download-btn {
        flex: 1;
        justify-content: center;
    }
    .bp-doc-meta {
        gap: 10px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .bp-document-card {
        -webkit-tap-highlight-color: transparent;
    }
}
</style>
@endpush

<div class="bp-category-docs">

    {{-- ═══════════════════════════════════════════════════════════════
         HERO HEADER
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="bp-hero">
        <div class="bp-hero-pattern"></div>
        <div class="bp-hero-content">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <a href="{{ route('board.portal.repository') }}" class="bp-back-link">
                        <i class="mdi mdi-arrow-left"></i> Document Repository
                    </a>
                    <h2 class="bp-hero-title">
                        <i class="mdi {{ $category->icon ?? 'mdi-folder-outline' }}"></i> {{ $category->name }}
                    </h2>
                    @if($category->description)
                        <p class="bp-hero-subtitle">{{ $category->description }}</p>
                    @endif
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="bp-hero-chip">
                        <i class="mdi mdi-file-document-multiple-outline"></i>
                        {{ $documents->count() }} {{ Str::plural('Document', $documents->count()) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         DOCUMENTS LIST
         ═══════════════════════════════════════════════════════════════ --}}
    @if($documents->count())
        <div class="bp-documents-list">
            @foreach($documents as $document)
                @php
                    $ft = $fileTypeConfig($document->file_type);
                @endphp

                <div class="bp-document-card">
                    <div class="bp-document-inner">
                        {{-- File Type Icon --}}
                        <div class="bp-doc-icon-wrap" style="background: {{ $ft['bg'] }}; color: {{ $ft['color'] }};">
                            <i class="mdi {{ $ft['icon'] }}"></i>
                        </div>

                        {{-- Document Info --}}
                        <div class="bp-doc-info">
                            <h5 class="bp-doc-title">{{ $document->title }}</h5>

                            @if($document->description)
                                <p class="bp-doc-description">{{ $document->description }}</p>
                            @endif

                            <div class="bp-doc-meta">
                                {{-- File Type Chip --}}
                                <span class="bp-doc-type-chip" style="background: {{ $ft['bg'] }}; color: {{ $ft['color'] }};">
                                    <i class="mdi {{ $ft['icon'] }}"></i>
                                    {{ $ft['label'] }}
                                </span>

                                {{-- File Size --}}
                                <span class="bp-doc-meta-item">
                                    <i class="mdi mdi-harddisk"></i>
                                    {{ $document->formattedFileSize() }}
                                </span>

                                {{-- Upload Date --}}
                                <span class="bp-doc-meta-item">
                                    <i class="mdi mdi-calendar-outline"></i>
                                    {{ $document->created_at->format('d M Y') }}
                                </span>

                                {{-- Uploaded By --}}
                                @if($document->uploader)
                                    <span class="bp-doc-meta-item">
                                        <i class="mdi mdi-account-outline"></i>
                                        {{ $document->uploader->name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Download Action --}}
                        <div class="bp-doc-actions">
                            <a href="{{ route('board.portal.repository.download', $document) }}" class="bp-download-btn">
                                <i class="mdi mdi-download"></i>
                                Download
                                <i class="mdi mdi-arrow-down bp-download-arrow"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="bp-empty-state">
            <div class="bp-empty-icon">
                <i class="mdi mdi-file-document-outline"></i>
            </div>
            <h4>No Documents Available</h4>
            <p>There are no documents available in the "{{ $category->name }}" category at this time. Please check back later.</p>
            <a href="{{ route('board.portal.repository') }}" class="bp-btn-primary">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Repository
            </a>
        </div>
    @endif
</div>
@endsection
