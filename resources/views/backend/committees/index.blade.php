@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0"><i class="mdi mdi-account-group-outline me-1" style="color: #222222;"></i> Board Committees</h3>
            <p class="text-muted mb-0">Manage board committees and their membership</p>
        </div>
        <a href="{{ route('committees.create') }}" class="btn text-white" style="background: #099139;">
            <i class="mdi mdi-plus-circle-outline me-1"></i> Add Committee
        </a>
    </div>

    {{-- Summary Stats --}}
    @php
        $totalCommittees = $committees->count();
        $totalMembers = $committees->sum('members_count');
        $avgMembers = $totalCommittees > 0 ? round($totalMembers / $totalCommittees, 1) : 0;
        $largestCommittee = $committees->sortByDesc('members_count')->first();
    @endphp
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #f2f2f2, #e6e6e6);">
                        <i class="mdi mdi-account-group" style="font-size: 24px; color: #222222;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 24px; color: #222222;">{{ $totalCommittees }}</div>
                        <div class="text-muted" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Committees</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #ecfdf5, #d1fae5);">
                        <i class="mdi mdi-account-multiple-check" style="font-size: 24px; color: #099139;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 24px; color: #099139;">{{ $totalMembers }}</div>
                        <div class="text-muted" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Total Members</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #fefce8, #fef9c3);">
                        <i class="mdi mdi-chart-bar" style="font-size: 24px; color: #d97706;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 24px; color: #d97706;">{{ $avgMembers }}</div>
                        <div class="text-muted" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Avg. Members</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #fce4ec, #fce7f3);">
                        <i class="mdi mdi-trophy-outline" style="font-size: 24px; color: #be185d;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-truncate" style="font-size: 14px; color: #be185d; max-width: 140px;" title="{{ $largestCommittee->name ?? 'N/A' }}">
                            {{ Str::limit($largestCommittee->name ?? 'N/A', 18) }}
                        </div>
                        <div class="text-muted" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Largest Committee</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Committee Cards --}}
    @php
        $committeeIcons = [
            'mdi-shield-check-outline',
            'mdi-finance',
            'mdi-scale-balance',
            'mdi-clipboard-text-search-outline',
            'mdi-account-tie-outline',
            'mdi-handshake-outline',
            'mdi-cog-outline',
            'mdi-lightbulb-outline',
        ];
        $committeeColors = [
            ['bg' => '#f2f2f2', 'text' => '#222222', 'border' => '#222222'],
            ['bg' => '#ecfdf5', 'text' => '#099139', 'border' => '#099139'],
            ['bg' => '#fefce8', 'text' => '#b45309', 'border' => '#d97706'],
            ['bg' => '#e9fef0', 'text' => '#0f9f41', 'border' => '#0f9f41'],
            ['bg' => '#fce4ec', 'text' => '#be185d', 'border' => '#be185d'],
            ['bg' => '#efefef', 'text' => '#282828', 'border' => '#282828'],
            ['bg' => '#fef2f2', 'text' => '#dc2626', 'border' => '#dc2626'],
            ['bg' => '#f0fdf4', 'text' => '#16a34a', 'border' => '#16a34a'],
        ];
    @endphp

    <div class="row">
        @foreach($committees as $i => $committee)
            @php
                $colorSet = $committeeColors[$i % count($committeeColors)];
                $icon = $committeeIcons[$i % count($committeeIcons)];
                $memberCount = $committee->members_count;
            @endphp
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 committee-card" style="border-left: 4px solid {{ $colorSet['border'] }} !important; transition: transform 0.15s, box-shadow 0.15s;">
                    <div class="card-body p-4">
                        {{-- Header --}}
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 48px; height: 48px; background: {{ $colorSet['bg'] }};">
                                    <i class="mdi {{ $icon }}" style="font-size: 24px; color: {{ $colorSet['text'] }};"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold" style="font-size: 15px;">{{ $committee->name }}</h5>
                                    @if($committee->is_audit_committee)
                                        <span class="badge rounded-pill mt-1" style="background: #fff3cd; color: #92400e; font-size: 11px;">
                                            <i class="mdi mdi-shield-check-outline me-1"></i>Audit Committee
                                        </span>
                                    @endif
                                    @if($committee->description)
                                        <small class="text-muted" style="font-size: 12px;">{{ Str::limit($committee->description, 60) }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Member Count Badge --}}
                        <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded-2" style="background: {{ $colorSet['bg'] }};">
                            <i class="mdi mdi-account-multiple-outline" style="font-size: 18px; color: {{ $colorSet['text'] }};"></i>
                            <span class="fw-semibold" style="font-size: 14px; color: {{ $colorSet['text'] }};">
                                {{ $memberCount }} {{ Str::plural('Member', $memberCount) }}
                            </span>
                        </div>

                        {{-- Member Avatars --}}
                        @if($committee->members->count())
                            <div class="d-flex align-items-center mb-3">
                                <div class="d-flex" style="margin-left: 0;">
                                    @foreach($committee->members->take(5) as $j => $member)
                                        <div class="rounded-circle d-flex align-items-center justify-content-center border border-2 border-white"
                                             style="width: 34px; height: 34px; min-width: 34px; background: {{ $colorSet['bg'] }}; margin-left: {{ $j > 0 ? '-8px' : '0' }}; z-index: {{ 10 - $j }};"
                                             title="{{ $member->name ?? '' }}">
                                            <span class="fw-bold" style="font-size: 11px; color: {{ $colorSet['text'] }};">
                                                {{ strtoupper(substr($member->name ?? 'N', 0, 2)) }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($committee->members->count() > 5)
                                        <div class="rounded-circle d-flex align-items-center justify-content-center border border-2 border-white"
                                             style="width: 34px; height: 34px; min-width: 34px; background: #f3f4f6; margin-left: -8px; z-index: 4;">
                                            <span class="fw-bold text-muted" style="font-size: 10px;">+{{ $committee->members->count() - 5 }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="mb-3">
                                <small class="text-muted fst-italic"><i class="mdi mdi-account-off-outline me-1"></i> No members assigned</small>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="d-flex gap-2 mt-auto">
                            <a href="{{ route('committees.members.index', $committee->id) }}" class="btn btn-sm text-white flex-fill" style="background: {{ $colorSet['border'] }};">
                                <i class="mdi mdi-account-multiple-plus-outline me-1"></i> Manage Members
                            </a>
                            <a href="{{ route('committees.edit', $committee->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="mdi mdi-pencil-outline"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if($committees->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width: 80px; height: 80px; background: #f3f4f6;">
                    <i class="mdi mdi-account-group-outline" style="font-size: 40px; color: #9ca3af;"></i>
                </div>
                <h5 class="text-muted mb-2">No Committees Yet</h5>
                <p class="text-muted mb-3" style="font-size: 14px;">Create your first board committee to get started.</p>
                <a href="{{ route('committees.create') }}" class="btn text-white" style="background: #099139;">
                    <i class="mdi mdi-plus-circle-outline me-1"></i> Add Committee
                </a>
            </div>
        </div>
    @endif

</div>

@push('styles')
<style>
    .committee-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush
@endsection
