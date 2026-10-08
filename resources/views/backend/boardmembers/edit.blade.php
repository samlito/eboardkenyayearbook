@extends('admin_dashboard')
@section('admin')

<style>
    .bm-wizard-header {
        background: linear-gradient(135deg, #2b2b2b 0%, #212121 100%);
        border-radius: 12px;
        padding: 1.5rem 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
    }
    .bm-wizard-header h4 { margin: 0; font-weight: 600; }
    .bm-wizard-header p { margin: 0.25rem 0 0; opacity: 0.8; font-size: 0.9rem; }

    .bm-step-nav {
        display: flex;
        gap: 0;
        margin-bottom: 2rem;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 6px;
        overflow-x: auto;
    }
    .bm-step-btn {
        flex: 1;
        min-width: 140px;
        padding: 10px 16px;
        border: none;
        background: transparent;
        color: #6c757d;
        font-weight: 500;
        font-size: 0.85rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.25s;
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        white-space: nowrap;
    }
    .bm-step-btn.active {
        background: #fff;
        color: #181818;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        font-weight: 600;
    }
    .bm-step-btn .step-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #dee2e6;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .bm-step-btn.active .step-num { background: #2b2b2b; color: #fff; }

    .bm-step-panel { display: none; animation: bmFadeIn 0.3s ease; }
    .bm-step-panel.active { display: block; }
    @keyframes bmFadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

    .bm-section-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .bm-section-title {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #181818;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bm-section-title i { font-size: 1.1rem; opacity: 0.7; }

    .bm-label {
        font-weight: 500;
        font-size: 0.85rem;
        color: #495057;
        margin-bottom: 4px;
    }
    .bm-input {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.55rem 0.85rem;
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .bm-input:focus { border-color: #9B0C23; box-shadow: 0 0 0 3px rgba(24,24,24,0.1); }

    .bm-upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 1.25rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s;
        background: #fbfbfb;
        position: relative;
    }
    .bm-upload-zone:hover { border-color: #9B0C23; background: #f8f8f8; }
    .bm-upload-zone.has-file { border-color: #198754; background: #f0fdf4; }
    .bm-upload-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .bm-upload-icon { font-size: 1.6rem; color: #adb5bd; margin-bottom: 0.25rem; }
    .bm-upload-zone.has-file .bm-upload-icon { color: #198754; }
    .bm-upload-label { font-size: 0.8rem; color: #6c757d; }
    .bm-upload-filename { font-size: 0.78rem; color: #198754; font-weight: 500; margin-top: 4px; }
    .bm-current-file { font-size: 0.78rem; margin-top: 6px; }
    .bm-current-file a { color: #e00214; text-decoration: none; }
    .bm-current-file a:hover { text-decoration: underline; }

    .bm-photo-edit-wrap {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    .bm-photo-current {
        width: 120px;
        height: 140px;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid #e9ecef;
        flex-shrink: 0;
    }
    .bm-photo-current img { width: 100%; height: 100%; object-fit: cover; }
    .bm-photo-new-wrap {
        width: 120px;
        height: 140px;
        border-radius: 10px;
        border: 2px dashed #dee2e6;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        position: relative;
        cursor: pointer;
    }
    .bm-photo-new-wrap:hover { border-color: #9B0C23; }
    .bm-photo-new-wrap input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .bm-photo-new-wrap img { width: 100%; height: 100%; object-fit: cover; }

    .bm-footer-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-top: 1px solid #e9ecef;
        margin-top: 1rem;
    }
    .btn-bm-primary {
        background: #2b2b2b; color: #fff; border: none; border-radius: 8px;
        padding: 0.6rem 1.5rem; font-weight: 600; font-size: 0.9rem;
    }
    .btn-bm-primary:hover { background: #111111; color: #fff; }
    .btn-bm-outline {
        background: transparent; color: #181818; border: 1px solid #9B0C23; border-radius: 8px;
        padding: 0.6rem 1.5rem; font-weight: 500; font-size: 0.9rem;
    }
    .btn-bm-outline:hover { background: #f8f8f8; }
    .btn-bm-success {
        background: #198754; color: #fff; border: none; border-radius: 8px;
        padding: 0.65rem 2rem; font-weight: 600; font-size: 0.95rem;
    }
    .btn-bm-success:hover { background: #146c43; color: #fff; }
</style>

@php
    $placeholderPhoto = asset('images/no-photo.png');
    $currentPhoto = $boardMember->photo ? asset($boardMember->photo) : $placeholderPhoto;
@endphp

<div class="content">
    <div class="container-fluid">

        {{-- Header --}}
        <div class="bm-wizard-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4><i class="mdi mdi-account-edit-outline me-2"></i>Edit Board Member</h4>
                    <p>Updating record for <strong>{{ $boardMember->names }}</strong></p>
                </div>
                <a href="{{ route('all.boardmembers') }}" class="btn btn-sm btn-light">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 10px; border-left: 4px solid #dc3545;">
                <strong><i class="mdi mdi-alert-circle me-1"></i> Please correct the following:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="boardMemberForm" method="POST" action="{{ route('boardmembers.update', $boardMember->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Step Nav --}}
            <div class="bm-step-nav">
                <button type="button" class="bm-step-btn active" data-step="1"><span class="step-num">1</span> Personal Info</button>
                <button type="button" class="bm-step-btn" data-step="2"><span class="step-num">2</span> Role & Address</button>
                <button type="button" class="bm-step-btn" data-step="3"><span class="step-num">3</span> Qualifications</button>
                <button type="button" class="bm-step-btn" data-step="4"><span class="step-num">4</span> Documents</button>
            </div>

            {{-- STEP 1: Personal --}}
            <div class="bm-step-panel active" data-step="1">
                <div class="bm-section-card">
                    <div class="bm-section-title"><i class="mdi mdi-account-outline"></i> Personal Information</div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="bm-label">Salutation <span class="text-danger">*</span></label>
                                <select name="salutation" class="form-select bm-input">
                                    <option value="">Select</option>
                                    @foreach($salutations as $salutation)
                                        <option value="{{ $salutation->id }}" {{ old('salutation', $boardMember->salutation) == $salutation->id ? 'selected' : '' }}>
                                            {{ $salutation->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('salutation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="bm-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="names" class="form-control bm-input" value="{{ old('names', $boardMember->names) }}">
                                @error('names') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="bm-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control bm-input" value="{{ old('email', $boardMember->email) }}">
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control bm-input" value="{{ old('phone', $boardMember->phone) }}">
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">National ID / Passport <span class="text-danger">*</span></label>
                                <input type="text" name="idno" class="form-control bm-input" value="{{ old('idno', $boardMember->idno) }}">
                                @error('idno') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control bm-input" value="{{ old('date_of_birth', $boardMember->date_of_birth) }}">
                                @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Role & Address --}}
            <div class="bm-step-panel" data-step="2">
                <div class="bm-section-card">
                    <div class="bm-section-title"><i class="mdi mdi-shield-account-outline"></i> Board Role</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Designation <span class="text-danger">*</span></label>
                                <select name="designation" class="form-select bm-input">
                                    <option value="">Select</option>
                                    <option value="Chairperson" {{ old('designation', $boardMember->designation) == 'Chairperson' ? 'selected' : '' }}>Chairperson</option>
                                    <option value="Vice Chairperson" {{ old('designation', $boardMember->designation) == 'Vice Chairperson' ? 'selected' : '' }}>Vice Chairperson</option>
                                    <option value="Member" {{ old('designation', $boardMember->designation) == 'Member' ? 'selected' : '' }}>Member</option>
                                </select>
                                @error('designation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Director Type</label>
                                <select name="director_type" class="form-select bm-input">
                                    <option value="">Select</option>
                                    <option value="Executive" {{ old('director_type', $boardMember->director_type) == 'Executive' ? 'selected' : '' }}>Executive</option>
                                    <option value="Non-Executive" {{ old('director_type', $boardMember->director_type) == 'Non-Executive' ? 'selected' : '' }}>Non-Executive</option>
                                    <option value="Chairperson" {{ old('director_type', $boardMember->director_type) == 'Chairperson' ? 'selected' : '' }}>Chairperson</option>
                                    <option value="Member" {{ old('director_type', $boardMember->director_type) == 'Member' ? 'selected' : '' }}>Member</option>
                                </select>
                                @error('director_type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Role Type</label>
                                <select name="role_type" class="form-select bm-input">
                                    <option value="">Select</option>
                                    <option value="independent" {{ old('role_type', $boardMember->role_type) == 'independent' ? 'selected' : '' }}>Independent</option>
                                    <option value="representative" {{ old('role_type', $boardMember->role_type) == 'representative' ? 'selected' : '' }}>Representative</option>
                                </select>
                                @error('role_type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointment_date" class="form-control bm-input" value="{{ old('appointment_date', $boardMember->appointment_date) }}">
                                @error('appointment_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">End of Term Date</label>
                                <input type="date" name="end_date" class="form-control bm-input" value="{{ old('end_date', $boardMember->end_date) }}">
                                <small class="text-muted">Member will be auto-flagged when term expires</small>
                                @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Exit Date</label>
                                <input type="date" name="exit_date" class="form-control bm-input" value="{{ old('exit_date', $boardMember->exit_date) }}">
                                @error('exit_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bm-section-card">
                    <div class="bm-section-title"><i class="mdi mdi-map-marker-outline"></i> Address & Location</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">County <span class="text-danger">*</span></label>
                                <select name="county_id" class="form-select bm-input">
                                    <option value="">Select County</option>
                                    @foreach($counties as $county)
                                        <option value="{{ $county->id }}" {{ old('county_id', $boardMember->county_id) == $county->id ? 'selected' : '' }}>
                                            {{ $county->county_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('county_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Place of Residence <span class="text-danger">*</span></label>
                                <input type="text" name="place_of_residence" class="form-control bm-input" value="{{ old('place_of_residence', $boardMember->place_of_residence) }}">
                                @error('place_of_residence') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="bm-label">Postal Address <span class="text-danger">*</span></label>
                                <input type="text" name="postal_address" class="form-control bm-input" value="{{ old('postal_address', $boardMember->postal_address) }}">
                                @error('postal_address') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Qualifications --}}
            <div class="bm-step-panel" data-step="3">
                <div class="bm-section-card">
                    <div class="bm-section-title"><i class="mdi mdi-school-outline"></i> Academic & Professional Background</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bm-label">Qualifications</label>
                                <textarea name="qualifications" class="form-control bm-input" rows="5">{{ old('qualifications', $boardMember->qualifications) }}</textarea>
                                @error('qualifications') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bm-label">Experience Summary</label>
                                <textarea name="experience_summary" class="form-control bm-input" rows="5">{{ old('experience_summary', $boardMember->experience_summary) }}</textarea>
                                @error('experience_summary') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Documents --}}
            <div class="bm-step-panel" data-step="4">
                <div class="bm-section-card">
                    <div class="bm-section-title"><i class="mdi mdi-camera-outline"></i> Profile Photo</div>
                    <div class="bm-photo-edit-wrap justify-content-center">
                        <div>
                            <small class="text-muted d-block text-center mb-1">Current</small>
                            <div class="bm-photo-current">
                                <img src="{{ $currentPhoto }}" alt="Current" onerror="this.src='{{ $placeholderPhoto }}';">
                            </div>
                        </div>
                        <div style="font-size: 1.5rem; color: #dee2e6;"><i class="mdi mdi-arrow-right"></i></div>
                        <div>
                            <small class="text-muted d-block text-center mb-1">New Photo</small>
                            <div class="bm-photo-new-wrap">
                                <div class="bm-photo-placeholder text-center" id="photoPlaceholder">
                                    <i class="mdi mdi-camera-plus-outline" style="font-size: 2rem; color: #adb5bd;"></i>
                                    <span style="display:block; font-size: 0.7rem; color: #adb5bd;">Upload</span>
                                </div>
                                <img id="photoPreview" src="#" alt="New" style="display:none;">
                                <input type="file" name="photo" id="photoInput" accept="image/*">
                            </div>
                        </div>
                    </div>
                    @error('photo') <small class="text-danger d-block text-center mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="bm-section-card">
                    <div class="bm-section-title"><i class="mdi mdi-file-document-outline"></i> Official Documents</div>
                    <div class="row">
                        @php
                            $docs = [
                                ['name' => 'upload_id', 'label' => 'National ID / Passport', 'icon' => 'mdi-card-account-details-outline', 'accept' => '.jpg,.jpeg,.png,.pdf', 'current' => $boardMember->upload_id],
                                ['name' => 'upload_cv', 'label' => 'Curriculum Vitae', 'icon' => 'mdi-file-account-outline', 'accept' => '.pdf,.doc,.docx', 'current' => $boardMember->upload_cv],
                                ['name' => 'appointment_letter', 'label' => 'Appointment Letter', 'icon' => 'mdi-file-sign', 'accept' => '.jpg,.jpeg,.png,.pdf', 'current' => $boardMember->appointment_letter],
                                ['name' => 'gazettee_notice', 'label' => 'Gazette Notice', 'icon' => 'mdi-newspaper-variant-outline', 'accept' => '.jpg,.jpeg,.png,.pdf', 'current' => $boardMember->gazettee_notice],
                                ['name' => 'acceptance_letter', 'label' => 'Acceptance Letter', 'icon' => 'mdi-file-check-outline', 'accept' => '.jpg,.jpeg,.png,.pdf', 'current' => $boardMember->acceptance_letter],
                            ];
                        @endphp
                        @foreach($docs as $doc)
                            <div class="col-md-4 col-lg-4 mb-3">
                                <label class="bm-label">{{ $doc['label'] }}</label>
                                <div class="bm-upload-zone {{ $doc['current'] ? 'has-file' : '' }}" id="zone_{{ $doc['name'] }}">
                                    <div class="bm-upload-icon"><i class="mdi {{ $doc['icon'] }}"></i></div>
                                    <div class="bm-upload-label">{{ $doc['current'] ? 'Replace file' : 'Click or drag file here' }}</div>
                                    <div class="bm-upload-filename" id="fname_{{ $doc['name'] }}" style="{{ $doc['current'] ? '' : 'display:none;' }}">
                                        @if($doc['current']) Currently uploaded @endif
                                    </div>
                                    <input type="file" name="{{ $doc['name'] }}" accept="{{ $doc['accept'] }}" onchange="handleFileSelect(this, '{{ $doc['name'] }}')">
                                </div>
                                @if($doc['current'])
                                    <div class="bm-current-file">
                                        <i class="mdi mdi-paperclip"></i> <a href="{{ asset($doc['current']) }}" target="_blank">View current file</a>
                                    </div>
                                @endif
                                @error($doc['name']) <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bm-footer-actions">
                <button type="button" class="btn btn-bm-outline" id="prevBtn" style="display:none;">
                    <i class="mdi mdi-arrow-left me-1"></i> Previous
                </button>
                <div class="ms-auto d-flex gap-2">
                    <button type="button" class="btn btn-bm-primary" id="nextBtn">
                        Next <i class="mdi mdi-arrow-right ms-1"></i>
                    </button>
                    <button type="submit" class="btn btn-bm-success" id="submitBtn" style="display:none;">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Update Board Member
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    let currentStep = 1;
    const totalSteps = 4;
    const stepBtns = document.querySelectorAll('.bm-step-btn');
    const panels = document.querySelectorAll('.bm-step-panel');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    function goToStep(step) {
        currentStep = step;
        stepBtns.forEach(btn => {
            const s = parseInt(btn.dataset.step);
            btn.classList.toggle('active', s === step);
        });
        panels.forEach(p => p.classList.toggle('active', parseInt(p.dataset.step) === step));
        prevBtn.style.display = step > 1 ? '' : 'none';
        nextBtn.style.display = step < totalSteps ? '' : 'none';
        submitBtn.style.display = step === totalSteps ? '' : 'none';
    }

    stepBtns.forEach(btn => btn.addEventListener('click', () => goToStep(parseInt(btn.dataset.step))));
    nextBtn.addEventListener('click', () => { if (currentStep < totalSteps) goToStep(currentStep + 1); });
    prevBtn.addEventListener('click', () => { if (currentStep > 1) goToStep(currentStep - 1); });

    // Photo preview
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    photoPreview.src = e.target.result;
                    photoPreview.style.display = 'block';
                    if (photoPlaceholder) photoPlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }
})();

function handleFileSelect(input, name) {
    const zone = document.getElementById('zone_' + name);
    const fnameEl = document.getElementById('fname_' + name);
    if (input.files.length > 0) {
        zone.classList.add('has-file');
        fnameEl.textContent = input.files[0].name;
        fnameEl.style.display = 'block';
    } else {
        zone.classList.remove('has-file');
        fnameEl.style.display = 'none';
    }
}
</script>

@endsection
