@extends('admin_dashboard')

@section('admin')
    <style>
        :root{ --brand:{{ setting('primary_color', '#1a1a1a') }}; --brand-soft:#fdecee; }
        .brand-gradient{ background: linear-gradient(135deg, var(--brand) 0%, #3a3a3a 100%); }
        .text-brand{ color: var(--brand) !important; }
        .bg-brand-soft{ background-color: var(--brand-soft) !important; }
        .card-shadow{ border:0; border-radius:16px; box-shadow:0 8px 22px rgba(0,0,0,.06); }
        .chip{ background:var(--brand-soft); color:var(--brand); border-radius:999px; padding:.25rem .6rem; font-weight:600; }
        .committee-card:hover{ transform: translateY(-2px); transition: .2s ease; }
    </style>

    <div class="content">
        <div class="container-fluid">

            {{-- Hero --}}
            <div class="card brand-gradient text-white card-shadow mb-4">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">My Committees</h3>
                        <div class="opacity-75">
                            {{ optional($member->assignsalutation)->name ? optional($member->assignsalutation)->name.' ' : '' }}{{ $member->names }}
                            • {{ $member->designation ?? 'Board Member' }}
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('board.dashboard') }}" class="btn btn-light btn-sm">Back to Dashboard</a>
                    </div>
                </div>
            </div>

            {{-- Empty state --}}
            @if($committees->isEmpty())
                <div class="card card-shadow">
                    <div class="card-body">
                        <div class="alert bg-brand-soft mb-0">
                            <i class="mdi mdi-information-outline"></i> You are not currently assigned to any committee.
                        </div>
                    </div>
                </div>
            @else
                {{-- Search --}}
                <div class="card card-shadow mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label mb-1">Search committees</label>
                                <input type="text" id="committee-search" class="form-control" placeholder="Type to filter by name or description...">
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('board.meetings.index') }}" class="btn btn-outline-primary">
                                    <i class="mdi mdi-calendar"></i> View Meetings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Committees Grid --}}
                <div class="row g-3" id="committee-grid">
                    @foreach($committees as $c)
                        @php
                            // Lazy fields to avoid errors if columns are absent
                            $name = $c->name ?? 'Committee';
                            $desc = $c->description ?? null;
                            $membersCount = method_exists($c, 'boardMembers') ? optional($c->boardMembers)->count() : null;
                        @endphp
                        <div class="col-lg-4 col-md-6 committee-item" data-key="{{ Str::lower($name.' '.($desc ?? '')) }}">
                            <div class="card card-shadow committee-card h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-start justify-content-between mb-1">
                                        <h5 class="mb-0">{{ $name }}</h5>
                                        <span class="chip"><i class="mdi mdi-shield-account-outline"></i> Member</span>
                                    </div>
                                    <p class="text-muted mb-3" style="min-height: 44px;">
                                        {{ $desc ?: '—' }}
                                    </p>

                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <div class="text-muted small">
                                            @if(!is_null($membersCount))
                                                <i class="mdi mdi-account-multiple"></i> {{ $membersCount }} members
                                            @else
                                                <span class="text-muted"> </span>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            {{-- Pre-filter link to meetings (implement filter handling in meetings index if desired) --}}
                                            <a href="{{ route('board.meetings.index') }}?committee_id={{ $c->id }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-calendar"></i> View Meetings
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Optional: Committee Meta List --}}
                                @if(property_exists($c, 'created_at') && $c->created_at)
                                    <div class="card-footer bg-light py-2">
                                        <small class="text-muted">Joined: {{ optional($c->pivot)->created_at ?? $c->created_at }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <script>
        (function(){
            const input = document.getElementById('committee-search');
            const items = document.querySelectorAll('.committee-item');
            function filter(){
                const q = (input.value || '').toLowerCase().trim();
                items.forEach(el => {
                    const key = el.getAttribute('data-key') || '';
                    el.style.display = (!q || key.includes(q)) ? '' : 'none';
                });
            }
            input.addEventListener('input', filter);
        })();
    </script>
@endsection
