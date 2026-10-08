@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ route('committees.index') }}" class="btn btn-sm btn-light rounded-pill mb-1">
                <i class="mdi mdi-arrow-left"></i> Back to Committees
            </a>
            <h3 class="mb-0">
                <i class="mdi mdi-account-group-outline me-1" style="color: #222222;"></i>
                {{ $committee->name }}
            </h3>
            @if($committee->description)
                <p class="text-muted mb-0" style="font-size: 13px;">{{ $committee->description }}</p>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2 text-white" style="font-size: 13px; background: #1a1a1a;">
                <i class="mdi mdi-account-multiple me-1"></i>
                <span id="assignedCount">{{ count($assigned) }}</span> {{ Str::plural('Member', count($assigned)) }} Assigned
            </span>
            <span class="badge bg-light text-dark px-3 py-2" style="font-size: 13px;">
                <i class="mdi mdi-account-group-outline me-1"></i>
                {{ $totalBoardMembers }} Total Board Members
            </span>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 46px; height: 46px; background: linear-gradient(135deg, #ecfdf5, #d1fae5);">
                        <i class="mdi mdi-account-check" style="font-size: 22px; color: #099139;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 22px; color: #099139;" id="statAssigned">{{ count($assigned) }}</div>
                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Currently Assigned</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 46px; height: 46px; background: linear-gradient(135deg, #fefce8, #fef9c3);">
                        <i class="mdi mdi-account-clock" style="font-size: 22px; color: #d97706;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 22px; color: #d97706;" id="statUnassigned">{{ $totalBoardMembers - count($assigned) }}</div>
                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Not in Committee</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 46px; height: 46px; background: linear-gradient(135deg, #f2f2f2, #e6e6e6);">
                        <i class="mdi mdi-percent" style="font-size: 22px; color: #222222;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 22px; color: #222222;" id="statPercent">
                            {{ $totalBoardMembers > 0 ? round((count($assigned) / $totalBoardMembers) * 100) : 0 }}%
                        </div>
                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Board Coverage</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('committees.members.store', $committee->id) }}" method="POST" id="memberForm">
        @csrf

        {{-- Search & Filter Bar --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" id="memberSearch"
                                   placeholder="Search members by name, email, or designation..." style="font-size: 14px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus" style="font-size: 14px;">
                            <option value="all">All Members</option>
                            <option value="assigned">Assigned Only</option>
                            <option value="unassigned">Not Assigned</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" id="selectAllBtn">
                            <i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">
                            <i class="mdi mdi-checkbox-multiple-blank-outline me-1"></i> Deselect All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Members Grid --}}
        <div class="row" id="membersGrid">
            @foreach($members->sortByDesc(fn($m) => in_array($m->id, $assigned)) as $member)
                @php
                    $isAssigned = in_array($member->id, $assigned);
                    $initials = collect(explode(' ', $member->names ?? 'N A'))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                @endphp
                <div class="col-xl-3 col-lg-4 col-md-6 mb-3 member-card-wrapper"
                     data-name="{{ strtolower($member->names ?? '') }}"
                     data-email="{{ strtolower($member->email ?? '') }}"
                     data-designation="{{ strtolower($member->designation ?? '') }}"
                     data-assigned="{{ $isAssigned ? '1' : '0' }}">
                    <label class="card border-0 shadow-sm h-100 member-card {{ $isAssigned ? 'member-assigned' : '' }}"
                           for="member-{{ $member->id }}" style="cursor: pointer; transition: all 0.2s;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-3">
                                {{-- Avatar --}}
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 member-avatar"
                                     style="width: 46px; height: 46px; min-width: 46px; font-size: 14px; font-weight: 700; transition: all 0.2s;
                                            {{ $isAssigned ? 'background: #1a1a1a; color: #fff;' : 'background: #f3f4f6; color: #9ca3af;' }}">
                                    {{ $initials }}
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="mb-0 text-truncate fw-semibold" style="font-size: 13px; max-width: 150px;" title="{{ $member->names }}">
                                            {{ $member->names }}
                                        </h6>
                                        <div class="form-check form-switch mb-0 ms-1">
                                            <input class="form-check-input member-checkbox" type="checkbox"
                                                   name="member_ids[]" value="{{ $member->id }}"
                                                   id="member-{{ $member->id }}"
                                                   {{ $isAssigned ? 'checked' : '' }}
                                                   style="width: 2.2em; height: 1.2em; cursor: pointer;">
                                        </div>
                                    </div>
                                    @if($member->designation)
                                        <div class="text-muted text-truncate" style="font-size: 11px;" title="{{ $member->designation }}">
                                            <i class="mdi mdi-briefcase-outline me-1"></i>{{ Str::limit($member->designation, 30) }}
                                        </div>
                                    @endif
                                    @if($member->email)
                                        <div class="text-muted text-truncate" style="font-size: 11px;" title="{{ $member->email }}">
                                            <i class="mdi mdi-email-outline me-1"></i>{{ Str::limit($member->email, 28) }}
                                        </div>
                                    @endif
                                    @if($member->phone)
                                        <div class="text-muted" style="font-size: 11px;">
                                            <i class="mdi mdi-phone-outline me-1"></i>{{ $member->phone }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- Status indicator --}}
                            <div class="mt-2 pt-2 border-top">
                                <small class="member-status-text" style="font-size: 11px;">
                                    @if($isAssigned)
                                        <span class="text-success"><i class="mdi mdi-check-circle me-1"></i>Assigned to this committee</span>
                                    @else
                                        <span class="text-muted"><i class="mdi mdi-circle-outline me-1"></i>Not assigned</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        {{-- Empty search result --}}
        <div class="text-center py-4 d-none" id="noResults">
            <i class="mdi mdi-account-search-outline" style="font-size: 48px; color: #d1d5db;"></i>
            <p class="text-muted mt-2 mb-0">No members match your search.</p>
        </div>

        {{-- Save Button --}}
        <div class="card border-0 shadow-sm mt-2">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div style="font-size: 13px;">
                    <i class="mdi mdi-information-outline text-info me-1"></i>
                    <span class="text-muted">Toggle members on/off, then save. Changes won't take effect until you click <strong>Save</strong>.</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('committees.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn text-white px-4" style="background: #099139;">
                        <i class="mdi mdi-content-save-check-outline me-1"></i> Save Assignments
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>

