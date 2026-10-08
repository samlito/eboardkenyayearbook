@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- ═══════════════════════════════════════════════════════════
             COMPACT HEADER — Almanac info, status & budget in one strip
             ═══════════════════════════════════════════════════════════ --}}
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-body p-0">
                {{-- Top banner --}}
                <div class="px-4 pt-3 pb-3" style="background: linear-gradient(135deg, #212121 0%, {{ setting('primary_color', '#1a1a1a') }} 60%, #b8000f 100%);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('workflow.ceo.almanac') }}"
                               class="d-flex align-items-center justify-content-center rounded-2"
                               style="width: 36px; height: 36px; background: rgba(255,255,255,0.15); color: #fff;" title="Back">
                                <i class="mdi mdi-arrow-left" style="font-size: 18px;"></i>
                            </a>
                            <div>
                                <h5 class="mb-0 text-white fw-bold" style="font-size: 17px;">Almanac Review — {{ $almanac->serialno }}</h5>
                                <p class="mb-0" style="color: rgba(255,255,255,0.75); font-size: 13px;">
                                    FY {{ $almanac->financialyear->year ?? '-' }} &middot; Created by {{ $almanac->created_by ?? '-' }} &middot; {{ $almanac->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            @if($almanac->status === 'Awaiting Approval')
                                <span class="badge px-3 py-2" style="background: rgba(255,255,255,0.18); color: #fff; font-size: 12px; border: 1px solid rgba(255,255,255,0.3);">
                                    <i class="mdi mdi-clock-outline me-1"></i> Awaiting Approval
                                </span>
                            @elseif($almanac->status === 'Approved')
                                <span class="badge px-3 py-2" style="background: #099139; color: #fff; font-size: 12px;">
                                    <i class="mdi mdi-check-decagram me-1"></i> Approved
                                </span>
                            @elseif($almanac->status === 'Rejected')
                                <span class="badge px-3 py-2" style="background: #f1416c; color: #fff; font-size: 12px;">
                                    <i class="mdi mdi-close-circle me-1"></i> Rejected
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Budget strip --}}
                @php
                    $pct = $almanac->budget > 0 ? min(100, ($totalScheduleBudget / $almanac->budget) * 100) : 0;
                @endphp
                <div class="d-flex flex-wrap align-items-center border-top" style="background: #fafafa;">
                    <div class="flex-fill px-3 py-2 border-end text-center">
                        <div class="fw-bold" style="font-size: 16px; color: {{ setting('primary_color', '#1a1a1a') }};">KES {{ number_format($almanac->budget, 2) }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.04em;">Total Budget</small>
                    </div>
                    <div class="flex-fill px-3 py-2 border-end text-center">
                        <div class="fw-bold" style="font-size: 16px; color: #c50010;">KES {{ number_format($totalScheduleBudget, 2) }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.04em;">Allocated</small>
                    </div>
                    <div class="flex-fill px-3 py-2 border-end text-center">
                        <div class="fw-bold" style="font-size: 16px; color: {{ $remainingBudget >= 0 ? '#099139' : '#f1416c' }};">KES {{ number_format($remainingBudget, 2) }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.04em;">Remaining</small>
                    </div>
                    <div class="flex-fill px-3 py-2 text-center">
                        <div class="fw-bold" style="font-size: 16px; color: #0f0f0f;">{{ $schedules->count() }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.04em;">Meetings</small>
                    </div>
                </div>
                {{-- Budget progress bar --}}
                <div style="height: 4px; background: #e9ecef;">
                    <div style="height: 100%; width: {{ $pct }}%; background: {{ $remainingBudget >= 0 ? '#099139' : '#f1416c' }}; transition: width 0.4s;"></div>
                </div>
                @if($remainingBudget < 0)
                <div class="px-3 py-2" style="background: #fff5f5; border-top: 1px solid #f5c6cb;">
                    <small style="color: #c92a2a; font-size: 12px;">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>Schedule budgets exceed almanac budget by <strong>KES {{ number_format(abs($remainingBudget), 2) }}</strong>
                    </small>
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            {{-- ═══ Left Column: Schedules ═════════════════════════════ --}}
            <div class="col-lg-8">

                {{-- All committees in ONE card with accordion --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between" style="border-left: 3px solid {{ setting('primary_color', '#1a1a1a') }}; background: #fff;">
                        <h6 class="mb-0" style="font-size: 14px; color: #0f0f0f;">
                            <i class="mdi mdi-calendar-text me-1" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i> Meeting Schedules by Committee
                        </h6>
                        <span class="badge" style="background: #f2f2f2; color: {{ setting('primary_color', '#1a1a1a') }}; font-size: 11px;">
                            {{ $groupedSchedules->count() }} committees &middot; {{ $schedules->count() }} meetings
                        </span>
                    </div>
                    <div class="card-body p-0">
                        @forelse($groupedSchedules as $committeeName => $committeeSchedules)
                            @php $accordionId = 'ceo-committee-' . Str::slug($committeeName); @endphp

                            {{-- Committee accordion header --}}
                            <div class="border-bottom">
                                <button class="btn w-100 text-start p-0 border-0 bg-transparent shadow-none almanac-show-toggle"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#{{ $accordionId }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2" style="background: {{ $loop->first ? '#fafafa' : '#fff' }};">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded"
                                                 style="width: 30px; height: 30px; background: #f2f2f2; flex-shrink: 0;">
                                                <i class="mdi mdi-account-group-outline" style="font-size: 15px; color: {{ setting('primary_color', '#1a1a1a') }};"></i>
                                            </div>
                                            <span class="fw-semibold" style="font-size: 13.5px; color: #0f0f0f;">{{ $committeeName }}</span>
                                            <span class="badge" style="background: #e6f6ec; color: #077a2e; font-size: 10.5px;">
                                                {{ $committeeSchedules->count() }} {{ Str::plural('meeting', $committeeSchedules->count()) }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="fw-semibold" style="font-size: 12.5px; color: #077a2e;">
                                                KES {{ number_format($committeeSchedules->sum('budget'), 0) }}
                                            </span>
                                            <i class="mdi mdi-chevron-down almanac-show-chevron text-muted" style="font-size: 20px; transition: transform 0.2s;"></i>
                                        </div>
                                    </div>
                                </button>

                                {{-- Schedule table --}}
                                <div id="{{ $accordionId }}" class="collapse {{ $loop->first ? 'show' : '' }}">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 almanac-show-table">
                                            <thead>
                                                <tr style="background: #f5f7f9;">
                                                    <th style="width: 32px; font-size: 11px;" class="ps-3 text-muted">#</th>
                                                    <th style="font-size: 11px;" class="text-muted">Date</th>
                                                    <th style="font-size: 11px;" class="text-muted">Purpose / Agenda</th>
                                                    <th style="font-size: 11px; width: 130px;" class="text-end pe-3 text-muted">Budget (KES)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($committeeSchedules as $idx => $schedule)
                                                <tr>
                                                    <td class="ps-3" style="color: #8b95a1; font-size: 12px;">{{ $idx + 1 }}</td>
                                                    <td style="white-space: nowrap;">
                                                        <span class="fw-semibold" style="font-size: 13px; color: #0f0f0f;">{{ $schedule->meeting_date ? $schedule->meeting_date->format('d M Y') : '-' }}</span>
                                                        @if($schedule->meeting_date)
                                                        <br><small style="color: #6b7785; font-size: 11.5px;">{{ $schedule->meeting_date->format('l') }}</small>
                                                        @endif
                                                    </td>
                                                    <td style="white-space: normal; max-width: 340px; font-size: 13px; color: #3d4852; line-height: 1.45;">
                                                        {{ $schedule->purpose ?? '—' }}
                                                    </td>
                                                    <td class="text-end pe-3 fw-semibold" style="font-size: 13px; color: #077a2e;">
                                                        {{ number_format($schedule->budget ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="background: #f5f7f9;">
                                                    <td colspan="3" class="ps-3 fw-semibold" style="font-size: 12.5px; color: #0f0f0f;">Subtotal</td>
                                                    <td class="text-end pe-3 fw-bold" style="font-size: 13px; color: {{ setting('primary_color', '#1a1a1a') }};">
                                                        KES {{ number_format($committeeSchedules->sum('budget'), 2) }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="mdi mdi-calendar-blank-outline text-muted" style="font-size: 42px;"></i>
                                <h6 class="mt-2 mb-1" style="color: #0f0f0f;">No Schedules</h6>
                                <p class="text-muted mb-0" style="font-size: 13px;">No meeting schedules have been added to this almanac.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- ═══ Right Column: Decision + Summary ═══════════════════ --}}
            <div class="col-lg-4">

                {{-- Approval Decision --}}
                @if($almanac->status === 'Awaiting Approval')
                <div class="card border-0 shadow-sm mb-3" style="position: sticky; top: 75px;">
                    <div class="card-header py-2" style="background: linear-gradient(135deg, {{ setting('primary_color', '#1a1a1a') }} 0%, #212121 100%);">
                        <h6 class="mb-0 text-white" style="font-size: 14px;"><i class="mdi mdi-gavel me-1"></i> Your Decision</h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="POST" action="{{ route('workflow.ceo.almanac.decision', $almanac->id) }}" id="almanacDecisionForm">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size: 13px;">Decision</label>
                                <select name="status" id="almanac_decision" class="form-select form-select-sm" required>
                                    <option value="">-- Select Decision --</option>
                                    <option value="Approved">Approve Almanac</option>
                                    <option value="Rejected">Reject Almanac</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3" id="rejection_comment_div" style="display: none;">
                                <label class="form-label fw-semibold" style="font-size: 13px;">Rejection Comment <span class="text-danger">*</span></label>
                                <textarea name="comment" class="form-control form-control-sm" rows="3" placeholder="Provide reason for rejection..."
                                          style="font-size: 13px;">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-sm btn-primary" id="submitDecisionBtn">
                                    <i class="mdi mdi-check-circle-outline me-1"></i> Submit Decision
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                {{-- Already decided --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header py-2" style="background: {{ $almanac->status === 'Approved' ? '#099139' : '#f1416c' }};">
                        <h6 class="mb-0 text-white" style="font-size: 14px;">
                            <i class="mdi {{ $almanac->status === 'Approved' ? 'mdi-check-decagram' : 'mdi-close-circle' }} me-1"></i>
                            {{ $almanac->status }}
                        </h6>
                    </div>
                    <div class="card-body text-center py-3">
                        <i class="mdi {{ $almanac->status === 'Approved' ? 'mdi-check-decagram-outline' : 'mdi-close-circle-outline' }}"
                           style="font-size: 40px; color: {{ $almanac->status === 'Approved' ? '#099139' : '#f1416c' }};"></i>
                        <p class="mt-2 mb-1 fw-semibold" style="font-size: 14px; color: {{ $almanac->status === 'Approved' ? '#099139' : '#f1416c' }};">
                            This almanac has been {{ strtolower($almanac->status) }}.
                        </p>
                        @if($almanac->ceo_approval_date)
                        <small class="text-muted" style="font-size: 12px;">{{ $almanac->ceo_approval_date->format('d M Y, H:i') }}</small>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Committee Budget Summary --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header py-2" style="border-left: 3px solid #c50010; background: #fff;">
                        <h6 class="mb-0" style="font-size: 13px; color: #0f0f0f;">
                            <i class="mdi mdi-chart-bar me-1" style="color: #c50010;"></i> Budget by Committee
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($groupedSchedules as $committeeName => $cs)
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <span style="font-size: 12.5px; font-weight: 600; color: #0f0f0f;">{{ $committeeName }}</span>
                                <br>
                                <small style="color: #6b7785; font-size: 11px;">{{ $cs->count() }} {{ Str::plural('meeting', $cs->count()) }}</small>
                            </div>
                            <span class="fw-bold" style="font-size: 12.5px; color: #077a2e;">
                                KES {{ number_format($cs->sum('budget'), 0) }}
                            </span>
                        </div>
                        @endforeach
                        @if($groupedSchedules->isEmpty())
                        <div class="text-center py-3">
                            <small class="text-muted">No schedules</small>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Review Comments History --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header py-2" style="border-left: 3px solid #6c757d; background: #fff;">
                        <h6 class="mb-0" style="font-size: 13px; color: #0f0f0f;">
                            <i class="mdi mdi-comment-text-multiple-outline me-1"></i> Review Comments
                            @if($rejectedComments->isNotEmpty())
                                <span class="badge bg-secondary ms-1" style="font-size: 10px;">{{ $rejectedComments->count() }}</span>
                            @endif
                        </h6>
                    </div>
                    <div class="card-body py-2 px-3">
                        @forelse($rejectedComments as $rc)
                        <div class="p-2 rounded mb-2" style="background: #fff5f8; border-left: 2px solid #f1416c;">
                            <p class="mb-1" style="font-size: 13px; color: #3d4852;">{{ $rc->comment }}</p>
                            <small style="color: #8b95a1; font-size: 11px;">
                                <i class="mdi mdi-calendar me-1"></i>
                                {{ $rc->comment_date ? \Carbon\Carbon::parse($rc->comment_date)->format('d M Y') : $rc->created_at->format('d M Y') }}
                            </small>
                        </div>
                        @empty
                        <div class="text-center py-3">
                            <i class="mdi mdi-comment-remove-outline" style="font-size: 28px; color: #d1d5db;"></i>
                            <p class="mb-0 mt-1" style="font-size: 12px; color: #8b95a1;">No review comments.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Page Styles --}}
<style>
    .almanac-show-toggle:focus { box-shadow: none !important; }
    .almanac-show-toggle[aria-expanded="true"] .almanac-show-chevron { transform: rotate(180deg); }
    .almanac-show-toggle:hover > div { background: #f6f6f6 !important; }
    .almanac-show-table th {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 600;
        padding: 6px 8px;
        border-bottom: 1px solid #e5e9ef;
    }
    .almanac-show-table td {
        padding: 8px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f2f5;
    }
    .almanac-show-table tbody tr:hover {
        background: #f9fafb;
    }
    .almanac-show-table tfoot td {
        padding: 8px 8px;
        border-top: 2px solid #e5e9ef;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var decisionSelect = document.getElementById('almanac_decision');
        var commentDiv = document.getElementById('rejection_comment_div');
        var submitBtn = document.getElementById('submitDecisionBtn');

        if (decisionSelect) {
            decisionSelect.addEventListener('change', function() {
                if (this.value === 'Rejected') {
                    commentDiv.style.display = 'block';
                    submitBtn.innerHTML = '<i class="mdi mdi-close-circle-outline me-1"></i> Reject Almanac';
                    submitBtn.classList.remove('btn-primary', 'btn-success');
                    submitBtn.classList.add('btn-danger');
                } else if (this.value === 'Approved') {
                    commentDiv.style.display = 'none';
                    submitBtn.innerHTML = '<i class="mdi mdi-check-decagram-outline me-1"></i> Approve Almanac';
                    submitBtn.classList.remove('btn-primary', 'btn-danger');
                    submitBtn.classList.add('btn-success');
                } else {
                    commentDiv.style.display = 'none';
                    submitBtn.innerHTML = '<i class="mdi mdi-check-circle-outline me-1"></i> Submit Decision';
                    submitBtn.classList.remove('btn-success', 'btn-danger');
                    submitBtn.classList.add('btn-primary');
                }
            });
        }

        var form = document.getElementById('almanacDecisionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                var decision = decisionSelect.value;
                var msg = decision === 'Approved'
                    ? 'Are you sure you want to APPROVE this almanac?'
                    : 'Are you sure you want to REJECT this almanac?';
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush

@endsection
