@extends('admin_dashboard')
@section('admin')
@php $auditCommitteeIds = \App\Models\BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray(); @endphp

<div class="content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        @php
                            $isDDIA = auth()->user()->hasRole('DD Internal Audit') && !auth()->user()->hasAnyRole(['CEO', 'Superadmin']);
                        @endphp
                        <h4 class="mb-1">Internal Notices</h4>
                        <p class="text-muted mb-0">
                            @if($isDDIA)
                                Review and approve internal notices for Audit Committee meetings
                            @else
                                Review, approve and track internal meeting notices
                            @endif
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('workflow.dashboard') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('message'))
            <div class="alert alert-{{ session('alert-type') === 'error' ? 'danger' : session('alert-type', 'success') }} alert-dismissible fade show" role="alert">
                <i class="mdi mdi-{{ session('alert-type') === 'error' ? 'alert-circle-outline' : 'check-circle-outline' }} me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tab Navigation --}}
        @php $primaryColor = setting('primary_color', '#1a1a1a'); @endphp
        <ul class="nav nav-tabs mb-0" role="tablist" style="border-bottom: 2px solid #e9ecef;">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}" href="{{ route('workflow.ceo.internal-notices', ['tab' => 'pending']) }}"
                   style="{{ $tab === 'pending' ? 'border-color: ' . $primaryColor . '; color: ' . $primaryColor . '; font-weight: 600; border-bottom: 2px solid ' . $primaryColor . ';' : 'color: #6c757d;' }}">
                    <i class="mdi mdi-clock-outline me-1"></i> Pending
                    @if($counts['pending'] > 0)
                        <span class="badge bg-warning ms-1" style="font-size: 10px;">{{ $counts['pending'] }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'approved' ? 'active' : '' }}" href="{{ route('workflow.ceo.internal-notices', ['tab' => 'approved']) }}"
                   style="{{ $tab === 'approved' ? 'border-color: ' . $primaryColor . '; color: ' . $primaryColor . '; font-weight: 600; border-bottom: 2px solid ' . $primaryColor . ';' : 'color: #6c757d;' }}">
                    <i class="mdi mdi-check-decagram-outline me-1"></i> Approved
                    @if($counts['approved'] > 0)
                        <span class="badge bg-success ms-1" style="font-size: 10px;">{{ $counts['approved'] }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'rejected' ? 'active' : '' }}" href="{{ route('workflow.ceo.internal-notices', ['tab' => 'rejected']) }}"
                   style="{{ $tab === 'rejected' ? 'border-color: ' . $primaryColor . '; color: ' . $primaryColor . '; font-weight: 600; border-bottom: 2px solid ' . $primaryColor . ';' : 'color: #6c757d;' }}">
                    <i class="mdi mdi-close-circle-outline me-1"></i> Rejected
                    @if($counts['rejected'] > 0)
                        <span class="badge bg-danger ms-1" style="font-size: 10px;">{{ $counts['rejected'] }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}" href="{{ route('workflow.ceo.internal-notices', ['tab' => 'all']) }}"
                   style="{{ $tab === 'all' ? 'border-color: ' . $primaryColor . '; color: ' . $primaryColor . '; font-weight: 600; border-bottom: 2px solid ' . $primaryColor . ';' : 'color: #6c757d;' }}">
                    <i class="mdi mdi-format-list-bulleted me-1"></i> All
                    <span class="badge bg-secondary ms-1" style="font-size: 10px;">{{ $counts['all'] }}</span>
                </a>
            </li>
        </ul>

        {{-- Notices Table --}}
        <div class="card" style="border-top-left-radius: 0; border-top-right-radius: 0;">
            <div class="card-body">
                @if($notices->isEmpty())
                    <div class="text-center py-5">
                        @if($tab === 'pending')
                            <i class="mdi mdi-check-all" style="font-size: 56px; color: #099139;"></i>
                            <h5 class="mt-3 mb-2">All Clear</h5>
                            <p class="text-muted mb-0">No internal notices pending approval at this time.</p>
                        @elseif($tab === 'approved')
                            <i class="mdi mdi-file-check-outline" style="font-size: 56px; color: #b5b5c3;"></i>
                            <h5 class="mt-3 mb-2">No Approved Notices</h5>
                            <p class="text-muted mb-0">No internal notices have been approved yet.</p>
                        @elseif($tab === 'rejected')
                            <i class="mdi mdi-file-remove-outline" style="font-size: 56px; color: #b5b5c3;"></i>
                            <h5 class="mt-3 mb-2">No Rejected Notices</h5>
                            <p class="text-muted mb-0">No internal notices have been rejected.</p>
                        @else
                            <i class="mdi mdi-file-document-outline" style="font-size: 56px; color: #b5b5c3;"></i>
                            <h5 class="mt-3 mb-2">No Notices Yet</h5>
                            <p class="text-muted mb-0">No internal notices have been created.</p>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 36px; font-size: 12px;">#</th>
                                    <th style="font-size: 12px;">Meeting</th>
                                    <th style="font-size: 12px;">Reference</th>
                                    <th style="font-size: 12px;">Subject</th>
                                    <th style="font-size: 12px;">Submitted By</th>
                                    @if($tab === 'approved')
                                        <th style="font-size: 12px;">Approved</th>
                                    @elseif($tab === 'all')
                                        <th style="font-size: 12px;">Status</th>
                                    @else
                                        <th style="font-size: 12px;">Submitted</th>
                                    @endif
                                    <th style="width: 100px; font-size: 12px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notices as $index => $notice)
                                    <tr>
                                        <td class="text-muted" style="font-size: 12px;">{{ $notices->firstItem() + $index }}</td>
                                        <td>
                                            <span class="fw-semibold" style="font-size: 13px;">{{ Str::limit($notice->meeting->title ?? '-', 40) }}</span>
                                            <br>
                                            <small class="text-muted">{{ $notice->meeting->boardCommittee->name ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold" style="font-size: 12px;">{{ $notice->reference_number ?? '-' }}</span>
                                        </td>
                                        <td style="font-size: 13px;">{{ Str::limit($notice->subject, 45) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-twwda-navy text-white d-flex align-items-center justify-content-center me-2"
                                                     style="width: 28px; height: 28px; font-size: 11px; font-weight: 600;">
                                                    {{ strtoupper(substr($notice->creator->name ?? 'U', 0, 2)) }}
                                                </div>
                                                <span style="font-size: 13px;">{{ $notice->creator->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        @if($tab === 'approved')
                                            <td>
                                                <span style="font-size: 13px;">{{ $notice->approved_at ? $notice->approved_at->format('d M Y') : '-' }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $notice->approver->name ?? ($notice->meeting && in_array($notice->meeting->board_committee_id, $auditCommitteeIds) ? 'DD Internal Audit' : 'CEO') }}
                                                    @if($notice->status === 'special_approved')
                                                        <span class="badge bg-info" style="font-size: 9px;">Special</span>
                                                    @endif
                                                </small>
                                            </td>
                                        @elseif($tab === 'all')
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        'draft' => ['label' => 'Draft', 'class' => 'bg-secondary'],
                                                        'submitted' => ['label' => 'Pending', 'class' => 'bg-warning'],
                                                        'approved' => ['label' => 'Approved', 'class' => 'bg-success'],
                                                        'special_approved' => ['label' => 'Special Approved', 'class' => 'bg-info'],
                                                        'rejected' => ['label' => 'Rejected', 'class' => 'bg-danger'],
                                                    ];
                                                    $status = $statusMap[$notice->status] ?? ['label' => ucfirst($notice->status), 'class' => 'bg-secondary'];
                                                @endphp
                                                <span class="badge {{ $status['class'] }}" style="font-size: 11px;">{{ $status['label'] }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $notice->updated_at->format('d M Y') }}
                                                </small>
                                            </td>
                                        @else
                                            <td>
                                                <span style="font-size: 13px;">
                                                    {{ $notice->submitted_at ? $notice->submitted_at->format('d M Y') : $notice->updated_at->format('d M Y') }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $notice->submitted_at ? $notice->submitted_at->format('H:i') : $notice->updated_at->format('H:i') }}
                                                </small>
                                            </td>
                                        @endif
                                        <td>
                                            <a href="{{ route('workflow.ceo.internal-notice.review', $notice->id) }}"
                                               class="btn btn-sm {{ $tab === 'pending' ? 'btn-primary' : 'btn-secondary' }}">
                                                <i class="mdi mdi-eye-outline"></i>
                                                {{ $tab === 'pending' ? 'Review' : 'View' }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($notices->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $notices->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
