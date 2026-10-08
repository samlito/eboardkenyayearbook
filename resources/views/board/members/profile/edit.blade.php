@extends('admin_dashboard')

@section('admin')
    @php
        use Carbon\Carbon;
    @endphp

    <style>
        :root{ --brand:{{ setting('primary_color', '#1a1a1a') }}; --brand-soft:#fdecee; }
        .card-shadow{ border:0; border-radius:16px; box-shadow:0 8px 22px rgba(0,0,0,.06); }
        .brand-gradient{ background: linear-gradient(135deg, var(--brand) 0%, #3a3a3a 100%); }
        .btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
        .btn-brand:hover{ opacity:.93; color:#fff; }
        .section-title{ font-weight:700; color:#1b1b1b; }
    </style>

    <div class="content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="card brand-gradient text-white card-shadow mb-4">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">Edit Profile</h3>
                        <div class="opacity-75">{{ $member->names }}</div>
                    </div>
                    <div>
                        <a href="{{ route('board.profile.show', $member->id) }}" class="btn btn-light btn-sm">Back to Profile</a>
                    </div>
                </div>
            </div>

            {{-- Validation + flash --}}
            @if ($errors->any())
                <div class="alert alert-danger card-shadow">
                    <div class="fw-semibold mb-2">Please fix the following:</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success card-shadow">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('board.profile.update', $member->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- Personal --}}
                    <div class="col-lg-6">
                        <div class="card card-shadow h-100">
                            <div class="card-body">
                                <div class="section-title mb-3">Personal Information</div>

                                <div class="mb-2">
                                    <label class="form-label">Salutation</label>
                                    <select name="salutation" class="form-select">
                                        <option value="">— Select —</option>
                                        @foreach($salutations as $s)
                                            <option value="{{ $s->id }}" @selected(old('salutation', $member->salutation) == $s->id)>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Full Names <span class="text-danger">*</span></label>
                                    <input type="text" name="names" class="form-control" value="{{ old('names', $member->names) }}" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Designation</label>
                                    <input type="text" name="designation" class="form-control" value="{{ old('designation', $member->designation) }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Director Type</label>
                                        <select name="director_type" class="form-select">
                                            <option value="">—</option>
                                            @foreach(['Executive','Non-Executive','Chairperson','Member'] as $opt)
                                                <option value="{{ $opt }}" @selected(old('director_type', $member->director_type) == $opt)>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Role Type</label>
                                        <select name="role_type" class="form-select">
                                            <option value="">—</option>
                                            @foreach(['independent','representative'] as $opt)
                                                <option value="{{ $opt }}" @selected(old('role_type', $member->role_type) == $opt)>{{ ucfirst($opt) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $member->date_of_birth) }}">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">National ID/Passport</label>
                                    <input type="text" name="idno" class="form-control" value="{{ old('idno', $member->idno) }}">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Place of Residence</label>
                                    <input type="text" name="place_of_residence" class="form-control" value="{{ old('place_of_residence', $member->place_of_residence) }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">County</label>
                                        <select name="county_id" class="form-select">
                                            <option value="">— Select county —</option>
                                            @foreach($counties as $c)
                                                <option value="{{ $c->id }}" @selected(old('county_id', $member->county_id) == $c->id)>{{ $c->county_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Postal Address</label>
                                        <input type="text" name="postal_address" class="form-control" value="{{ old('postal_address', $member->postal_address) }}">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Contact + Appointment --}}
                    <div class="col-lg-6">
                        <div class="card card-shadow h-100">
                            <div class="card-body">
                                <div class="section-title mb-3">Contact & Appointment</div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $member->email) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $member->phone) }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="appointment_date" class="form-control" value="{{ old('appointment_date', $member->appointment_date) }}" required>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $member->end_date) }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Exit Date</label>
                                        <input type="date" name="exit_date" class="form-control" value="{{ old('exit_date', $member->exit_date) }}">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Status</label>
                                    <input type="text" name="status" class="form-control" value="{{ old('status', $member->status) }}">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Qualifications</label>
                                    <textarea name="qualifications" rows="4" class="form-control" placeholder="Degrees, professional certifications, etc.">{{ old('qualifications', $member->qualifications) }}</textarea>
                                </div>

                                <div>
                                    <label class="form-label">Experience Summary</label>
                                    <textarea name="experience_summary" rows="5" class="form-control" placeholder="Brief bio / career highlights">{{ old('experience_summary', $member->experience_summary) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div class="col-12">
                        <div class="card card-shadow">
                            <div class="card-body">
                                <div class="section-title mb-3">Documents & Photo</div>

                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="photo" class="form-control">
                                        @if($member->photo)
                                            <small class="text-muted">Current: <a href="{{ asset('storage/'.$member->photo) }}" target="_blank">view</a></small>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">National ID / Passport</label>
                                        <input type="file" name="upload_id" class="form-control">
                                        @if($member->upload_id)
                                            <small class="text-muted">Current: <a href="{{ asset('storage/'.$member->upload_id) }}" target="_blank">view</a></small>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Curriculum Vitae</label>
                                        <input type="file" name="upload_cv" class="form-control">
                                        @if($member->upload_cv)
                                            <small class="text-muted">Current: <a href="{{ asset('storage/'.$member->upload_cv) }}" target="_blank">view</a></small>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Appointment Letter (PDF)</label>
                                        <input type="file" name="appointment_letter" class="form-control">
                                        @if($member->appointment_letter)
                                            <small class="text-muted">Current: <a href="{{ asset('storage/'.$member->appointment_letter) }}" target="_blank">view</a></small>
                                        @endif
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Gazette Notice (PDF)</label>
                                        <input type="file" name="gazettee_notice" class="form-control">
                                        @if($member->gazettee_notice)
                                            <small class="text-muted">Current: <a href="{{ asset('storage/'.$member->gazettee_notice) }}" target="_blank">view</a></small>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Acceptance Letter (PDF)</label>
                                        <input type="file" name="acceptance_letter" class="form-control">
                                        @if($member->acceptance_letter)
                                            <small class="text-muted">Current: <a href="{{ asset('storage/'.$member->acceptance_letter) }}" target="_blank">view</a></small>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Signature Specimen (PNG/JPG)</label>
                                        <input type="file" name="signature_image" class="form-control">
                                        @if($member->signature_image)
                                            <small class="text-muted">
                                                Current: <a href="{{ asset('storage/'.$member->signature_image) }}" target="_blank">view</a>
                                            </small>
                                        @endif
                                    </div>

                                </div>

                                <div class="text-end mt-3">
                                    <button class="btn btn-brand">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> {{-- /row --}}
            </form>

        </div>
    </div>
@endsection
