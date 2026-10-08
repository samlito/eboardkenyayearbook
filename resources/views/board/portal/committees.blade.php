@extends('admin_dashboard')

@section('admin')

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════════════
   BOARD PORTAL — COMMITTEES PAGE
   Premium government portal design with depth & polish
   ══════════════════════════════════════════════════════════════════════ */
:root {
    --brand: {{ setting('primary_color', '#1a1a1a') }};
}

.bp-committees {
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
.bp-hero-stat {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 14px;
    padding: 14px 22px;
    text-align: center;
    min-width: 100px;
}
.bp-hero-stat-number {
    display: block;
    font-size: 28px;
    font-weight: 800;
    color: #fff;
    line-height: 1;
    margin-bottom: 4px;
}
.bp-hero-stat-label {
    display: block;
    font-size: 13px;
    color: rgba(255,255,255,0.8);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ── Committee Cards ─────────────────────────────────────────────────── */
.bp-committees-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.bp-committee-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    overflow: hidden;
    border: 1px solid #e9ecef;
    position: relative;
    transition: box-shadow 0.3s;
}
.bp-committee-card:hover {
    box-shadow: 0 8px 36px rgba(0,0,0,0.1);
}
.bp-committee-accent {
    height: 5px;
    width: 100%;
}
.bp-committee-header {
    padding: 24px 28px 20px;
    border-bottom: 1px solid #f0f2f5;
}
.bp-committee-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 26px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.bp-committee-name {
    font-size: 22px;
    font-weight: 700;
    color: #0d0d0d;
    margin-bottom: 2px;
    line-height: 1.3;
}
.bp-committee-desc {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}
.bp-member-count {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
}
.bp-committee-body {
    padding: 24px 28px 28px;
}

/* ── Members Grid ────────────────────────────────────────────────────── */
.bp-members-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
}
.bp-member-card {
    position: relative;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    padding: 24px 20px 20px;
    text-align: center;
    transition: all 0.25s ease;
    overflow: hidden;
}
.bp-member-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #e9ecef;
    transition: background 0.25s;
}
.bp-member-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.08);
    border-color: #d0d5dd;
}
.bp-member-card:hover::before {
    background: var(--brand);
}

/* You highlight */
.bp-member-you {
    border: 2px solid var(--brand);
    box-shadow: 0 0 0 4px rgba(200, 16, 46, 0.08);
}
.bp-member-you::before {
    background: var(--brand);
    height: 5px;
}
.bp-you-ribbon {
    position: absolute;
    top: 12px;
    right: -28px;
    background: var(--brand);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1px;
    padding: 3px 32px;
    transform: rotate(45deg);
    box-shadow: 0 2px 6px rgba(200, 16, 46, 0.3);
}

/* Chair highlight */
.bp-member-chair::before {
    background: linear-gradient(90deg, #f9a825, #ff8f00);
    height: 5px;
}

/* Chair crown */
.bp-chair-crown {
    position: absolute;
    top: 10px;
    left: 14px;
    color: #f59e0b;
    font-size: 22px;
    filter: drop-shadow(0 2px 4px rgba(245, 158, 11, 0.4));
    animation: bp-crown-float 3s ease-in-out infinite;
}
@keyframes bp-crown-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}

/* ── Member Avatar ───────────────────────────────────────────────────── */
.bp-member-avatar-wrap {
    position: relative;
    display: inline-block;
    margin-bottom: 14px;
}
.bp-member-avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 1px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    border: 4px solid #fff;
}
.bp-member-avatar-img {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    border: 4px solid #fff;
}
.bp-chair-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* ── Member Info ─────────────────────────────────────────────────────── */
.bp-member-name {
    font-size: 16px;
    font-weight: 700;
    color: #0d0d0d;
    margin-bottom: 4px;
    line-height: 1.3;
}
.bp-member-designation {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 10px;
    line-height: 1.4;
}
.bp-role-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 10px;
}
.bp-role-chair {
    background: linear-gradient(135deg, #f9a825, #ff8f00) !important;
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(249, 168, 37, 0.35);
}
.bp-member-email {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #9ca3af;
    text-decoration: none;
    transition: color 0.2s;
    word-break: break-all;
}
.bp-member-email:hover {
    color: var(--brand);
}

/* ── Empty States ────────────────────────────────────────────────────── */
.bp-empty-mini {
    text-align: center;
    padding: 32px 16px;
    color: #9ca3af;
}
.bp-empty-mini i {
    font-size: 40px;
    display: block;
    margin-bottom: 8px;
}
.bp-empty-mini p {
    font-size: 14px;
    margin: 0;
}
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
    .bp-hero-stat {
        padding: 10px 16px;
        min-width: 80px;
    }
    .bp-hero-stat-number {
        font-size: 22px;
    }
    .bp-committee-header {
        padding: 18px 20px 14px;
    }
    .bp-committee-body {
        padding: 18px 20px 20px;
    }
    .bp-members-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
    }
    .bp-member-card {
        padding: 20px 16px 16px;
    }
    .bp-member-avatar,
    .bp-member-avatar-img {
        width: 60px;
        height: 60px;
        font-size: 18px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .bp-members-grid {
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    }
    .bp-member-card {
        -webkit-tap-highlight-color: transparent;
    }
}
</style>
@endpush

