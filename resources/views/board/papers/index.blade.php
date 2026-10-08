@extends('admin_dashboard')

@section('admin')
    @php
        use Carbon\Carbon;
        // Quick derived stats
        $totalPapers = $papers->count();
        $totalMeetings = $meetings->count();
        $annexuresTotal = $meetings->flatMap->boardPapers->sum(function($bp){ return $bp->annexures->count(); });

        // Committee filter options
        $committeeOptions = $meetings->map(fn($m) => optional($m->committee)->name)->filter()->unique()->values();
    @endphp

    <style>
        :root{ --brand:{{ setting('primary_color', '#1a1a1a') }}; --brand-soft:#fdecee; }
        .brand-gradient{ background: linear-gradient(135deg, var(--brand) 0%, #3a3a3a 100%); }
        .text-brand{ color: var(--brand) !important; }
        .bg-brand{ background-color: var(--brand) !important; }
        .bg-brand-soft{ background-color: var(--brand-soft) !important; }
        .card-shadow{ border:0; border-radius:16px; box-shadow:0 8px 22px rgba(0,0,0,.06); }
        .chip{ background:var(--brand-soft); color:var(--brand); border-radius:999px; padding:.25rem .6rem; font-weight:600; }
        .stat{ border-left:4px solid var(--brand); border-radius:12px; }
        .accordion-button:not(.collapsed){ background:#f9f9f9; color:#141414; }
        .table thead th{ white-space:nowrap; }
    </style>

    <div class="content">
        <div class="container-fluid">

            {{-- Hero --}}
            <div class="card brand-gradient text-white card-shadow mb-4">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">Board Papers</h3>
                        <div class="opacity-75">All papers assigned to meetings you’re part of</div>
                    </div>
                    <div class="text-end">
                        <span class="chip me-1"><i class="mdi mdi-file-document"></i> {{ $totalPapers }} Papers</span>
                        <span class="chip me-1"><i class="mdi mdi-calendar"></i> {{ $totalMeetings }} Meetings</span>
                        <span class="chip"><i class="mdi mdi-paperclip"></i> {{ $annexuresTotal }} Annexures</span>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="card card-shadow mb-3">
                <div class="card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label mb-1">Search</label>
                            <input type="text" class="form-control" id="paper-search" placeholder="Search by paper title, meeting, presenter...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1">Committee</label>
                            <select class="form-select" id="committee-filter">
                                <option value="">All committees</option>
                                @foreach($committeeOptions as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('board.meetings.index') }}" class="btn btn-outline-primary">
                                <i class="mdi mdi-calendar"></i> View Meetings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            @if($meetings->isEmpty())
                <div class="card card-shadow">
                    <div class="card-body">
                        <div class="alert bg-brand-soft mb-0">
                            <i class="mdi mdi-information-outline"></i> No upcoming meetings or papers found.
                        </div>
                    </div>
                </div>
            @else
                <div class="accordion" id="papersAccordion">
                    @foreach($meetings as $mIndex => $m)
                        @php
                            $committeeName = optional($m->committee)->name ?? 'Committee';
                            $dateStr = $m->date ? Carbon::parse($m->date)->isoFormat('ddd, MMM D, h:mm A') : '—';
                            $paperCount = $m->boardPapers->count();
                            $annexCount = $m->boardPapers->sum(fn($bp)=>$bp->annexures->count());
                        @endphp
                        <div class="card card-shadow mb-2 meeting-block"
                             data-committee="{{ $committeeName }}"
                             data-meeting-title="{{ ($m->title ?? '') . ' ' . $committeeName }}"
                        >
                            <div class="card-header p-0" id="heading{{ $mIndex }}">
                                <button class="accordion-button px-3 py-3 w-100 d-flex justify-content-between align-items-center"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $mIndex }}" aria-expanded="{{ $mIndex===0?'true':'false' }}"
                                        aria-controls="collapse{{ $mIndex }}">
                                    <div class="text-start">
                                        <div class="fw-semibold">{{ $committeeName }} — {{ $m->title ?? 'Board Meeting' }}</div>
                                        <small class="text-muted">{{ $dateStr }} @if($m->venue) • Venue: {{ $m->venue }} @endif</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="chip me-1"><i class="mdi mdi-file-document"></i> {{ $paperCount }} Papers</span>
                                        @if($annexCount>0)
                                            <span class="chip"><i class="mdi mdi-paperclip"></i> {{ $annexCount }} Annexures</span>
                                        @endif
                                    </div>
                                </button>
                            </div>

                            <div id="collapse{{ $mIndex }}" class="collapse {{ $mIndex===0?'show':'' }}" aria-labelledby="heading{{ $mIndex }}" data-bs-parent="#papersAccordion">
                                <div class="card-body">

                                    @if($m->boardPapers->isEmpty())
                                        <div class="alert alert-light mb-0">No papers uploaded yet for this meeting.</div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0 papers-table">
                                                <thead>
                                                <tr>
                                                    <th style="width:38%">Paper</th>
                                                    <th>Presenter</th>
                                                    <th>Annexures</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-end" style="width:160px">Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($m->boardPapers as $p)
                                                    @php
                                                        $title = $p->title ?? $p->subject ?? 'Board Paper';
                                                        $presenter = optional($p->user)->name ?? optional($p->user)->email ?? '—';
                                                        $annexC = $p->annexures->count();
                                                        $status = $p->status ?? '—';
                                                        $anchor = 'paper-'.$p->id;
                                                    @endphp
                                                    <tr class="paper-row"
                                                        data-title="{{ Str::lower($title.' '.$committeeName.' '.$presenter.' '.$status) }}"
                                                        data-committee="{{ $committeeName }}"
                                                    >
                                                        <td>
                                                            <div class="fw-semibold">{{ $title }}</div>
                                                            <small class="text-muted">Meeting: {{ $committeeName }}</small>
                                                        </td>
                                                        <td>{{ $presenter }}</td>
                                                        <td>
                                                            @if($annexC>0)
                                                                <span class="badge bg-primary-subtle text-primary">{{ $annexC }}</span>
                                                            @else
                                                                <span class="text-muted">0</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if(Str::of($status)->lower()->contains('final'))
                                                                <span class="badge bg-success">Final</span>
                                                            @elseif(Str::of($status)->lower()->contains('draft'))
                                                                <span class="badge bg-warning text-dark">Draft</span>
                                                            @else
                                                                <span class="badge bg-light text-dark">{{ $status }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="{{ route('board.meetings.show', $m->id) }}?focus={{ $anchor }}"
                                                               class="btn btn-sm btn-outline-primary">
                                                                Open in Meeting
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <div class="text-end mt-3">
                                        <a href="{{ route('board.meetings.show', $m->id) }}" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-open-in-new"></i> Open Meeting
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <script>
        (function() {
            const q = document.getElementById('paper-search');
            const committeeSel = document.getElementById('committee-filter');

            function applyFilter() {
                const query = (q.value || '').trim().toLowerCase();
                const committee = (committeeSel.value || '').trim();

                // Filter meetings by committee
                document.querySelectorAll('.meeting-block').forEach(block => {
                    const blockCommittee = block.getAttribute('data-committee') || '';
                    const showByCommittee = !committee || committee === blockCommittee;

                    // Also filter individual rows by text query
                    let hasVisibleRow = false;
                    block.querySelectorAll('.paper-row').forEach(row => {
                        const text = row.getAttribute('data-title') || '';
                        const rowCommittee = row.getAttribute('data-committee') || '';
                        const matchText = !query || text.includes(query);
                        const matchCommittee = !committee || committee === rowCommittee;
                        const visible = matchText && matchCommittee;
                        row.style.display = visible ? '' : 'none';
                        if (visible) hasVisibleRow = true;
                    });

                    // If no rows matched within a block (or no papers), hide the block
                    block.style.display = (showByCommittee && hasVisibleRow) ? '' : 'none';
                });
            }

            q.addEventListener('input', applyFilter);
            committeeSel.addEventListener('change', applyFilter);
        })();
    </script>
@endsection
