@extends('admin_dashboard')
@section('admin')

@php
    $roleMap = [
        'superadmin' => ['Superadmin', 'System Administrator'],
        'cs'         => ['Corporation Secretary', 'Corporation Secretary'],
        'ceo'        => ['CEO', 'CEO'],
        'director'   => ['Director', 'Director'],
        'divhead'    => ['Division Head', 'Division Head / Officer'],
        'ddia'       => ['DD Internal Audit', 'DD Internal Audit'],
        'board'      => ['Board Member', 'Board Member'],
    ];
    $myRoles = collect($roleMap)
        ->filter(fn ($r) => auth()->user()->hasRole($r[0]))
        ->keys()
        ->values();
    $acronym = setting('institution_acronym', 'ORG');
@endphp

@push('styles')
<style>
    .ug { --ug-accent: #f00014; --ug-ink: #0d0d0d; --ug-muted: #4b5563; --ug-line: #e5e7eb; --ug-soft: #f7f7f7; }
    .ug-hero {
        background: linear-gradient(135deg, #1a1a1a 0%, #191919 100%);
        color: #fff; border-radius: 14px; padding: 28px 30px; margin-bottom: 22px;
    }
    .ug-hero h2 { color: #fff !important; font-size: 1.6rem !important; margin: 0 0 6px; }
    .ug-hero p { color: rgba(255,255,255,.85); margin: 0; max-width: 760px; }

    .ug-filter { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-top: 18px; }
    .ug-filter span.lbl { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.7); margin-right: 4px; }
    .ug-chip {
        border: 1px solid rgba(255,255,255,.35); background: transparent; color: #fff;
        border-radius: 999px; padding: 5px 13px; font-size: 13px; font-weight: 500; cursor: pointer;
    }
    .ug-chip:hover { background: rgba(255,255,255,.12); }
    .ug-chip.active { background: #fff; color: #191919; border-color: #fff; font-weight: 600; }
    .ug-chip .me { font-size: 10px; margin-left: 5px; padding: 1px 6px; border-radius: 999px; background: #7AC143; color: #fff; }

    .ug-totop {
        position: fixed; right: 24px; bottom: 24px; z-index: 1000; width: 42px; height: 42px; border-radius: 50%;
        background: #1a1a1a; color: #fff; border: 0; box-shadow: 0 4px 14px rgba(0,0,0,.18);
        display: none; align-items: center; justify-content: center; font-size: 22px;
    }
    .ug-totop.show { display: inline-flex; }
    .ug-totop:hover { background: #191919; }
    .ug-toc .card-body { padding: 16px 10px; }
    .ug-toc h6 { font-size: 11px !important; text-transform: uppercase; letter-spacing: .09em; color: var(--ug-muted) !important; margin: 0 10px 8px; }
    .ug-toc a {
        display: block; padding: 6px 10px; border-radius: 6px; font-size: 13.5px;
        color: var(--ug-muted); text-decoration: none; line-height: 1.35;
    }
    .ug-toc a:hover { background: var(--ug-soft); color: var(--ug-ink); }
    .ug-toc a.hidden-by-filter { display: none; }
    .ug-toc .grp { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin: 14px 10px 4px; }

    .ug-sec { scroll-margin-top: 84px; }
    .ug-sec.hidden-by-filter { display: none; }
    .ug-sec .card-body { padding: 26px 28px; }
    .ug-sec h3 { font-size: 1.3rem !important; display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
    .ug-sec h3 .num {
        flex: 0 0 auto; width: 30px; height: 30px; border-radius: 8px; background: #f3f3f3; color: #222222;
        font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;
    }
    .ug-sec h3 i { color: #222222; }
    .ug-sec h5 { font-size: 1.02rem !important; margin: 20px 0 8px; }
    .ug-sec p, .ug-sec li { color: var(--ug-ink); font-size: 14.5px; line-height: 1.65; }
    .ug-sec ol, .ug-sec ul { padding-left: 1.3rem; margin-bottom: 10px; }
    .ug-sec li { margin-bottom: 4px; }
    .ug-who { display: flex; flex-wrap: wrap; gap: 6px; margin: 6px 0 14px; }
    .ug-role {
        font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 999px;
        background: var(--ug-soft); color: #374151; border: 1px solid var(--ug-line);
    }
    .ug-role.mine { background: #e6f6ec; color: #077a2e; border-color: #b7e4c7; }
    .ug-path {
        display: inline-flex; align-items: center; gap: 6px; font-size: 13px; background: var(--ug-soft);
        border: 1px solid var(--ug-line); border-radius: 6px; padding: 3px 9px; color: #374151;
    }
    .ug-ui { font-weight: 600; color: #191919; white-space: nowrap; }
    .ug-tip, .ug-warn {
        border-radius: 10px; padding: 12px 14px; margin: 14px 0; font-size: 14px; display: flex; gap: 10px;
    }
    .ug-tip { background: #f6f6f6; border: 1px solid #e7e7e7; }
    .ug-warn { background: #fff8e6; border: 1px solid #fde3a7; }
    .ug-tip i, .ug-warn i { font-size: 18px; line-height: 1.3; }
    .ug-tip i { color: #f00014; } .ug-warn i { color: #d97706; }
    .ug-tip p, .ug-warn p { margin: 0; font-size: 14px; }

    .ug-flow { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 10px; margin-top: 12px; }
    .ug-flow a {
        display: block; border: 1px solid var(--ug-line); border-radius: 10px; padding: 12px 14px;
        text-decoration: none; background: #fff; transition: border-color .15s, box-shadow .15s;
    }
    .ug-flow a:hover { border-color: #f00014; box-shadow: 0 2px 10px rgba(240,0,20,.12); }
    .ug-flow .s { font-size: 11px; font-weight: 700; color: #f00014; text-transform: uppercase; letter-spacing: .06em; }
    .ug-flow .t { display: block; font-weight: 600; color: var(--ug-ink); font-size: 14px; margin: 2px 0; }
    .ug-flow .r { font-size: 12.5px; color: var(--ug-muted); }

    .ug-table { width: 100%; font-size: 14px; margin: 8px 0 12px; }
    .ug-table th { background: var(--ug-soft); font-weight: 600; color: #374151; padding: 8px 10px; border-bottom: 1px solid var(--ug-line); }
    .ug-table td { padding: 8px 10px; border-bottom: 1px solid var(--ug-line); vertical-align: top; }

    .ug-status { display: inline-block; font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 6px; background: #f5f5f5; color: #334155; }

    @media print {
        .left-side-menu, .navbar-custom, .footer, .ug-toc-col, .ug-filter, .ug-totop { display: none !important; }
        .content-page { margin: 0 !important; padding: 0 !important; }
        .ug-sec.hidden-by-filter { display: block; }
    }
</style>
@endpush

<div class="content ug">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="page-title">User Guide</h4>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('user.guide.pdf') }}" class="btn btn-sm btn-primary"><i class="mdi mdi-file-pdf-box me-1"></i>Download PDF</a>
                        <button type="button" class="btn btn-sm btn-light" onclick="window.print()"><i class="mdi mdi-printer me-1"></i>Print</button>
                        <ol class="breadcrumb m-0 ms-2">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">User Guide</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="ug-hero">
            <h2>{{ $acronym }} eBoard User Guide</h2>
            <p>How to use the system from start to finish: setting it up, planning the year's meetings, preparing notices and board papers, running the meeting and recording minutes. Pick a role to see only the sections that apply to it.</p>
            <div class="ug-filter" id="ugFilter">
                <span class="lbl">Show for</span>
                <button type="button" class="ug-chip active" data-role="all">All roles</button>
                @foreach($roleMap as $key => $r)
                    <button type="button" class="ug-chip" data-role="{{ $key }}">
                        {{ $r[1] }}@if($myRoles->contains($key))<span class="me">YOU</span>@endif
                    </button>
                @endforeach
            </div>
        </div>

        <div class="row">
            {{-- ───────── Table of contents ───────── --}}
            <div class="col-lg-3 ug-toc-col">
                <div class="card ug-toc">
                    <div class="card-body" id="ugToc">
                        <h6>Contents</h6>
                        <div class="grp">Getting started</div>
                        <a href="#roles" data-roles="all">Who does what</a>
                        <a href="#login" data-roles="all">Logging in</a>
                        <a href="#navigating" data-roles="all">Finding your way around</a>
                        <a href="#lifecycle" data-roles="all">The meeting lifecycle</a>
                        <div class="grp">Setup &amp; administration</div>
                        <a href="#setup" data-roles="superadmin">First-time setup &amp; settings</a>
                        <a href="#organisation" data-roles="superadmin cs">Organisation structure</a>
                        <a href="#users" data-roles="superadmin cs">Users, roles &amp; permissions</a>
                        <a href="#board-members" data-roles="cs">Board members &amp; committees</a>
                        <div class="grp">Meeting workflow</div>
                        <a href="#almanac" data-roles="cs ceo superadmin">Almanac (annual calendar)</a>
                        <a href="#initiate" data-roles="cs ddia superadmin">1. Initiate a meeting</a>
                        <a href="#internal-notice" data-roles="cs ceo ddia superadmin">2–3. Internal notice</a>
                        <a href="#assign" data-roles="cs ddia divhead director">4. Assign officers</a>
                        <a href="#paper" data-roles="divhead director cs ddia">5. Prepare a board paper</a>
                        <a href="#director-review" data-roles="director divhead">6. Director review</a>
                        <a href="#ceo-review" data-roles="ceo ddia superadmin">7. CEO paper review</a>
                        <a href="#board-notice" data-roles="cs ceo ddia director">Board notice to members</a>
                        <a href="#prepare" data-roles="cs ceo ddia">8. Meeting preparation</a>
                        <a href="#live" data-roles="cs ddia board ceo">9. The live meeting</a>
                        <a href="#minutes" data-roles="cs ddia">10. Minutes</a>
                        <div class="grp">Other</div>
                        <a href="#portal" data-roles="board ceo">Board member portal</a>
                        <a href="#audit" data-roles="ddia cs ceo">Audit committee meetings</a>
                        <a href="#records" data-roles="cs ddia">Records &amp; resolutions</a>
                        <a href="#faq" data-roles="all">Troubleshooting</a>
                    </div>
                </div>
            </div>

            {{-- ───────── Content ───────── --}}
            <div class="col-lg-9">

                @include('help.partials.guide_sections')

            </div>
        </div>
    </div>
</div>

<button type="button" class="ug-totop" id="ugTop" title="Back to contents" aria-label="Back to contents"><i class="mdi mdi-chevron-up"></i></button>

@push('scripts')
<script>
(function () {
    var myRoles = @json($myRoles);
    var chips = document.querySelectorAll('#ugFilter .ug-chip');
    var sections = document.querySelectorAll('.ug-sec');
    var tocLinks = document.querySelectorAll('#ugToc a');

    // Highlight role badges that match the viewer's own role(s)
    document.querySelectorAll('.ug-role[data-r]').forEach(function (el) {
        if (myRoles.indexOf(el.dataset.r) !== -1) el.classList.add('mine');
    });

    function matches(el, role) {
        if (role === 'all') return true;
        var roles = (el.dataset.roles || '').split(' ');
        return roles.indexOf('all') !== -1 || roles.indexOf(role) !== -1;
    }

    function applyFilter(role) {
        chips.forEach(function (c) { c.classList.toggle('active', c.dataset.role === role); });
        sections.forEach(function (s) { s.classList.toggle('hidden-by-filter', !matches(s, role)); });
        tocLinks.forEach(function (a) { a.classList.toggle('hidden-by-filter', !matches(a, role)); });
        try { localStorage.setItem('ugRole', role); } catch (e) {}
    }

    chips.forEach(function (c) {
        c.addEventListener('click', function () { applyFilter(c.dataset.role); });
    });

    var saved = null;
    try { saved = localStorage.getItem('ugRole'); } catch (e) {}
    if (saved && document.querySelector('#ugFilter [data-role="' + saved + '"]')) applyFilter(saved);

    // Back-to-contents button once the contents list has scrolled out of view
    var topBtn = document.getElementById('ugTop');
    var toc = document.getElementById('ugToc');
    topBtn.addEventListener('click', function () {
        document.querySelector('.page-title-box').scrollIntoView({ behavior: 'smooth' });
    });
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) {
            topBtn.classList.toggle('show', !entries[0].isIntersecting);
        }).observe(toc);
    }
})();
</script>
@endpush

@endsection