<div class="bp-committees">

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
                        <i class="mdi mdi-account-group"></i> My Committees
                    </h2>
                    <p class="bp-hero-subtitle">
                        {{ $member->names }} &mdash; {{ $member->designation ?? 'Board Member' }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <div class="bp-hero-stat">
                        <span class="bp-hero-stat-number">{{ $committees->count() }}</span>
                        <span class="bp-hero-stat-label">{{ Str::plural('Committee', $committees->count()) }}</span>
                    </div>
                    <div class="bp-hero-stat">
                        <span class="bp-hero-stat-number">{{ $committees->sum(fn($c) => $c->members ? $c->members->count() : 0) }}</span>
                        <span class="bp-hero-stat-label">Total Members</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         COMMITTEES
         ═══════════════════════════════════════════════════════════════ --}}
    @if($committees->count())
        <div class="bp-committees-list">
            @foreach($committees as $committee)
                @php
                    $memberCount = $committee->members ? $committee->members->count() : 0;
                    // Gradient palette per committee index
                    $gradients = [
                        [setting('primary_color', '#1a1a1a'), '#e50013'],
                        ['#099139', '#27ae60'],
                        ['#e13240', '#fd1c2f'],
                        ['#d63384', '#e84393'],
                        ['#0f766e', '#14b8a6'],
                        ['#b45309', '#f59e0b'],
                    ];
                    $grad = $gradients[$loop->index % count($gradients)];
                @endphp

                <div class="bp-committee-card">
                    {{-- Accent bar --}}
                    <div class="bp-committee-accent" style="background: linear-gradient(135deg, {{ $grad[0] }}, {{ $grad[1] }});"></div>

                    {{-- Header --}}
                    <div class="bp-committee-header">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bp-committee-icon" style="background: linear-gradient(135deg, {{ $grad[0] }}, {{ $grad[1] }});">
                                    <i class="mdi mdi-shield-account"></i>
                                </div>
                                <div>
                                    <h4 class="bp-committee-name">{{ $committee->name }}</h4>
                                    @if($committee->description)
                                        <p class="bp-committee-desc">{{ $committee->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="bp-member-count" style="background: {{ $grad[0] }}10; color: {{ $grad[0] }};">
                                <i class="mdi mdi-account-multiple"></i>
                                {{ $memberCount }} {{ Str::plural('Member', $memberCount) }}
                            </span>
                        </div>
                    </div>

                    {{-- Members Grid --}}
                    <div class="bp-committee-body">
                        @if($memberCount > 0)
                            <div class="bp-members-grid">
                                @foreach($committee->members->sortByDesc(fn($m) => Str::contains(Str::lower($m->director_type ?? ''), 'chairperson') ? 1 : 0) as $committeeMember)
                                    @php
                                        $isCurrentUser = ($committeeMember->id === $member->id);
                                        $salutation = optional($committeeMember->assignsalutation)->name;
                                        $fullName = ($salutation ? $salutation . ' ' : '') . $committeeMember->names;
                                        $directorType = $committeeMember->director_type;
                                        $isChair = $directorType && Str::contains(Str::lower($directorType), 'chairperson');
                                        $photo = $committeeMember->photo;

                                        $nameParts = explode(' ', trim($committeeMember->names));
                                        $initials = count($nameParts) >= 2
                                            ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
                                            : strtoupper(substr($nameParts[0] ?? 'N', 0, 2));
                                    @endphp

                                    <div class="bp-member-card {{ $isCurrentUser ? 'bp-member-you' : '' }} {{ $isChair ? 'bp-member-chair' : '' }}">
                                        {{-- You badge --}}
                                        @if($isCurrentUser)
                                            <div class="bp-you-ribbon">YOU</div>
                                        @endif

                                        {{-- Chair crown --}}
                                        @if($isChair)
                                            <div class="bp-chair-crown">
                                                <i class="mdi mdi-crown"></i>
                                            </div>
                                        @endif

                                        {{-- Avatar --}}
                                        <div class="bp-member-avatar-wrap">
                                            @if($photo)
                                                <img src="{{ asset('storage/' . $photo) }}" alt="{{ $committeeMember->names }}" class="bp-member-avatar-img">
                                            @else
                                                <div class="bp-member-avatar" style="background: linear-gradient(135deg, {{ $grad[0] }}, {{ $grad[1] }});">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            @if($isChair)
                                                <span class="bp-chair-dot" style="background: {{ $grad[0] }};"></span>
                                            @endif
                                        </div>

                                        {{-- Info --}}
                                        <h6 class="bp-member-name">{{ $fullName }}</h6>
                                        @if($committeeMember->designation)
                                            <p class="bp-member-designation">{{ $committeeMember->designation }}</p>
                                        @endif

                                        {{-- Role badge --}}
                                        @if($directorType)
                                            <span class="bp-role-badge {{ $isChair ? 'bp-role-chair' : '' }}" style="{{ !$isChair ? 'background: ' . $grad[0] . '12; color: ' . $grad[0] . ';' : '' }}">
                                                @if($isChair)
                                                    <i class="mdi mdi-crown"></i>
                                                @else
                                                    <i class="mdi mdi-account-tie"></i>
                                                @endif
                                                {{ $directorType }}
                                            </span>
                                        @endif

                                        {{-- Email --}}
                                        @if($committeeMember->email)
                                            <a href="mailto:{{ $committeeMember->email }}" class="bp-member-email">
                                                <i class="mdi mdi-email-outline"></i> {{ $committeeMember->email }}
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bp-empty-mini">
                                <i class="mdi mdi-account-off-outline"></i>
                                <p>No members assigned to this committee yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="bp-empty-state">
            <div class="bp-empty-icon">
                <i class="mdi mdi-account-group-outline"></i>
            </div>
            <h4>No Committee Assignments</h4>
            <p>You are not currently assigned to any board committee. Contact the Board Secretary if you believe this is an error.</p>
            <a href="{{ route('board.portal.dashboard') }}" class="bp-btn-primary">
                <i class="mdi mdi-arrow-left me-1"></i> Return to Dashboard
            </a>
        </div>
    @endif
</div>
@endsection
