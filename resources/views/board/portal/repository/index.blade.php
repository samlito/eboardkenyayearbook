@extends('admin_dashboard')

@section('admin')

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════════════
   BOARD PORTAL — DOCUMENT REPOSITORY
   Premium government portal design with depth & polish
   ══════════════════════════════════════════════════════════════════════ */
:root {
    --brand: {{ setting('primary_color', '#1a1a1a') }};
}

.bp-repository {
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

/* ── Category Cards Grid ─────────────────────────────────────────────── */
.bp-categories-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.bp-category-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    overflow: hidden;
    position: relative;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
}
.bp-category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--brand), #3a3a3a);
    opacity: 0;
    transition: opacity 0.25s;
}
.bp-category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(200, 16, 46, 0.14);
    border-color: #dd3c49;
    color: inherit;
    text-decoration: none;
}
.bp-category-card:hover::before {
    opacity: 1;
}
.bp-category-card-body {
    padding: 28px 24px 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.bp-category-icon-wrap {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 18px;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(0,0,0,0.1);
}
.bp-category-name {
    font-size: 19px;
    font-weight: 700;
    color: #0d0d0d;
    margin-bottom: 8px;
    line-height: 1.3;
}
.bp-category-desc {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 16px;
    line-height: 1.55;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.bp-category-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid #f0f2f5;
    background: #fbfbfb;
}
.bp-doc-count-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}
.bp-category-arrow {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #f2f2f2;
    color: var(--brand);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.25s;
    flex-shrink: 0;
}
.bp-category-card:hover .bp-category-arrow {
    background: var(--brand);
    color: #fff;
    transform: translateX(3px);
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
@media (max-width: 1024px) {
    .bp-categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .bp-hero {
        padding: 24px 20px 20px;
        border-radius: 14px;
    }
    .bp-hero-title {
        font-size: 22px;
    }
    .bp-categories-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .bp-category-card-body {
        padding: 22px 20px 20px;
    }
    .bp-category-footer {
        padding: 14px 20px;
    }
    .bp-category-icon-wrap {
        width: 48px;
        height: 48px;
        font-size: 24px;
        border-radius: 14px;
    }
    .bp-category-name {
        font-size: 17px;
    }
}
</style>
@endpush

<div class="bp-repository">

    {{-- ═══════════════════════════════════════════════════════════════
         HERO HEADER
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="bp-hero">
        <div class="bp-hero-pattern"></div>
        <div class="bp-hero-content">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <a href="{{ route('board.portal.dashboard') }}" class="bp-back-link">
                        <i class="mdi mdi-arrow-left"></i> Dashboard
                    </a>
                    <h2 class="bp-hero-title">
                        <i class="mdi mdi-folder-multiple-outline"></i> Document Repository
                    </h2>
                    <p class="bp-hero-subtitle">Browse and download board documents by category</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="bp-hero-chip">
                        <i class="mdi mdi-folder-outline"></i>
                        {{ $categories->count() }} {{ Str::plural('Category', $categories->count()) }}
                    </div>
                    <div class="bp-hero-chip">
                        <i class="mdi mdi-file-document-multiple-outline"></i>
                        {{ $categories->sum('total_documents') }} {{ Str::plural('Document', $categories->sum('total_documents')) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         CATEGORY CARDS
         ═══════════════════════════════════════════════════════════════ --}}
    @if($categories->count())
        <div class="bp-categories-grid">
            @foreach($categories as $category)
                @php
                    $gradients = [
                        [setting('primary_color', '#1a1a1a'), '#e50013'],
                        ['#099139', '#27ae60'],
                        ['#e13240', '#fd1c2f'],
                        ['#d63384', '#e84393'],
                        ['#0f766e', '#14b8a6'],
                        ['#b45309', '#f59e0b'],
                    ];
                    $grad = $gradients[$loop->index % count($gradients)];
                    $docCount = $category->total_documents ?? 0;
                @endphp

                <a href="{{ route('board.portal.repository.category', $category) }}" class="bp-category-card">
                    <div class="bp-category-card-body">
                        <div class="bp-category-icon-wrap" style="background: linear-gradient(135deg, {{ $grad[0] }}, {{ $grad[1] }}); color: #fff;">
                            <i class="mdi {{ $category->icon ?? 'mdi-folder-outline' }}"></i>
                        </div>
                        <h4 class="bp-category-name">{{ $category->name }}</h4>
                        @if($category->description)
                            <p class="bp-category-desc">{{ $category->description }}</p>
                        @else
                            <p class="bp-category-desc" style="color: #9ca3af; font-style: italic;">No description available</p>
                        @endif
                    </div>
                    <div class="bp-category-footer">
                        <span class="bp-doc-count-badge" style="background: {{ $grad[0] }}10; color: {{ $grad[0] }};">
                            <i class="mdi mdi-file-document-outline"></i>
                            {{ $docCount }} {{ Str::plural('Document', $docCount) }}
                        </span>
                        <span class="bp-category-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="bp-empty-state">
            <div class="bp-empty-icon">
                <i class="mdi mdi-folder-open-outline"></i>
            </div>
            <h4>No Document Categories</h4>
            <p>There are no document categories available in the repository at this time. Please check back later.</p>
            <a href="{{ route('board.portal.dashboard') }}" class="bp-btn-primary">
                <i class="mdi mdi-arrow-left me-1"></i> Return to Dashboard
            </a>
        </div>
    @endif
</div>
@endsection
