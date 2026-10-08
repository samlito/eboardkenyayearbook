@extends('admin_dashboard')

@section('admin')
<div class="content">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <a href="{{ route('workflow.meeting.live', $meeting) }}" class="btn btn-sm btn-light rounded-pill mb-1">
                <i class="mdi mdi-arrow-left"></i> Back to Live Meeting
            </a>
            <h3 class="mb-0"><i class="mdi mdi-shield-check-outline text-primary me-1"></i> Declaration of Conflict of Interest</h3>
            <p class="text-muted mb-0">{{ $meeting->title }} &mdash; {{ $boardMember->names ?? 'Select Board Member' }}</p>
        </div>
    </div>

    {{-- Board Member Selector --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-0"><i class="mdi mdi-account-outline me-1"></i> Board Member</label>
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="boardMemberSelector">
                        <option value="">-- Select Board Member --</option>
                        @foreach($boardMembers as $bm)
                            <option value="{{ $bm->id }}" {{ ($boardMember && $boardMember->id == $bm->id) ? 'selected' : '' }}>
                                {{ $bm->names }} — {{ $bm->designation ?? 'Member' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    @if($boardMember)
                        <small class="text-muted"><i class="mdi mdi-email-outline me-1"></i>{{ $boardMember->email }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$boardMember)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="mdi mdi-account-search-outline text-muted" style="font-size: 48px; opacity: 0.4;"></i>
                <h5 class="mt-3 text-muted">Select a Board Member</h5>
                <p class="text-muted mb-0">Please select a board member from the dropdown above to proceed with the conflict of interest declaration.</p>
            </div>
        </div>
    @else

    {{-- Step Wizard --}}
    @php
        $currentStep = 1;
        if($declaration && !$declaration->otp_verified_at) $currentStep = 2;           // Declaration submitted, awaiting OTP
        if($declaration && $declaration->otp_verified_at && !$declaration->signed_at) $currentStep = 3;  // OTP verified, awaiting signature
        if($declaration && $declaration->otp_verified_at && $declaration->signed_at) $currentStep = 4;   // Fully complete
    @endphp

    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between position-relative">
                {{-- Progress line --}}
                <div style="position: absolute; top: 20px; left: 10%; right: 10%; height: 3px; background: #e9ecef; z-index: 0;">
                    <div style="height: 100%; background: linear-gradient(90deg, {{ setting('primary_color', '#1a1a1a') }}, #099139); width: {{ min(100, (($currentStep - 1) / 2) * 100) }}%; transition: width 0.5s;"></div>
                </div>

                @php
                    $steps = [
                        1 => ['label' => 'Declare', 'icon' => 'mdi-pencil-outline'],
                        2 => ['label' => 'Verify OTP', 'icon' => 'mdi-shield-key-outline'],
                        3 => ['label' => 'Sign', 'icon' => 'mdi-draw'],
                    ];
                @endphp
                @foreach($steps as $num => $step)
                    @php
                        $isComplete = $num < $currentStep;
                        $isCurrent = $num == $currentStep;
                    @endphp
                    <div class="text-center position-relative" style="z-index: 1; flex: 1;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-1
                            @if($isComplete) bg-success text-white
                            @elseif($isCurrent) bg-primary text-white shadow
                            @else bg-light text-muted border
                            @endif"
                            style="width: 40px; height: 40px; font-size: 18px;">
                            @if($isComplete)
                                <i class="mdi mdi-check"></i>
                            @else
                                <i class="mdi {{ $step['icon'] }}"></i>
                            @endif
                        </div>
                        <div style="font-size: 12px; font-weight: {{ $isCurrent ? '600' : '400' }}; color: {{ $isCurrent ? setting('primary_color', '#1a1a1a') : ($isComplete ? '#099139' : '#adb5bd') }};">
                            Step {{ $num }}: {{ $step['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Step 1: Declaration Form --}}
            @if($currentStep === 1)
                <div class="card" id="step1">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="mdi mdi-pencil-outline text-primary me-1"></i> Step 1: Declare Conflict of Interest</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('workflow.meeting.conflict.submit', $meeting) }}" method="POST" id="conflictForm">
                            @csrf
                            <input type="hidden" name="board_member_id" value="{{ $boardMember->id ?? '' }}">

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Do you have a conflict of interest in any of the agenda items for this meeting? <span class="text-danger">*</span></label>
                                <div class="d-flex gap-4 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="has_conflict" id="conflict_yes" value="1"
                                               {{ old('has_conflict', $declaration->has_conflict ?? '') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="conflict_yes">
                                            <span class="text-danger">Yes</span>, I have a conflict of interest
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="has_conflict" id="conflict_no" value="0"
                                               {{ old('has_conflict', $declaration->has_conflict ?? '') === '0' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="conflict_no">
                                            <span class="text-success">No</span>, I do not have a conflict
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Conflict Details (shown only when Yes) --}}
                            <div id="conflictDetails" style="display: none;">
                                <div class="alert alert-warning" style="font-size: 13px;">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    Please provide details of the conflict of interest as required under the {{ setting('institution_acronym', 'ORG') }} governance framework.
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Person Name <span class="text-danger">*</span></label>
                                        <input type="text" name="person_name" class="form-control"
                                               value="{{ old('person_name', $declaration->person_name ?? '') }}"
                                               placeholder="Full name of interested person">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Person Address</label>
                                        <input type="text" name="person_address" class="form-control"
                                               value="{{ old('person_address', $declaration->person_address ?? '') }}"
                                               placeholder="Address of the person">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Nature of Interest <span class="text-danger">*</span></label>
                                        <textarea name="interest_nature" class="form-control" rows="3"
                                                  placeholder="Describe the nature of your interest in the agenda item(s)...">{{ old('interest_nature', $declaration->interest_nature ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Value (KES)</label>
                                        <input type="text" name="interest_value" class="form-control"
                                               value="{{ old('interest_value', $declaration->interest_value ?? '') }}"
                                               placeholder="e.g. 500,000">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Ceased On</label>
                                        <input type="date" name="interest_ceased_on" class="form-control"
                                               value="{{ old('interest_ceased_on', $declaration->interest_ceased_on ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Remarks</label>
                                        <input type="text" name="remarks" class="form-control"
                                               value="{{ old('remarks', $declaration->remarks ?? '') }}"
                                               placeholder="Additional remarks">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('workflow.meeting.live', $meeting) }}" class="btn btn-light">
                                    <i class="mdi mdi-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4" id="submitDeclaration">
                                    <i class="mdi mdi-arrow-right me-1"></i> Submit Declaration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Step 2: OTP Verification --}}
            @if($currentStep === 2)
                <div class="card" id="step2">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="mdi mdi-shield-key-outline text-primary me-1"></i> Step 2: Verify with OTP</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="bg-soft-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="mdi mdi-email-check-outline text-primary" style="font-size: 36px;"></i>
                            </div>
                            <h5>An OTP has been sent to your email</h5>
                            <p class="text-muted">
                                Please check your inbox for the 6-digit verification code.
                                <br>A code was sent to <strong>{{ $boardMember->email ?? 'your registered email' }}</strong>.
                            </p>
                        </div>

                        <form action="{{ route('workflow.meeting.conflict.verify', $meeting) }}" method="POST" class="text-center">
                            @csrf
                            <input type="hidden" name="declaration_id" value="{{ $declaration->id ?? '' }}">

                            @if($errors->has('otp'))
                                <div class="alert alert-danger" style="font-size: 13px;">
                                    {{ $errors->first('otp') }}
                                </div>
                            @endif

                            <div class="d-flex justify-content-center gap-2 mb-3" id="otpInputs">
                                @for($i = 0; $i < 6; $i++)
                                    <input type="text" class="form-control text-center fw-bold otp-digit"
                                           style="width: 50px; height: 56px; font-size: 24px; border-radius: 8px;"
                                           maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                           data-index="{{ $i }}">
                                @endfor
                            </div>
                            <input type="hidden" name="otp_code" id="otpHidden">

                            <button type="submit" class="btn btn-primary px-5 mb-3" id="verifyOtpBtn">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Verify OTP
                            </button>

                            <div>
                                <small class="text-muted">Didn't receive the code?</small>
                                <br>
                                <a href="#" id="resendOtp" class="text-primary fw-semibold" style="font-size: 13px;">
                                    <i class="mdi mdi-refresh me-1"></i> Resend OTP
                                </a>
                                <span id="resendTimer" class="text-muted ms-1" style="font-size: 12px; display: none;"></span>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Step 3: Signature --}}
            @if($currentStep === 3)
                <div class="card" id="step3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="mdi mdi-draw text-primary me-1"></i> Step 3: Sign Declaration</h5>
                    </div>
                    <div class="card-body">
                        {{-- Declaration Summary --}}
                        <div class="border rounded p-3 bg-light mb-4">
                            <h6 class="mb-2">Declaration Summary</h6>
                            <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                                <tr>
                                    <td class="text-muted" width="40%">Member:</td>
                                    <td class="fw-semibold">{{ $boardMember->names ??'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Meeting:</td>
                                    <td>{{ $meeting->title }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Has Conflict:</td>
                                    <td>
                                        @if($declaration && $declaration->has_conflict)
                                            <span class="badge bg-danger">Yes</span>
                                        @else
                                            <span class="badge bg-success">No</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($declaration && $declaration->has_conflict)
                                    <tr>
                                        <td class="text-muted">Nature:</td>
                                        <td>{{ $declaration->interest_nature ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">OTP Verified:</td>
                                    <td><span class="badge bg-success"><i class="mdi mdi-check me-1"></i>Verified</span></td>
                                </tr>
                            </table>
                        </div>

                        <form action="{{ route('workflow.meeting.conflict.sign', $meeting) }}" method="POST" id="signForm">
                            @csrf
                            <input type="hidden" name="declaration_id" value="{{ $declaration->id ?? '' }}">

                            @include('workflow.components.signature-pad', ['inputName' => 'signature', 'label' => 'Sign Declaration'])

                            <hr>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('workflow.meeting.live', $meeting) }}" class="btn btn-light">
                                    <i class="mdi mdi-arrow-left me-1"></i> Back
                                </a>
                                <button type="submit" class="btn btn-success px-4" id="signBtn">
                                    <i class="mdi mdi-check-decagram me-1"></i> Sign & Complete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Completed State --}}
            @if($currentStep === 4)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="bg-soft-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-check-decagram text-success" style="font-size: 42px;"></i>
                        </div>
                        <h4 class="text-success">Declaration Complete</h4>
                        <p class="text-muted mb-3">Your conflict of interest declaration has been successfully submitted, verified, and signed.</p>
                        @if($declaration && $declaration->signature)
                            <div class="border rounded p-3 d-inline-block bg-light mb-3">
                                <small class="text-muted d-block mb-1">Your Signature</small>
                                <img src="{{ $declaration->signature }}" alt="Signature" style="max-height: 60px;">
                            </div>
                        @endif
                        <br>
                        <a href="{{ route('workflow.meeting.live', $meeting) }}" class="btn btn-primary">
                            <i class="mdi mdi-arrow-left me-1"></i> Return to Meeting
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @endif {{-- end @if($boardMember) --}}
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Board member selector - reload page with selected member
    $('#boardMemberSelector').on('change', function() {
        var memberId = $(this).val();
        if (memberId) {
            window.location.href = '{{ route("workflow.meeting.conflict", $meeting) }}?board_member_id=' + memberId;
        }
    });

    // Toggle conflict details
    $('input[name="has_conflict"]').on('change', function() {
        if ($(this).val() === '1') {
            $('#conflictDetails').slideDown(200);
        } else {
            $('#conflictDetails').slideUp(200);
        }
    });
    // Init state
    if ($('input[name="has_conflict"]:checked').val() === '1') {
        $('#conflictDetails').show();
    }

    // OTP digit inputs auto-focus
    $('.otp-digit').on('input', function() {
        var val = $(this).val();
        if (val.length === 1) {
            var next = $(this).data('index') + 1;
            if (next < 6) {
                $('.otp-digit[data-index="' + next + '"]').focus();
            }
        }
        updateOtpHidden();
    });

    $('.otp-digit').on('keydown', function(e) {
        if (e.key === 'Backspace' && $(this).val() === '') {
            var prev = $(this).data('index') - 1;
            if (prev >= 0) {
                $('.otp-digit[data-index="' + prev + '"]').focus();
            }
        }
    });

    // Paste support for OTP
    $('.otp-digit').first().on('paste', function(e) {
        e.preventDefault();
        var pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').substring(0, 6);
        for (var i = 0; i < pasted.length; i++) {
            $('.otp-digit[data-index="' + i + '"]').val(pasted[i]);
        }
        updateOtpHidden();
    });

    function updateOtpHidden() {
        var otp = '';
        $('.otp-digit').each(function() { otp += $(this).val(); });
        $('#otpHidden').val(otp);
    }

    // Resend OTP
    var resendCooldown = 0;
    $('#resendOtp').on('click', function(e) {
        e.preventDefault();
        if (resendCooldown > 0) return;

        $.ajax({
            url: '{{ route("workflow.meeting.conflict.resend", $meeting) }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { declaration_id: '{{ $declaration->id ?? "" }}' },
            success: function(response) {
                toastr.success(response.message || 'OTP resent to your email.');
                startCooldown(60);
            },
            error: function(xhr) {
                var msg = 'Failed to resend OTP.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            }
        });
    });

    function startCooldown(seconds) {
        resendCooldown = seconds;
        $('#resendOtp').addClass('text-muted').css('pointer-events', 'none');
        $('#resendTimer').show();
        var interval = setInterval(function() {
            resendCooldown--;
            $('#resendTimer').text('(' + resendCooldown + 's)');
            if (resendCooldown <= 0) {
                clearInterval(interval);
                $('#resendOtp').removeClass('text-muted').css('pointer-events', 'auto');
                $('#resendTimer').hide();
            }
        }, 1000);
    }

    // Sign form validation
    $('#signForm').on('submit', function(e) {
        var sig = $('input[name="signature"]').val();
        if (!sig) {
            e.preventDefault();
            Swal.fire({
                title: 'Signature Required',
                text: 'Please sign in the signature pad before submitting.',
                icon: 'warning',
                confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}'
            });
        }
    });
});
</script>
@endpush
