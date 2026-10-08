@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="{{ route('workflow.meetings.show', $meeting->id) }}" class="btn btn-sm btn-secondary">
                                <i class="mdi mdi-arrow-left"></i>
                            </a>
                            <h4 class="mb-0">Assign Officers</h4>
                        </div>
                        <p class="text-muted mb-0 ms-5">
                            {{ $meeting->title }} &middot; {{ $meeting->reference_number }}
                        </p>
                    </div>
                    <div>
                        @include('workflow.components.countdown-timer', ['meetingDate' => $meeting->meeting_date])
                    </div>
                </div>
            </div>
        </div>

        {{-- Stage Indicator --}}
        @include('workflow.components.stage-indicator', ['currentStage' => 4, 'stageLabel' => 'Officer Assignment', 'meeting' => $meeting])

        {{-- Flash Messages --}}
        @if(session('message'))
            <div class="alert alert-{{ session('alert-type') === 'error' ? 'danger' : session('alert-type', 'success') }} alert-dismissible fade show" role="alert">
                <i class="mdi mdi-{{ session('alert-type') === 'error' ? 'alert-circle-outline' : 'check-circle-outline' }} me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $agendaItems = [];
            if ($meeting->internalNotice && $meeting->internalNotice->agenda_items) {
                $raw = $meeting->internalNotice->agenda_items;
                $agendaItems = is_array($raw) ? $raw : json_decode($raw, true);
            }
        @endphp

        <div class="row">
            {{-- Left: Assignment Form --}}
            <div class="col-xl-8 col-lg-7 mb-3">

                {{-- Existing Assignments --}}
                @if($existingAssignments->count() > 0)
                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">
                                <i class="mdi mdi-check-circle-outline me-1 text-success"></i>
                                Already Assigned
                                <span class="badge bg-success ms-1">{{ $existingAssignments->count() }}</span>
                            </h6>
                            <a href="{{ route('workflow.assignments.dashboard', $meeting->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-view-dashboard-outline"></i> View Dashboard
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th style="font-size: 12px;">Item #</th>
                                            <th style="font-size: 12px;">Agenda Item</th>
                                            <th style="font-size: 12px;">Assigned Officer</th>
                                            <th style="font-size: 12px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($existingAssignments as $ea)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-twwda-navy" style="font-size: 10px;">{{ $ea->agenda_item_number }}</span>
                                                </td>
                                                <td style="font-size: 13px;">{{ Str::limit($ea->agenda_item_title, 50) }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle bg-twwda-navy text-white d-flex align-items-center justify-content-center me-2"
                                                             style="width: 26px; height: 26px; font-size: 10px; font-weight: 600;">
                                                            {{ strtoupper(substr($ea->user->name ?? 'U', 0, 2)) }}
                                                        </div>
                                                        <span style="font-size: 13px;">{{ $ea->user->name ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($ea->isRead())
                                                        <span class="badge bg-success" style="font-size: 10px;">Read</span>
                                                    @else
                                                        <span class="badge bg-warning" style="font-size: 10px;">Unread</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- New Assignment Form --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="mdi mdi-account-plus-outline me-1"></i>
                            {{ $existingAssignments->count() > 0 ? 'Add More Assignments' : 'Assign Officers to Agenda Items' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3" style="font-size: 13px;">
                            <i class="mdi mdi-information-outline me-1"></i>
                            @if(!empty($isAuditCommittee) && $isAuditCommittee)
                                Assign Internal Audit officers to each agenda item. They will be notified via email and in-app notification to prepare their board papers.
                            @else
                                Assign Directors/Deputy Directors to each agenda item. They will be notified via email and in-app notification to prepare their board papers.
                            @endif
                        </p>

                        <form id="assignmentForm"
                              action="{{ $existingAssignments->count() > 0 ? route('workflow.assignments.add', $meeting->id) : route('workflow.assignments.store', $meeting->id) }}"
                              method="POST">
                            @csrf

                            <div id="assignment-rows">
                                @if($existingAssignments->count() === 0 && count($agendaItems) > 0)
                                    {{-- Pre-populate from approved internal notice agenda items --}}
                                    @foreach($agendaItems as $index => $item)
                                        <div class="assignment-row card border mb-3" data-index="{{ $index }}">
                                            <div class="card-body py-3">
                                                <div class="d-flex align-items-start justify-content-between mb-2">
                                                    <span class="badge bg-twwda-navy row-label" style="font-size: 11px;">Assignment {{ $index + 1 }}</span>
                                                    @if($index > 0)
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row" style="font-size: 11px; padding: 2px 8px;">
                                                            <i class="mdi mdi-close"></i> Remove
                                                        </button>
                                                    @endif
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-2">
                                                        <label class="form-label" style="font-size: 12px;">Item #</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="assignments[{{ $index }}][agenda_item_number]"
                                                               value="{{ $index + 1 }}" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label" style="font-size: 12px;">Agenda Item</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="assignments[{{ $index }}][agenda_item_title]"
                                                               value="{{ $item }}" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label" style="font-size: 12px;">Assign To</label>
                                                        <select class="form-select form-select-sm"
                                                                name="assignments[{{ $index }}][user_id]" required>
                                                            <option value="">Select officer...</option>
                                                            @foreach($users as $user)
                                                                @if(($user->_officer_type ?? '') === 'director')
                                                                    <option value="{{ $user->id }}">⭐ {{ $user->name }} — Director, {{ $user->_directorate_name ?? '' }}</option>
                                                                @elseif(($user->_officer_type ?? '') === 'ddia')
                                                                    <option value="{{ $user->id }}">🔒 {{ $user->name }} — DD Internal Audit</option>
                                                                @elseif(($user->_officer_type ?? '') === 'corporation_secretary')
                                                                    <option value="{{ $user->id }}">📋 {{ $user->name }} — Corporation Secretary</option>
                                                                @else
                                                                    <option value="{{ $user->id }}">{{ $user->name }}{{ $user->division ? ' — ' . $user->division->name : '' }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Empty starting row --}}
                                    <div class="assignment-row card border mb-3" data-index="0">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-start justify-content-between mb-2">
                                                <span class="badge bg-twwda-navy row-label" style="font-size: 11px;">Assignment 1</span>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-2">
                                                    <label class="form-label" style="font-size: 12px;">Item #</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           name="assignments[0][agenda_item_number]"
                                                           value="1" required>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label" style="font-size: 12px;">Agenda Item</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           name="assignments[0][agenda_item_title]"
                                                           placeholder="Enter agenda item title" required>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label" style="font-size: 12px;">Assign To</label>
                                                    <select class="form-select form-select-sm"
                                                            name="assignments[0][user_id]" required>
                                                        <option value="">Select officer...</option>
                                                        @foreach($users as $user)
                                                            @if(($user->_officer_type ?? '') === 'director')
                                                                <option value="{{ $user->id }}">⭐ {{ $user->name }} — Director, {{ $user->_directorate_name ?? '' }}</option>
                                                            @elseif(($user->_officer_type ?? '') === 'ddia')
                                                                <option value="{{ $user->id }}">🔒 {{ $user->name }} — DD Internal Audit</option>
                                                            @elseif(($user->_officer_type ?? '') === 'corporation_secretary')
                                                                <option value="{{ $user->id }}">📋 {{ $user->name }} — Corporation Secretary</option>
                                                            @else
                                                                <option value="{{ $user->id }}">{{ $user->name }}{{ $user->division ? ' — ' . $user->division->name : '' }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Add Row Button --}}
                            <button type="button" class="btn btn-sm btn-secondary mb-3" id="add-assignment-btn">
                                <i class="mdi mdi-plus-circle-outline"></i> Add Another Assignment
                            </button>

                            {{-- Form Actions --}}
                            <div class="separator"></div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <a href="{{ route('workflow.meetings.show', $meeting->id) }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn text-white" style="background: {{ setting('primary_color', '#1a1a1a') }};"
                                        onclick="return confirm('Are you sure? Officers will be notified by email and in-app notification.');">
                                    <i class="mdi mdi-send-outline"></i> Assign & Notify Officers
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right: Info Sidebar --}}
            <div class="col-xl-4 col-lg-5 mb-3">

                {{-- Meeting Info --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="mdi mdi-calendar-outline me-1"></i> Meeting Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted d-block">Meeting</small>
                            <span class="fw-semibold" style="font-size: 13px;">{{ $meeting->title }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Committee</small>
                            <span style="font-size: 13px;">{{ $meeting->boardCommittee->name ?? '-' }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Date</small>
                            <span style="font-size: 13px;">{{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->format('d F Y') : '-' }}</span>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted d-block">Venue</small>
                            <span style="font-size: 13px;">{{ $meeting->venue ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Agenda Reference --}}
                @if(count($agendaItems) > 0)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="mdi mdi-format-list-numbered me-1"></i> Agenda (from Notice)</h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach($agendaItems as $idx => $item)
                                    <li class="list-group-item py-2 px-3" style="font-size: 13px;">
                                        <span class="badge bg-twwda-navy me-2" style="font-size: 10px; min-width: 22px;">{{ $idx + 1 }}</span>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Available Officers --}}
                <div class="card mb-0">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="mdi mdi-account-group-outline me-1"></i> Available Officers
                            <span class="badge bg-secondary ms-1" style="font-size: 10px;">{{ $users->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 450px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @forelse($users as $user)
                                @php
                                    $officerType = $user->_officer_type ?? '';
                                    $avatarBg = match($officerType) {
                                        'director' => '#099139',
                                        'ddia' => '#238b47',
                                        'corporation_secretary' => '#e83e8c',
                                        default => setting('primary_color', '#1a1a1a'),
                                    };
                                @endphp
                                <li class="list-group-item py-2 px-3 d-flex align-items-start">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                                         style="width: 32px; height: 32px; font-size: 10px; font-weight: 600; flex-shrink: 0; margin-top: 2px;
                                                background: {{ $avatarBg }};">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <span style="font-size: 13px; font-weight: 500;">{{ $user->name }}</span>
                                        @if($officerType === 'director')
                                            <span class="badge bg-success ms-1" style="font-size: 9px; vertical-align: middle;">Director</span>
                                            <br>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="mdi mdi-domain"></i>
                                                {{ $user->_directorate_name ?? '' }}
                                            </small>
                                            <br>
                                            <small class="text-success" style="font-size: 10px;">
                                                <i class="mdi mdi-arrow-right-bold-circle-outline"></i>
                                                Submits directly to CEO
                                            </small>
                                        @elseif($officerType === 'ddia')
                                            <span class="badge ms-1" style="font-size: 9px; vertical-align: middle; background: #238b47; color: #fff;">DD Internal Audit</span>
                                            <br>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="mdi mdi-shield-check-outline"></i>
                                                Internal Audit
                                            </small>
                                        @elseif($officerType === 'corporation_secretary')
                                            <span class="badge ms-1" style="font-size: 9px; vertical-align: middle; background: #e83e8c; color: #fff;">Corp. Secretary</span>
                                            <br>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="mdi mdi-briefcase-outline"></i>
                                                Corporation Secretary's Office
                                            </small>
                                        @else
                                            @if($user->division)
                                                <br>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    <i class="mdi mdi-office-building-outline"></i>
                                                    {{ $user->division->name }}
                                                </small>
                                                @if($user->division->directorate)
                                                    <br>
                                                    <small class="text-muted" style="font-size: 11px;">
                                                        <i class="mdi mdi-domain"></i>
                                                        {{ $user->division->directorate->name }}
                                                    </small>
                                                @endif
                                            @endif
                                        @endif
                                        @if($user->email)
                                            <br>
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="mdi mdi-email-outline"></i>
                                                {{ $user->email }}
                                            </small>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-3" style="font-size: 13px;">
                                    No officers available.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('assignment-rows');
    var addBtn = document.getElementById('add-assignment-btn');
    var rowIndex = container.querySelectorAll('.assignment-row').length;

    var usersOptions = '<option value="">Select officer...</option>';
    @foreach($users as $user)
        @if(($user->_officer_type ?? '') === 'director')
            usersOptions += '<option value="{{ $user->id }}">⭐ {{ addslashes($user->name) }} — Director, {{ addslashes($user->_directorate_name ?? '') }}</option>';
        @elseif(($user->_officer_type ?? '') === 'ddia')
            usersOptions += '<option value="{{ $user->id }}">🔒 {{ addslashes($user->name) }} — DD Internal Audit</option>';
        @elseif(($user->_officer_type ?? '') === 'corporation_secretary')
            usersOptions += '<option value="{{ $user->id }}">📋 {{ addslashes($user->name) }} — Corporation Secretary</option>';
        @else
            usersOptions += '<option value="{{ $user->id }}">{{ addslashes($user->name) }}{{ $user->division ? ' — ' . addslashes($user->division->name) : '' }}</option>';
        @endif
    @endforeach

    addBtn.addEventListener('click', function() {
        var newRow = document.createElement('div');
        newRow.className = 'assignment-row card border mb-3';
        newRow.setAttribute('data-index', rowIndex);
        newRow.innerHTML = '<div class="card-body py-3">' +
            '<div class="d-flex align-items-start justify-content-between mb-2">' +
                '<span class="badge bg-twwda-navy row-label" style="font-size: 11px;">Assignment ' + (rowIndex + 1) + '</span>' +
                '<button type="button" class="btn btn-sm btn-outline-danger remove-row" style="font-size: 11px; padding: 2px 8px;">' +
                    '<i class="mdi mdi-close"></i> Remove</button>' +
            '</div>' +
            '<div class="row g-3">' +
                '<div class="col-md-2">' +
                    '<label class="form-label" style="font-size: 12px;">Item #</label>' +
                    '<input type="text" class="form-control form-control-sm" name="assignments[' + rowIndex + '][agenda_item_number]" value="' + (rowIndex + 1) + '" required>' +
                '</div>' +
                '<div class="col-md-5">' +
                    '<label class="form-label" style="font-size: 12px;">Agenda Item</label>' +
                    '<input type="text" class="form-control form-control-sm" name="assignments[' + rowIndex + '][agenda_item_title]" placeholder="Enter agenda item title" required>' +
                '</div>' +
                '<div class="col-md-5">' +
                    '<label class="form-label" style="font-size: 12px;">Assign To</label>' +
                    '<select class="form-select form-select-sm" name="assignments[' + rowIndex + '][user_id]" required>' + usersOptions + '</select>' +
                '</div>' +
            '</div>' +
        '</div>';
        container.appendChild(newRow);
        rowIndex++;
        renumberRows();
    });

    container.addEventListener('click', function(e) {
        var removeBtn = e.target.closest('.remove-row');
        if (removeBtn) {
            var rows = container.querySelectorAll('.assignment-row');
            if (rows.length > 1) {
                removeBtn.closest('.assignment-row').remove();
                renumberRows();
            }
        }
    });

    function renumberRows() {
        var rows = container.querySelectorAll('.assignment-row');
        rows.forEach(function(row, idx) {
            row.querySelector('.row-label').textContent = 'Assignment ' + (idx + 1);
        });
    }
});
</script>
@endpush
