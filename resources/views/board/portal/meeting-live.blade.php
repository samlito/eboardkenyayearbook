@extends('admin_dashboard')

@section('admin')
    @php use Carbon\Carbon; @endphp

    @push('styles')
    <style>
        /* ═══════════════════════════════════════════════════════════════
           Board Portal — Live Meeting Day Experience
           iPad-optimized  |  15px base  |  44px touch targets
        ═══════════════════════════════════════════════════════════════ */
        :root {
            --twwda-brand: {{ setting('primary_color', '#1a1a1a') }};
            --twwda-brand-soft: #fdecee;
            --twwda-green: #099139;
            --twwda-green-soft: #e6f5eb;
        }

        /* ── Base ── */
        .twwda-content { font-size: 15px; }

        /* ── Brand helpers ── */
        .twwda-brand-gradient { background: linear-gradient(135deg, var(--twwda-brand) 0%, #3a3a3a 100%); }
        .twwda-text-brand { color: var(--twwda-brand) !important; }
        .twwda-bg-brand-soft { background-color: var(--twwda-brand-soft) !important; }
        .twwda-bg-green-soft { background-color: var(--twwda-green-soft) !important; }
        .twwda-btn-brand {
            background: var(--twwda-brand); border-color: var(--twwda-brand); color: #fff;
            border-radius: 10px; min-height: 44px; font-weight: 600;
        }
        .twwda-btn-brand:hover { background: #2b2b2b; border-color: #9b0c23; color: #fff; }
        .twwda-btn-green {
            background: var(--twwda-green); border-color: var(--twwda-green); color: #fff;
            border-radius: 10px; min-height: 44px; font-weight: 600;
        }
        .twwda-btn-green:hover { background: #077a2e; border-color: #077a2e; color: #fff; }
        .twwda-card {
            border: 0; border-radius: 16px;
            box-shadow: 0 8px 22px rgba(0,0,0,.06);
        }

        /* ═══════ LIVE MEETING BANNER ═══════ */
        .twwda-live-banner {
            background: linear-gradient(135deg, #d32f2f 0%, #ff6d00 100%);
            border-radius: 16px; color: #fff; position: relative; overflow: hidden;
        }
        .twwda-live-banner::before {
            content: '';
            position: absolute; inset: 0;
            background: repeating-linear-gradient(
                90deg,
                rgba(255,255,255,.03) 0px,
                rgba(255,255,255,.03) 2px,
                transparent 2px,
                transparent 8px
            );
            pointer-events: none;
        }
        .twwda-live-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.2); backdrop-filter: blur(4px);
            border-radius: 999px; padding: .4rem 1rem;
            font-weight: 800; font-size: .85rem; letter-spacing: 1px;
        }
        .twwda-live-dot {
            width: 10px; height: 10px; border-radius: 50%; background: #fff;
            animation: twwdaBlink 1s ease-in-out infinite;
        }
        @keyframes twwdaBlink {
            0%, 100% { opacity: 1; }
            50%      { opacity: .25; }
        }
        .twwda-live-pulse {
            animation: twwdaPulse 2s ease-in-out infinite;
        }
        @keyframes twwdaPulse {
            0%   { box-shadow: 0 0 0 0 rgba(255,255,255,.35); }
            70%  { box-shadow: 0 0 0 12px rgba(255,255,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
        }

        /* ═══════ CONFLICT DECLARATION SECTION ═══════ */
        .twwda-conflict-warning {
            border-left: 5px solid #ff9800;
            background: #fff8e1;
        }
        .twwda-conflict-success {
            border-left: 5px solid var(--twwda-green);
            background: var(--twwda-green-soft);
        }

        /* ═══════ AGENDA ACCORDION ═══════ */
        .twwda-agenda-accordion .accordion-item {
            border: none; border-radius: 12px !important;
            margin-bottom: 1rem; overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,.05);
        }
        .twwda-agenda-accordion .accordion-button {
            background: #fff; font-weight: 600; font-size: 1rem;
            padding: 1rem 1.25rem; min-height: 56px;
        }
        .twwda-agenda-accordion .accordion-button:not(.collapsed) {
            background: var(--twwda-brand-soft); color: var(--twwda-brand);
            box-shadow: none;
        }
        .twwda-agenda-accordion .accordion-button::after {
            flex-shrink: 0; width: 1.1rem; height: 1.1rem;
        }
        .twwda-agenda-accordion .accordion-body {
            padding: 1.25rem; background: #fff;
        }
        .twwda-item-number {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--twwda-brand); color: #fff; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: .95rem;
        }
        .twwda-source-badge-notice { background: #f0f0f0; color: #2b2b2b; }
        .twwda-source-badge-cs_added { background: #fff3e0; color: #e65100; }
        .twwda-source-badge-ddia_added { background: #e2f8ea; color: #1c9245; }

        /* ═══════ STICKY NOTES ═══════ */
        .twwda-sticky-note {
            border-radius: 8px; padding: .5rem .75rem; margin-bottom: .35rem;
            font-size: .85rem; border-left: 4px solid;
        }
        .twwda-sticky-yellow { background: #fff8e1; border-color: #ffc107; }
        .twwda-sticky-blue   { background: #f0f0f0; border-color: #df0c1e; }
        .twwda-sticky-green  { background: #e8f5e9; border-color: #4caf50; }
        .twwda-sticky-pink   { background: #fce4ec; border-color: #e91e63; }

        /* ═══════ SIDEBAR CARDS ═══════ */
        .twwda-sidebar-sticky { position: sticky; top: 80px; }
        .twwda-member-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            object-fit: cover; border: 2px solid #e9ecef;
        }
        .twwda-member-initials {
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--twwda-brand-soft); color: var(--twwda-brand);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem; flex-shrink: 0;
        }

        /* ═══════ SIGNATURE CANVAS ═══════ */
        .twwda-signature-canvas {
            border: 2px dashed #ccc; border-radius: 10px;
            background: #fff; cursor: crosshair;
            touch-action: none; width: 100%; max-width: 400px; height: 160px;
        }

        /* ═══════ iPad optimizations ═══════ */
        @media (max-width: 1024px) {
            .twwda-content { font-size: 15px; }
            .btn, .form-control, .form-select { min-height: 44px; font-size: 15px; }
            .twwda-sidebar-sticky { position: relative; top: auto; }
        }
        @media (min-width: 768px) and (max-width: 1200px) {
            .twwda-btn-brand, .twwda-btn-green { padding: .6rem 1.2rem; font-size: 1rem; }
        }
    </style>
    @endpush

    <div class="content twwda-content">
        <div class="container-fluid">

            {{-- ╔══════════════════════════════════════════════════════════════╗
                 ║  1. LIVE MEETING BANNER                                     ║
                 ╚══════════════════════════════════════════════════════════════╝ --}}
            <div class="twwda-live-banner p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between position-relative" style="z-index:1;">
                    <div>
                        <a href="{{ route('board.portal.active') }}" class="text-white-50 text-decoration-none small d-inline-flex align-items-center mb-1" style="min-height:44px;">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to Active Meetings
                        </a>
                        <h3 class="fw-bold mb-1 text-white">{{ $meeting->title }}</h3>
                        <div class="text-white" style="opacity:.8;">
                            <i class="mdi mdi-account-group-outline"></i>
                            {{ optional($meeting->boardCommittee)->name ?? 'Board Meeting' }}
                            &middot;
                            <i class="mdi mdi-calendar"></i>
                            {{ Carbon::parse($meeting->meeting_date)->isoFormat('ddd, D MMM YYYY') }}
                            @if($meeting->meeting_time)
                                &middot; <i class="mdi mdi-clock-outline"></i>
                                {{ Carbon::parse($meeting->meeting_time)->format('g:i A') }}
                            @endif
                            @if($meeting->venue)
                                &middot; <i class="mdi mdi-map-marker-outline"></i> {{ $meeting->venue }}
                            @endif
                        </div>
                    </div>
                    <div class="text-end mt-2 mt-md-0">
                        <span class="twwda-live-badge twwda-live-pulse">
                            <span class="twwda-live-dot"></span> LIVE
                        </span>
                        @if($meeting->started_at)
                            <div class="small text-white mt-1" style="opacity:.75;">
                                <i class="mdi mdi-clock-start"></i>
                                Started {{ Carbon::parse($meeting->started_at)->diffForHumans() }}
                                <br>{{ Carbon::parse($meeting->started_at)->format('g:i A') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ╔══════════════════════════════════════════════════════════════╗
                 ║  2. CONFLICT OF INTEREST DECLARATION                        ║
                 ╚══════════════════════════════════════════════════════════════╝ --}}
            @if(!$myDeclaration || !$myDeclaration->isComplete())
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-9">

                        {{-- ── STATE A: No declaration yet ── --}}
                        @if(!$myDeclaration)
                            <div class="card twwda-card twwda-conflict-warning">
                                <div class="card-header twwda-bg-brand-soft py-3">
                                    <h5 class="mb-0 fw-bold twwda-text-brand">
                                        <i class="mdi mdi-shield-alert-outline me-1"></i>
                                        Conflict of Interest Declaration
                                        <span class="badge bg-danger ms-2">Required</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning d-flex align-items-start gap-2 mb-4" role="alert">
                                        <i class="mdi mdi-information-outline mt-1" style="font-size:1.2rem;"></i>
                                        <div>
                                            Before participating in this meeting, you are required to declare whether you have
                                            any conflict of interest regarding any agenda item. This is a mandatory requirement
                                            under the {{ setting('institution_acronym', 'ORG') }} Board governance policy.
                                        </div>
                                    </div>

                                    <form id="twwdaConflictForm" method="POST" action="{{ route('workflow.meeting.conflict.submit', $meeting) }}">
                                        @csrf
                                        <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
                                        <input type="hidden" name="board_member_id" value="{{ $member->id }}">

                                        {{-- Radio Selection --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold fs-6">
                                                Do you have a conflict of interest in any of the agenda items? <span class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex flex-wrap gap-3 mt-2">
                                                <div class="form-check form-check-inline" style="min-height:44px;">
                                                    <input class="form-check-input" type="radio" name="has_conflict"
                                                           id="twwdaConflictNo" value="0" checked>
                                                    <label class="form-check-label fw-semibold" for="twwdaConflictNo">
                                                        <span class="text-success"><i class="mdi mdi-check-circle-outline me-1"></i>No</span>, I have no conflict
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline" style="min-height:44px;">
                                                    <input class="form-check-input" type="radio" name="has_conflict"
                                                           id="twwdaConflictYes" value="1">
                                                    <label class="form-check-label fw-semibold" for="twwdaConflictYes">
                                                        <span class="text-danger"><i class="mdi mdi-alert-circle-outline me-1"></i>Yes</span>, I have a conflict
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- No Conflict Confirmation --}}
                                        <div id="twwdaNoConflictInfo" class="alert alert-success">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                            You are confirming that you have <strong>no conflict of interest</strong>
                                            with any agenda item in this meeting.
                                        </div>

                                        {{-- Conflict Details (hidden by default) --}}
                                        <div id="twwdaConflictFields" style="display: none;">
                                            <div class="alert alert-danger border-0 mb-3">
                                                <i class="mdi mdi-alert-outline me-1"></i>
                                                Please provide full details of your conflict of interest as required under the {{ setting('institution_acronym', 'ORG') }} governance framework.
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Person Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="person_name" class="form-control"
                                                           placeholder="Full name of interested person / entity">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Person Address</label>
                                                    <input type="text" name="person_address" class="form-control"
                                                           placeholder="Address of the person / entity">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Nature of Interest <span class="text-danger">*</span></label>
                                                    <textarea name="interest_nature" class="form-control" rows="3"
                                                              placeholder="Describe the nature of your interest in the agenda item(s)..."></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Value of Interest (KES)</label>
                                                    <input type="number" name="interest_value" class="form-control"
                                                           placeholder="0.00" step="0.01" min="0">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Interest Ceased On</label>
                                                    <input type="date" name="interest_ceased_on" class="form-control">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Remarks</label>
                                                    <textarea name="remarks" class="form-control" rows="2"
                                                              placeholder="Any additional remarks"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn twwda-btn-brand btn-lg w-100 mt-4" id="twwdaSubmitConflictBtn">
                                            <i class="mdi mdi-send-outline me-1"></i> Submit Declaration
                                        </button>
                                    </form>
                                </div>
                            </div>

                        {{-- ── STATE B: Declaration submitted, OTP not verified ── --}}
                        @elseif(!$myDeclaration->isVerified())
                            <div class="card twwda-card">
                                <div class="card-header bg-warning bg-opacity-25 py-3">
                                    <h5 class="mb-0 fw-bold">
                                        <i class="mdi mdi-email-check-outline me-1"></i> OTP Verification Required
                                    </h5>
                                </div>
                                <div class="card-body text-center py-5">
                                    <i class="mdi mdi-shield-key-outline text-warning" style="font-size: 64px;"></i>
                                    <h5 class="mt-3 fw-bold">Verification Code Sent</h5>
                                    <p class="text-muted mb-1">
                                        A 6-digit verification code has been sent to your registered email address.
                                    </p>
                                    <p class="text-muted mb-4">
                                        <i class="mdi mdi-email-outline"></i>
                                        <strong>{{ $member->email ?? 'your email' }}</strong>
                                    </p>

                                    <form id="twwdaOtpForm" method="POST" action="{{ route('workflow.meeting.conflict.verify', $meeting) }}">
                                        @csrf
                                        <input type="hidden" name="declaration_id" value="{{ $myDeclaration->id }}">

                                        <div class="row justify-content-center">
                                            <div class="col-sm-8 col-md-6 col-lg-5">
                                                <div class="input-group input-group-lg mb-3">
                                                    <input type="text" id="twwdaOtpInput" name="otp_code"
                                                           class="form-control text-center" maxlength="6"
                                                           placeholder="- - - - - -" inputmode="numeric" autocomplete="one-time-code"
                                                           style="letter-spacing: 10px; font-weight: 700; font-size: 1.6rem;">
                                                </div>

                                                <button type="submit" class="btn twwda-btn-green btn-lg w-100 mb-3" id="twwdaVerifyOtpBtn">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i> Verify OTP
                                                </button>

                                                <button type="button" class="btn btn-link text-muted btn-sm" id="twwdaResendOtpBtn">
                                                    <i class="mdi mdi-email-sync-outline me-1"></i> Resend OTP
                                                </button>
                                                <span id="twwdaResendTimer" class="text-muted small ms-1" style="display:none;"></span>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        {{-- ── STATE C: OTP verified, signature needed ── --}}
                        @elseif($myDeclaration->isVerified() && !$myDeclaration->isSigned())
                            <div class="card twwda-card">
                                <div class="card-header twwda-bg-brand-soft py-3">
                                    <h5 class="mb-0 fw-bold twwda-text-brand">
                                        <i class="mdi mdi-draw-pen me-1"></i> Signature Required
                                    </h5>
                                </div>
                                <div class="card-body py-4">
                                    {{-- Declaration Summary --}}
                                    <div class="twwda-bg-brand-soft rounded-3 p-3 mb-4">
                                        <h6 class="fw-semibold mb-2"><i class="mdi mdi-file-check-outline me-1"></i> Declaration Summary</h6>
                                        <div class="row g-2" style="font-size:.92rem;">
                                            <div class="col-sm-6">
                                                <span class="text-muted">Member:</span>
                                                <strong class="ms-1">{{ $member->names }}</strong>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="text-muted">Conflict:</span>
                                                @if($myDeclaration->has_conflict)
                                                    <span class="badge bg-danger ms-1">Yes</span>
                                                @else
                                                    <span class="badge bg-success ms-1">No</span>
                                                @endif
                                            </div>
                                            @if($myDeclaration->has_conflict && $myDeclaration->interest_nature)
                                                <div class="col-12">
                                                    <span class="text-muted">Nature:</span>
                                                    <span class="ms-1">{{ $myDeclaration->interest_nature }}</span>
                                                </div>
                                            @endif
                                            <div class="col-sm-6">
                                                <span class="text-muted">OTP Verified:</span>
                                                <span class="badge bg-success ms-1"><i class="mdi mdi-check me-1"></i>Verified</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <i class="mdi mdi-gesture twwda-text-brand" style="font-size: 56px;"></i>
                                        <h5 class="mt-2 fw-bold">Please Sign Below</h5>
                                        <p class="text-muted mb-3">
                                            Draw your signature in the box below to complete the declaration.
                                        </p>

                                        <div class="d-flex justify-content-center mb-3">
                                            <canvas id="twwdaSignatureCanvas" class="twwda-signature-canvas" width="400" height="160"></canvas>
                                        </div>

                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-outline-secondary" id="twwdaClearSigBtn" style="min-height:44px;">
                                                <i class="mdi mdi-eraser me-1"></i> Clear
                                            </button>
                                            <button type="button" class="btn twwda-btn-green btn-lg px-4" id="twwdaSubmitSigBtn"
                                                    data-declaration-id="{{ $myDeclaration->id }}">
                                                <i class="mdi mdi-check-decagram me-1"></i> Sign Declaration
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @else
                {{-- ── STATE D: Declaration fully complete ── --}}
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-9">
                        <div class="card twwda-card twwda-conflict-success">
                            <div class="card-body py-4">
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                         style="width: 56px; height: 56px; background: var(--twwda-green-soft);">
                                        <i class="mdi mdi-check-decagram text-success" style="font-size: 32px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fw-bold text-success mb-0">Declaration Complete</h5>
                                        <span class="text-muted small">
                                            @if($myDeclaration->has_conflict)
                                                <span class="badge bg-danger">Conflict Declared</span>
                                            @else
                                                <span class="badge bg-success">No Conflict</span>
                                            @endif
                                            &middot; Signed {{ Carbon::parse($myDeclaration->signed_at)->format('d M Y, g:i A') }}
                                        </span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#twwdaDeclDetails"
                                            aria-expanded="false" style="min-height:44px;">
                                        <i class="mdi mdi-eye-outline me-1"></i> View Details
                                    </button>
                                </div>

                                <div class="collapse mt-3" id="twwdaDeclDetails">
                                    <div class="twwda-bg-brand-soft rounded-3 p-3">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <span class="text-muted small">Conflict Status</span><br>
                                                @if($myDeclaration->has_conflict)
                                                    <span class="badge bg-danger">Has Conflict</span>
                                                @else
                                                    <span class="badge bg-success">No Conflict</span>
                                                @endif
                                            </div>
                                            @if($myDeclaration->has_conflict)
                                                <div class="col-sm-6">
                                                    <span class="text-muted small">Person Name</span><br>
                                                    <strong>{{ $myDeclaration->person_name ?? '---' }}</strong>
                                                </div>
                                                @if($myDeclaration->person_address)
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small">Person Address</span><br>
                                                        <strong>{{ $myDeclaration->person_address }}</strong>
                                                    </div>
                                                @endif
                                                <div class="col-sm-6">
                                                    <span class="text-muted small">Nature of Interest</span><br>
                                                    <strong>{{ $myDeclaration->interest_nature ?? '---' }}</strong>
                                                </div>
                                                @if($myDeclaration->interest_value)
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small">Value of Interest</span><br>
                                                        <strong>KES {{ number_format($myDeclaration->interest_value, 2) }}</strong>
                                                    </div>
                                                @endif
                                                @if($myDeclaration->interest_ceased_on)
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small">Interest Ceased On</span><br>
                                                        <strong>{{ Carbon::parse($myDeclaration->interest_ceased_on)->format('d M Y') }}</strong>
                                                    </div>
                                                @endif
                                                @if($myDeclaration->remarks)
                                                    <div class="col-12">
                                                        <span class="text-muted small">Remarks</span><br>
                                                        <strong>{{ $myDeclaration->remarks }}</strong>
                                                    </div>
                                                @endif
                                            @endif
                                            <div class="col-sm-6">
                                                <span class="text-muted small">Verified At</span><br>
                                                <strong>{{ Carbon::parse($myDeclaration->otp_verified_at)->format('d M Y, g:i A') }}</strong>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="text-muted small">Signed At</span><br>
                                                <strong>{{ Carbon::parse($myDeclaration->signed_at)->format('d M Y, g:i A') }}</strong>
                                            </div>
                                            @if($myDeclaration->signature)
                                                <div class="col-12">
                                                    <span class="text-muted small">Signature</span><br>
                                                    <img src="{{ $myDeclaration->signature }}" alt="Signature"
                                                         style="max-width: 200px; height: auto;" class="mt-1 border rounded p-1 bg-white">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            {{-- ╔══════════════════════════════════════════════════════════════╗
                 ║  3. MEETING AGENDA & SIDEBAR (Two-column layout)            ║
                 ║     Only visible AFTER conflict declaration is complete      ║
                 ╚══════════════════════════════════════════════════════════════╝ --}}
            @if($myDeclaration && $myDeclaration->isComplete())
            <div class="row g-4">

                {{-- ════════ LEFT COLUMN: Agenda Items ════════ --}}
                <div class="col-lg-8">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="fw-bold mb-0 twwda-text-brand">
                            <i class="mdi mdi-format-list-numbered me-1"></i> Meeting Agenda
                        </h4>
                        <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size:.85rem;">
                            {{ $agendaItems->count() }} {{ Str::plural('Item', $agendaItems->count()) }}
                        </span>
                    </div>

                    @if($agendaItems->isEmpty())
                        <div class="card twwda-card">
                            <div class="card-body text-center py-5">
                                <i class="mdi mdi-format-list-checks text-muted" style="font-size: 56px;"></i>
                                <h5 class="text-muted mt-3">No agenda items available</h5>
                                <p class="text-muted mb-0">Agenda items will appear here once added by the Corporation Secretary.</p>
                            </div>
                        </div>
                    @else
                        <div class="accordion twwda-agenda-accordion" id="twwdaAgendaAccordion">
                            @foreach($agendaItems as $index => $item)
                                @php
                                    $sourceBadgeClass = match($item->source) {
                                        'notice'     => 'twwda-source-badge-notice',
                                        'cs_added'   => 'twwda-source-badge-cs_added',
                                        'ddia_added'  => 'twwda-source-badge-ddia_added',
                                        default      => 'bg-light text-dark',
                                    };
                                    $sourceLabel = match($item->source) {
                                        'notice'     => 'Notice',
                                        'cs_added'   => 'CS Added',
                                        'ddia_added'  => 'DDIA Added',
                                        default      => $item->source ?? '',
                                    };
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="twwdaAgendaHead{{ $item->id }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#twwdaAgendaBody{{ $item->id }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                            <span class="twwda-item-number me-3">{{ $item->item_number }}</span>
                                            <span class="flex-grow-1">{{ $item->title }}</span>
                                            @if($item->source)
                                                <span class="badge {{ $sourceBadgeClass }} ms-2 me-2" style="font-size:.75rem;">
                                                    {{ $sourceLabel }}
                                                </span>
                                            @endif
                                            @if($item->status)
                                                <span class="badge bg-primary-subtle text-primary ms-1 me-2" style="font-size:.72rem;">
                                                    {{ $item->status }}
                                                </span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="twwdaAgendaBody{{ $item->id }}"
                                         class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                         aria-labelledby="twwdaAgendaHead{{ $item->id }}"
                                         data-bs-parent="#twwdaAgendaAccordion">
                                        <div class="accordion-body">

                                            {{-- Description --}}
                                            @if($item->description)
                                                <div class="text-muted mb-3" style="line-height:1.7;">
                                                    {!! nl2br(e($item->description)) !!}
                                                </div>
                                            @endif

                                            {{-- Board Paper --}}
                                            @if($item->boardPaper)
                                                @php $paper = $item->boardPaper; @endphp
                                                <div class="card border rounded-3 mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex flex-wrap align-items-start gap-2 mb-2">
                                                            <i class="mdi mdi-file-document-outline twwda-text-brand" style="font-size:1.5rem;"></i>
                                                            <div class="flex-grow-1">
                                                                @if($paper->paper_number)
                                                                    <span class="badge text-white me-1" style="background:var(--twwda-brand);">
                                                                        {{ $paper->paper_number }}
                                                                    </span>
                                                                @endif
                                                                <strong>{{ $paper->title }}</strong>
                                                                @if(optional($paper->author)->name)
                                                                    <div class="small text-muted mt-1">
                                                                        <i class="mdi mdi-account-outline"></i>
                                                                        Author: {{ $paper->author->name }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- Rendered content preview --}}
                                                        @if($paper->rendered_content)
                                                            <div class="border rounded p-3 bg-light mb-3"
                                                                 style="max-height: 250px; overflow-y: auto; font-size:.92rem; line-height:1.7;">
                                                                {!! $paper->rendered_content !!}
                                                            </div>
                                                        @endif

                                                        <a href="{{ route('board.portal.paper.show', $paper) }}"
                                                           class="btn twwda-btn-brand btn-sm" style="min-height:44px;">
                                                            <i class="mdi mdi-book-open-page-variant-outline me-1"></i> View Full Paper
                                                        </a>

                                                        {{-- Annexures --}}
                                                        @if($paper->annexures && $paper->annexures->count())
                                                            <div class="mt-3">
                                                                <h6 class="fw-semibold mb-2">
                                                                    <i class="mdi mdi-paperclip twwda-text-brand"></i>
                                                                    Annexures ({{ $paper->annexures->count() }})
                                                                </h6>
                                                                <div class="d-flex flex-column gap-2">
                                                                    @foreach($paper->annexures as $annexure)
                                                                        <div class="d-flex align-items-center justify-content-between bg-light rounded-3 px-3 py-2">
                                                                            <span class="small">
                                                                                <i class="mdi mdi-file-outline text-muted me-1"></i>
                                                                                {{ $annexure->title ?? $annexure->file_name ?? 'Annexure' }}
                                                                            </span>
                                                                            @if($annexure->file_path)
                                                                                <a href="{{ asset('storage/' . $annexure->file_path) }}"
                                                                                   target="_blank"
                                                                                   class="btn btn-sm btn-outline-primary" style="min-height:40px;">
                                                                                    <i class="mdi mdi-open-in-new me-1"></i> View
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- My Sticky Notes for this paper --}}
                                                        @if(isset($myPaperNotes[$paper->id]) && $myPaperNotes[$paper->id]->count())
                                                            <div class="mt-3">
                                                                <h6 class="fw-semibold mb-2">
                                                                    <i class="mdi mdi-note-text-outline twwda-text-brand"></i>
                                                                    My Notes ({{ $myPaperNotes[$paper->id]->count() }})
                                                                </h6>
                                                                @foreach($myPaperNotes[$paper->id] as $note)
                                                                    @php
                                                                        $colorClass = match($note->color ?? 'yellow') {
                                                                            'blue'  => 'twwda-sticky-blue',
                                                                            'green' => 'twwda-sticky-green',
                                                                            'pink'  => 'twwda-sticky-pink',
                                                                            default => 'twwda-sticky-yellow',
                                                                        };
                                                                    @endphp
                                                                    <div class="twwda-sticky-note {{ $colorClass }}">
                                                                        {{ $note->content }}
                                                                        <div class="text-end text-muted" style="font-size: .7rem;">
                                                                            {{ Carbon::parse($note->created_at)->format('g:i A') }}
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                @if(!$item->description)
                                                    <p class="text-muted mb-0 fst-italic">
                                                        <i class="mdi mdi-information-outline me-1"></i>
                                                        No additional information available for this agenda item.
                                                    </p>
                                                @endif
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ════════ RIGHT COLUMN: Meeting Info Sidebar ════════ --}}
                <div class="col-lg-4">
                    <div class="twwda-sidebar-sticky">

                        {{-- ── Attendance Register ── --}}
                        <div class="card twwda-card mb-3">
                            <div class="card-header twwda-bg-brand-soft d-flex justify-content-between align-items-center py-3">
                                <h6 class="mb-0 fw-bold twwda-text-brand">
                                    <i class="mdi mdi-account-check-outline me-1"></i> Attendance
                                </h6>
                                <span class="badge bg-success rounded-pill px-2 py-1">
                                    {{ $attendance->count() }} Present
                                </span>
                            </div>
                            <div class="card-body" style="max-height: 320px; overflow-y: auto;">
                                @if($attendance->isEmpty())
                                    <div class="text-center py-3">
                                        <i class="mdi mdi-account-off-outline text-muted" style="font-size: 36px;"></i>
                                        <p class="text-muted small mt-1 mb-0">No members joined yet</p>
                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($attendance as $record)
                                            @php
                                                $attendeeName = optional($record->boardMember)->names
                                                    ?? optional($record->user)->name
                                                    ?? $record->display_name
                                                    ?? '---';
                                                $attendeePhoto = optional($record->boardMember)->photo;
                                                $attendeeDesig = optional($record->boardMember)->designation ?? '';
                                                $initials = collect(explode(' ', $attendeeName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                                            @endphp
                                            <div class="d-flex align-items-center gap-2">
                                                @if($attendeePhoto)
                                                    <img src="{{ asset('storage/' . $attendeePhoto) }}" alt="{{ $attendeeName }}"
                                                         class="twwda-member-avatar">
                                                @else
                                                    <span class="twwda-member-initials">{{ $initials }}</span>
                                                @endif
                                                <div style="min-width:0;">
                                                    <div class="fw-semibold text-truncate" style="font-size:.9rem;">{{ $attendeeName }}</div>
                                                    @if($attendeeDesig)
                                                        <div class="text-muted text-truncate" style="font-size:.75rem;">{{ $attendeeDesig }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ── Conflict Register ── --}}
                        @if($conflicts->count())
                            <div class="card twwda-card mb-3">
                                <div class="card-header twwda-bg-brand-soft py-3">
                                    <h6 class="mb-0 fw-bold twwda-text-brand">
                                        <i class="mdi mdi-shield-check-outline me-1"></i> Conflict Register
                                    </h6>
                                </div>
                                <div class="card-body" style="max-height: 280px; overflow-y: auto;">
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($conflicts as $declaration)
                                            @if($declaration->otp_verified_at && $declaration->signed_at)
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="fw-semibold" style="font-size:.9rem;">
                                                        {{ optional($declaration->boardMember)->names ?? '---' }}
                                                    </span>
                                                    @if($declaration->has_conflict)
                                                        <span class="badge bg-danger" style="font-size:.72rem;">Conflict Declared</span>
                                                    @else
                                                        <span class="badge bg-success" style="font-size:.72rem;">No Conflict</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    @if($conflicts->filter(fn($d) => $d->otp_verified_at && $d->signed_at)->isEmpty())
                                        <p class="text-muted small text-center mb-0">
                                            No completed declarations yet.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- ── Meeting Details ── --}}
                        <div class="card twwda-card mb-3">
                            <div class="card-header twwda-bg-brand-soft py-3">
                                <h6 class="mb-0 fw-bold twwda-text-brand">
                                    <i class="mdi mdi-information-outline me-1"></i> Meeting Details
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0" style="font-size:.9rem;">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" style="width:40%;"><i class="mdi mdi-account-group-outline me-1"></i>Committee</td>
                                            <td class="fw-semibold">{{ optional($meeting->boardCommittee)->name ?? 'Board Meeting' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><i class="mdi mdi-calendar me-1"></i>Date</td>
                                            <td class="fw-semibold">{{ Carbon::parse($meeting->meeting_date)->isoFormat('D MMM YYYY') }}</td>
                                        </tr>
                                        @if($meeting->meeting_time)
                                            <tr>
                                                <td class="text-muted"><i class="mdi mdi-clock-outline me-1"></i>Time</td>
                                                <td class="fw-semibold">{{ Carbon::parse($meeting->meeting_time)->format('g:i A') }}</td>
                                            </tr>
                                        @endif
                                        @if($meeting->venue)
                                            <tr>
                                                <td class="text-muted"><i class="mdi mdi-map-marker-outline me-1"></i>Venue</td>
                                                <td class="fw-semibold">{{ $meeting->venue }}</td>
                                            </tr>
                                        @endif
                                        @if($meeting->current_stage)
                                            <tr>
                                                <td class="text-muted"><i class="mdi mdi-progress-check me-1"></i>Stage</td>
                                                <td><span class="badge bg-primary-subtle text-primary">Stage {{ $meeting->current_stage }}</span></td>
                                            </tr>
                                        @endif
                                        @if($meeting->started_at)
                                            <tr>
                                                <td class="text-muted"><i class="mdi mdi-clock-start me-1"></i>Started At</td>
                                                <td class="fw-semibold">{{ Carbon::parse($meeting->started_at)->format('g:i A') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>{{-- /.row --}}
            @else
                {{-- Declaration not complete — block access to meeting content --}}
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="card twwda-card">
                            <div class="card-body text-center py-5">
                                <i class="mdi mdi-shield-lock-outline text-warning" style="font-size: 64px;"></i>
                                <h4 class="fw-bold mt-3">Complete Your Declaration First</h4>
                                <p class="text-muted mb-0" style="max-width:480px; margin:0 auto;">
                                    You must submit your conflict of interest declaration, verify it with the OTP sent to your email,
                                    and sign it before you can view the meeting agenda, board papers, and other meeting materials.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // ══════════════════════════════════════════════════════════════
    //  CONFLICT FORM — Toggle fields based on radio
    // ══════════════════════════════════════════════════════════════
    function toggleConflictFields() {
        var val = $('input[name="has_conflict"]:checked').val();
        if (val === '1') {
            $('#twwdaConflictFields').slideDown(250);
            $('#twwdaNoConflictInfo').slideUp(250);
        } else {
            $('#twwdaConflictFields').slideUp(250);
            $('#twwdaNoConflictInfo').slideDown(250);
        }
    }
    $('input[name="has_conflict"]').on('change', toggleConflictFields);
    // Initialize on page load
    toggleConflictFields();

    // ══════════════════════════════════════════════════════════════
    //  CONFLICT FORM — AJAX Submit
    // ══════════════════════════════════════════════════════════════
    $('#twwdaConflictForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#twwdaSubmitConflictBtn');
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Submitting...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: $(this).serialize(),
            success: function(res) {
                toastr.success(res.message || 'Declaration submitted successfully');
                setTimeout(function() { location.reload(); }, 1200);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-send-outline me-1"></i> Submit Declaration');
                var msg = xhr.responseJSON?.message || 'Failed to submit declaration';
                if (xhr.responseJSON?.errors) {
                    var errs = Object.values(xhr.responseJSON.errors).flat();
                    msg = errs.join('<br>');
                }
                toastr.error(msg);
            }
        });
    });

    // ══════════════════════════════════════════════════════════════
    //  OTP — Input filter (numeric only)
    // ══════════════════════════════════════════════════════════════
    $('#twwdaOtpInput').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // ══════════════════════════════════════════════════════════════
    //  OTP — Form Submit
    // ══════════════════════════════════════════════════════════════
    $('#twwdaOtpForm').on('submit', function(e) {
        e.preventDefault();
        var otpCode = $('#twwdaOtpInput').val().trim();
        if (otpCode.length !== 6) {
            toastr.warning('Please enter a valid 6-digit OTP');
            return;
        }
        var $btn = $('#twwdaVerifyOtpBtn');
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Verifying...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: $(this).serialize(),
            success: function(res) {
                toastr.success(res.message || 'OTP verified successfully');
                setTimeout(function() { location.reload(); }, 800);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-check-circle-outline me-1"></i> Verify OTP');
                toastr.error(xhr.responseJSON?.message || 'Invalid OTP. Please try again.');
            }
        });
    });

    // ══════════════════════════════════════════════════════════════
    //  OTP — Resend with cooldown
    // ══════════════════════════════════════════════════════════════
    var resendCooldown = 0;
    $('#twwdaResendOtpBtn').on('click', function() {
        if (resendCooldown > 0) return;
        var $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("workflow.meeting.conflict.resend", $meeting) }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { declaration_id: '{{ optional($myDeclaration)->id }}' },
            success: function(res) {
                toastr.success(res.message || 'OTP resent to your email');
                startResendCooldown(60);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to resend OTP');
                $btn.prop('disabled', false);
            }
        });
    });

    function startResendCooldown(seconds) {
        resendCooldown = seconds;
        var $btn = $('#twwdaResendOtpBtn');
        var $timer = $('#twwdaResendTimer');
        $btn.prop('disabled', true);
        $timer.show();
        var interval = setInterval(function() {
            resendCooldown--;
            $timer.text('(' + resendCooldown + 's)');
            if (resendCooldown <= 0) {
                clearInterval(interval);
                $btn.prop('disabled', false);
                $timer.hide();
            }
        }, 1000);
    }

    // ══════════════════════════════════════════════════════════════
    //  SIGNATURE CANVAS — Mouse + Touch
    // ══════════════════════════════════════════════════════════════
    var canvas = document.getElementById('twwdaSignatureCanvas');
    if (canvas) {
        var ctx = canvas.getContext('2d');
        var drawing = false;
        var lastX = 0, lastY = 0;

        function getCanvasPos(e) {
            var rect = canvas.getBoundingClientRect();
            var scaleX = canvas.width / rect.width;
            var scaleY = canvas.height / rect.height;
            if (e.touches && e.touches.length) {
                return {
                    x: (e.touches[0].clientX - rect.left) * scaleX,
                    y: (e.touches[0].clientY - rect.top) * scaleY
                };
            }
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }

        function startDraw(e) {
            e.preventDefault();
            drawing = true;
            var pos = getCanvasPos(e);
            lastX = pos.x;
            lastY = pos.y;
        }

        function continueDraw(e) {
            if (!drawing) return;
            e.preventDefault();
            var pos = getCanvasPos(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.strokeStyle = '#1a1a1a';
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();
            lastX = pos.x;
            lastY = pos.y;
        }

        function stopDraw(e) {
            if (e) e.preventDefault();
            drawing = false;
        }

        // Mouse events
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', continueDraw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);

        // Touch events (iPad)
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', continueDraw, { passive: false });
        canvas.addEventListener('touchend', stopDraw, { passive: false });
        canvas.addEventListener('touchcancel', stopDraw, { passive: false });

        // Clear signature
        $('#twwdaClearSigBtn').on('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // Submit signature
        $('#twwdaSubmitSigBtn').on('click', function() {
            var declarationId = $(this).data('declaration-id');
            var signatureData = canvas.toDataURL('image/png');

            // Check if canvas is blank
            var blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            if (canvas.toDataURL() === blank.toDataURL()) {
                toastr.warning('Please draw your signature before submitting');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Signing...');

            $.ajax({
                url: '{{ route("workflow.meeting.conflict.sign", $meeting) }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    declaration_id: declarationId,
                    signature: signatureData
                },
                success: function(res) {
                    toastr.success(res.message || 'Declaration signed successfully');
                    setTimeout(function() { location.reload(); }, 800);
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-check-decagram me-1"></i> Sign Declaration');
                    toastr.error(xhr.responseJSON?.message || 'Failed to save signature');
                }
            });
        });
    }

    // ══════════════════════════════════════════════════════════════
    //  ACCORDION — Auto-scroll into view on expand
    // ══════════════════════════════════════════════════════════════
    $('.twwda-agenda-accordion').on('shown.bs.collapse', function(e) {
        var $target = $(e.target);
        var $header = $target.prev('.accordion-header');
        if ($header.length) {
            $('html, body').animate({
                scrollTop: $header.offset().top - 100
            }, 300);
        }
    });

});
</script>
@endpush