@push('styles')
<style>
    .member-card {
        border: 2px solid transparent !important;
    }
    .member-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
    }
    .member-card.member-assigned {
        border-color: #C8102E !important;
        background: linear-gradient(135deg, #fcfcfc, #f6f6f6) !important;
    }
    .form-check-input:checked {
        background-color: #1a1a1a;
        border-color: #C8102E;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('memberSearch');
    var filterSelect = document.getElementById('filterStatus');
    var cards = document.querySelectorAll('.member-card-wrapper');
    var noResults = document.getElementById('noResults');
    var grid = document.getElementById('membersGrid');

    function filterMembers() {
        var query = searchInput.value.toLowerCase().trim();
        var filter = filterSelect.value;
        var visible = 0;

        cards.forEach(function(card) {
            var name = card.getAttribute('data-name');
            var email = card.getAttribute('data-email');
            var designation = card.getAttribute('data-designation');
            var isAssigned = card.getAttribute('data-assigned') === '1';

            var matchesSearch = !query || name.indexOf(query) !== -1 || email.indexOf(query) !== -1 || designation.indexOf(query) !== -1;
            var matchesFilter = filter === 'all' || (filter === 'assigned' && isAssigned) || (filter === 'unassigned' && !isAssigned);

            if (matchesSearch && matchesFilter) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visible === 0) {
            noResults.classList.remove('d-none');
            grid.classList.add('d-none');
        } else {
            noResults.classList.add('d-none');
            grid.classList.remove('d-none');
        }
    }

    searchInput.addEventListener('input', filterMembers);
    filterSelect.addEventListener('change', filterMembers);

    // Toggle card visual state when checkbox changes
    document.querySelectorAll('.member-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var card = this.closest('.member-card');
            var wrapper = this.closest('.member-card-wrapper');
            var avatar = card.querySelector('.member-avatar');
            var statusText = card.querySelector('.member-status-text');

            if (this.checked) {
                card.classList.add('member-assigned');
                avatar.style.background = '#222222';
                avatar.style.color = '#fff';
                wrapper.setAttribute('data-assigned', '1');
                statusText.innerHTML = '<span class="text-success"><i class="mdi mdi-check-circle me-1"></i>Assigned to this committee</span>';
            } else {
                card.classList.remove('member-assigned');
                avatar.style.background = '#f3f4f6';
                avatar.style.color = '#9ca3af';
                wrapper.setAttribute('data-assigned', '0');
                statusText.innerHTML = '<span class="text-muted"><i class="mdi mdi-circle-outline me-1"></i>Not assigned</span>';
            }
            updateCounts();
        });
    });

    function updateCounts() {
        var checked = document.querySelectorAll('.member-checkbox:checked').length;
        var total = {{ $totalBoardMembers }};
        document.getElementById('assignedCount').textContent = checked;
        document.getElementById('statAssigned').textContent = checked;
        document.getElementById('statUnassigned').textContent = total - checked;
        document.getElementById('statPercent').textContent = total > 0 ? Math.round((checked / total) * 100) + '%' : '0%';
    }

    // Select All / Deselect All
    document.getElementById('selectAllBtn').addEventListener('click', function() {
        document.querySelectorAll('.member-card-wrapper').forEach(function(wrapper) {
            if (wrapper.style.display !== 'none') {
                var cb = wrapper.querySelector('.member-checkbox');
                if (!cb.checked) {
                    cb.checked = true;
                    cb.dispatchEvent(new Event('change'));
                }
            }
        });
    });

    document.getElementById('deselectAllBtn').addEventListener('click', function() {
        document.querySelectorAll('.member-card-wrapper').forEach(function(wrapper) {
            if (wrapper.style.display !== 'none') {
                var cb = wrapper.querySelector('.member-checkbox');
                if (cb.checked) {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change'));
                }
            }
        });
    });
});
</script>
@endpush
@endsection
