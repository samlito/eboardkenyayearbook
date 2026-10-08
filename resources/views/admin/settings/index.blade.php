@extends('admin_dashboard')
@section('admin')

@php
    $activeTab = request('tab', 'institution');
    $tabs = [
        'institution' => ['icon' => 'mdi-domain', 'label' => 'Institution', 'desc' => 'Identity & contact'],
        'branding'    => ['icon' => 'mdi-palette-outline', 'label' => 'Branding', 'desc' => 'Logos & colors'],
        'workflow'    => ['icon' => 'mdi-cog-transfer-outline', 'label' => 'Workflow', 'desc' => 'Prefixes & titles'],
        'roles'       => ['icon' => 'mdi-badge-account-outline', 'label' => 'Role Labels', 'desc' => 'Display names'],
        'email'       => ['icon' => 'mdi-email-fast-outline', 'label' => 'Email', 'desc' => 'SMTP config'],
        'landing'     => ['icon' => 'mdi-web', 'label' => 'Landing Page', 'desc' => 'Slides & content'],
    ];
@endphp

<style>
    :root { --brand: {{ setting('primary_color', '#1a1a1a') }}; --brand-light: {{ setting('primary_color', '#1a1a1a') }}12; }

    .settings-sidebar { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 12px rgba(0,0,0,0.04); }
    .settings-sidebar .nav-link {
        display: flex; align-items: center; gap: 14px; padding: 14px 20px;
        color: #4b5563; border-left: 3px solid transparent; transition: all .2s; border-radius: 0;
    }
    .settings-sidebar .nav-link:hover { background: #f9fafb; color: #0d0d0d; }
    .settings-sidebar .nav-link.active {
        background: var(--brand-light); color: var(--brand); border-left-color: var(--brand); font-weight: 600;
    }
    .settings-sidebar .nav-link .tab-icon {
        width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        background: #f3f4f6; font-size: 18px; flex-shrink: 0; transition: .2s;
    }
    .settings-sidebar .nav-link.active .tab-icon { background: var(--brand); color: #fff; }
    .settings-sidebar .tab-desc { font-size: 11px; color: #9ca3af; font-weight: 400; }
    .settings-sidebar .nav-link.active .tab-desc { color: var(--brand); opacity: .7; }

    .settings-card { background: #fff; border-radius: 16px; box-shadow: 0 1px 12px rgba(0,0,0,0.04); overflow: hidden; }
    .settings-card-header {
        padding: 24px 28px 16px; border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; gap: 14px;
    }
    .settings-card-header .header-icon {
        width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        background: var(--brand-light); color: var(--brand); font-size: 22px;
    }
    .settings-card-body { padding: 28px; }

    .form-label-sm { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }
    .form-control:focus, .form-select:focus { border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-light); }

    .section-divider { border: none; border-top: 1px dashed #e5e7eb; margin: 24px 0; }
    .section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #9ca3af; margin-bottom: 16px; }

    .upload-zone {
        border: 2px dashed #d1d5db; border-radius: 12px; padding: 16px; text-align: center;
        background: #fbfbfb; transition: .2s; cursor: pointer; position: relative; min-height: 100px;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px;
    }
    .upload-zone:hover { border-color: var(--brand); background: var(--brand-light); }
    .upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .upload-zone .preview-img { max-height: 60px; border-radius: 8px; }
    .upload-zone .upload-icon { font-size: 24px; color: #9ca3af; }
    .upload-zone .upload-text { font-size: 12px; color: #9ca3af; }

    .color-picker-group { display: flex; align-items: center; gap: 12px; }
    .color-picker-group input[type="color"] { width: 48px; height: 48px; border: 2px solid #e5e7eb; border-radius: 12px; padding: 2px; cursor: pointer; }
    .color-picker-group .color-hex { font-family: monospace; font-size: 14px; color: #6b7280; width: 90px; text-align: center; border-radius: 8px; }

    .repeater-card {
        background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; position: relative;
        transition: .2s; margin-bottom: 12px;
    }
    .repeater-card:hover { border-color: #d1d5db; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
    .repeater-card .card-number {
        position: absolute; top: -10px; left: 16px; background: var(--brand); color: #fff;
        font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 20px;
    }
    .btn-remove-item {
        position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%;
        border: none; background: #fee2e2; color: #dc2626; font-size: 14px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; transition: .2s;
    }
    .btn-remove-item:hover { background: #dc2626; color: #fff; }

    .btn-add-item {
        border: 2px dashed #d1d5db; background: transparent; color: #6b7280; border-radius: 12px;
        padding: 14px; width: 100%; font-weight: 600; font-size: 14px; cursor: pointer; transition: .2s;
    }
    .btn-add-item:hover { border-color: var(--brand); color: var(--brand); background: var(--brand-light); }

    .btn-save {
        background: var(--brand); color: #fff; border: none; border-radius: 10px; padding: 10px 28px;
        font-weight: 600; font-size: 14px; transition: .2s;
    }
    .btn-save:hover { opacity: .9; transform: translateY(-1px); color: #fff; }

    .test-email-group { display: flex; gap: 8px; }
    .test-email-group input { border-radius: 10px; }
    .test-email-group button { border-radius: 10px; white-space: nowrap; }

    .icon-picker-preview { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: var(--brand-light); border-radius: 8px; color: var(--brand); font-size: 18px; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container-fluid">
    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1">System Settings</h4>
                    <p class="text-muted mb-0">Configure your {{ setting('institution_acronym', 'ORG') }} eBoard system</p>
                </div>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert border-0 d-flex align-items-center alert-dismissible fade show mb-3" style="background: #ecfdf5; color: #065f46; border-radius: 12px;">
            <i class="mdi mdi-check-circle me-2" style="font-size: 20px;"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert border-0 d-flex align-items-center alert-dismissible fade show mb-3" style="background: #fef2f2; color: #991b1b; border-radius: 12px;">
            <i class="mdi mdi-alert-circle me-2" style="font-size: 20px;"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Sidebar Tabs --}}
        <div class="col-lg-3 mb-3">
            <div class="settings-sidebar">
                <div class="nav flex-column" role="tablist">
                    @foreach($tabs as $key => $tab)
                        <a class="nav-link {{ $activeTab == $key ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-{{ $key }}" role="tab">
                            <div class="tab-icon"><i class="mdi {{ $tab['icon'] }}"></i></div>
                            <div>
                                <div>{{ $tab['label'] }}</div>
                                <div class="tab-desc">{{ $tab['desc'] }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="col-lg-9">
            <div class="tab-content">

                {{-- ═══════════ INSTITUTION ═══════════ --}}
                <div class="tab-pane fade {{ $activeTab == 'institution' ? 'show active' : '' }}" id="tab-institution">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="header-icon"><i class="mdi mdi-domain"></i></div>
                            <div>
                                <h5 class="mb-0 fw-bold">Institution Identity</h5>
                                <p class="text-muted mb-0 small">Your organization's name, contact details and public info</p>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <form action="{{ route('admin.settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="group" value="institution">

                                <div class="section-title">Organization</div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label-sm">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="institution_name" class="form-control" value="{{ setting('institution_name') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Acronym <span class="text-danger">*</span></label>
                                        <input type="text" name="institution_acronym" class="form-control" value="{{ setting('institution_acronym') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-sm">Tagline / Motto</label>
                                        <input type="text" name="institution_tagline" class="form-control" value="{{ setting('institution_tagline') }}" placeholder="e.g. Empowering Governance Through Technology">
                                    </div>
                                </div>

                                <hr class="section-divider">
                                <div class="section-title">Address</div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-sm">Physical Address</label>
                                        <textarea name="physical_address" class="form-control" rows="2" placeholder="Street address, building, floor">{{ setting('physical_address') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-sm">Postal Address</label>
                                        <input type="text" name="postal_address" class="form-control" value="{{ setting('postal_address') }}" placeholder="P.O. Box 123 – 00100, City">
                                    </div>
                                </div>

                                <hr class="section-divider">
                                <div class="section-title">Contact</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-sm"><i class="mdi mdi-phone me-1"></i> Primary Phone</label>
                                        <input type="text" name="phone_primary" class="form-control" value="{{ setting('phone_primary') }}" placeholder="+254 700 000 000">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-sm"><i class="mdi mdi-phone-outline me-1"></i> Secondary Phone</label>
                                        <input type="text" name="phone_secondary" class="form-control" value="{{ setting('phone_secondary') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-sm"><i class="mdi mdi-email-outline me-1"></i> Info Email</label>
                                        <input type="email" name="email_info" class="form-control" value="{{ setting('email_info') }}" placeholder="info@example.go.ke">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-sm"><i class="mdi mdi-web me-1"></i> Website</label>
                                        <input type="url" name="website_url" class="form-control" value="{{ setting('website_url') }}" placeholder="https://www.example.go.ke">
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline me-1"></i> Save Institution</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ═══════════ BRANDING ═══════════ --}}
                <div class="tab-pane fade {{ $activeTab == 'branding' ? 'show active' : '' }}" id="tab-branding">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="header-icon"><i class="mdi mdi-palette-outline"></i></div>
                            <div>
                                <h5 class="mb-0 fw-bold">Branding & Appearance</h5>
                                <p class="text-muted mb-0 small">Logos, colors, and visual identity across the system</p>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <form action="{{ route('admin.settings.branding') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="section-title">Logos & Images</div>
                                <div class="row g-3 mb-3">
                                    @foreach([
                                        'logo_main' => ['label' => 'Main Logo', 'hint' => 'Header, login, emails', 'icon' => 'mdi-image-outline'],
                                        'logo_sidebar' => ['label' => 'Sidebar Logo', 'hint' => 'Small admin sidebar icon', 'icon' => 'mdi-image-size-select-small'],
                                        'logo_letterhead' => ['label' => 'Letterhead', 'hint' => 'PDF documents & notices', 'icon' => 'mdi-file-image-outline'],
                                        'favicon' => ['label' => 'Favicon', 'hint' => 'Browser tab icon', 'icon' => 'mdi-star-four-points-small'],
                                        'ceo_signature_image' => ['label' => 'CEO Signature', 'hint' => 'Official documents', 'icon' => 'mdi-draw-pen'],
                                        'login_background' => ['label' => 'Login Background', 'hint' => 'Login page backdrop', 'icon' => 'mdi-panorama-outline'],
                                    ] as $field => $info)
                                    <div class="col-md-4">
                                        <label class="form-label-sm">{{ $info['label'] }}</label>
                                        <div class="upload-zone">
                                            @if(setting($field))
                                                <img src="{{ asset(setting($field)) }}" alt="{{ $info['label'] }}" class="preview-img">
                                                <div class="upload-text mt-1">Click to replace</div>
                                            @else
                                                <i class="mdi {{ $info['icon'] }} upload-icon"></i>
                                                <div class="upload-text">Click to upload</div>
                                            @endif
                                            <input type="file" name="{{ $field }}" accept="image/*" onchange="previewUpload(this)">
                                        </div>
                                        <div class="form-hint">{{ $info['hint'] }}</div>
                                    </div>
                                    @endforeach
                                </div>

                                <hr class="section-divider">
                                <div class="section-title">Brand Colors</div>
                                <div class="row g-4">
                                    @foreach([
                                        'primary_color' => ['label' => 'Primary', 'hint' => 'Main brand color used across headers, buttons, links', 'default' => '#1a1a1a'],
                                        'accent_color' => ['label' => 'Accent', 'hint' => 'Highlights, success states, secondary actions', 'default' => '#7AC143'],
                                        'secondary_color' => ['label' => 'Secondary', 'hint' => 'Gradients, secondary elements, hover states', 'default' => '#2b2b2b'],
                                    ] as $field => $info)
                                    <div class="col-md-4">
                                        <label class="form-label-sm">{{ $info['label'] }} Color</label>
                                        <div class="color-picker-group">
                                            <input type="color" name="{{ $field }}" value="{{ setting($field, $info['default']) }}" onchange="this.nextElementSibling.value = this.value">
                                            <input type="text" class="form-control color-hex" value="{{ setting($field, $info['default']) }}" readonly>
                                        </div>
                                        <div class="form-hint">{{ $info['hint'] }}</div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline me-1"></i> Save Branding</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ═══════════ WORKFLOW ═══════════ --}}
                <div class="tab-pane fade {{ $activeTab == 'workflow' ? 'show active' : '' }}" id="tab-workflow">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="header-icon"><i class="mdi mdi-cog-transfer-outline"></i></div>
                            <div>
                                <h5 class="mb-0 fw-bold">Workflow Configuration</h5>
                                <p class="text-muted mb-0 small">Reference numbers, signatory titles, and document defaults</p>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <form action="{{ route('admin.settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="group" value="workflow">

                                <div class="section-title">Prefixes & Defaults</div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Reference Prefix <span class="text-danger">*</span></label>
                                        <input type="text" name="reference_number_prefix" class="form-control" value="{{ setting('reference_number_prefix') }}" required>
                                        <div class="form-hint">e.g. ORG &rarr; ORG/AUD/1/07/2026</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Staff Number Prefix</label>
                                        <input type="text" name="staff_number_prefix" class="form-control" value="{{ setting('staff_number_prefix') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Email Subject Prefix <span class="text-danger">*</span></label>
                                        <input type="text" name="email_subject_prefix" class="form-control" value="{{ setting('email_subject_prefix') }}" required>
                                        <div class="form-hint">Prepended to all email subjects</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Default Meeting Venue</label>
                                        <input type="text" name="default_venue" class="form-control" value="{{ setting('default_venue') }}" placeholder="e.g. Board Room, 5th Floor">
                                    </div>
                                </div>

                                <hr class="section-divider">
                                <div class="section-title">Signatory Titles</div>
                                <p class="text-muted small mb-3">These titles appear on official documents, notices, and PDFs</p>
                                <div class="row g-3">
                                    @foreach([
                                        'signatory_title_ceo' => ['label' => 'CEO / Head of Institution', 'default' => 'Chief Executive Officer', 'icon' => 'mdi-account-tie'],
                                        'signatory_title_chairperson' => ['label' => 'Board Chairperson', 'default' => 'Chairperson', 'icon' => 'mdi-account-star'],
                                        'signatory_title_audit_head' => ['label' => 'Head of Internal Audit', 'default' => 'Director, Internal Audit', 'icon' => 'mdi-clipboard-check'],
                                        'signatory_title_cs' => ['label' => 'Corporation Secretary', 'default' => 'Corporation Secretary', 'icon' => 'mdi-briefcase-account'],
                                    ] as $field => $info)
                                    <div class="col-md-6">
                                        <label class="form-label-sm"><i class="mdi {{ $info['icon'] }} me-1"></i> {{ $info['label'] }}</label>
                                        <input type="text" name="{{ $field }}" class="form-control" value="{{ setting($field, $info['default']) }}">
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline me-1"></i> Save Workflow</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ═══════════ ROLE LABELS ═══════════ --}}
                <div class="tab-pane fade {{ $activeTab == 'roles' ? 'show active' : '' }}" id="tab-roles">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="header-icon"><i class="mdi mdi-badge-account-outline"></i></div>
                            <div>
                                <h5 class="mb-0 fw-bold">Role Display Labels</h5>
                                <p class="text-muted mb-0 small">Customize how role names appear to users in the interface</p>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="alert border-0 d-flex align-items-start mb-4" style="background: #f7f7f7; border-radius: 12px;">
                                <i class="mdi mdi-information-outline me-2 mt-1" style="color: var(--brand); font-size: 18px;"></i>
                                <div class="small">
                                    Internal role identifiers (used by the system for permissions) remain unchanged.
                                    These labels only affect what users see in the UI — menus, badges, and headings.
                                </div>
                            </div>
                            <form action="{{ route('admin.settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="group" value="roles">
                                <div class="row g-3">
                                    @foreach([
                                        'role_label_ceo' => ['system' => 'CEO', 'desc' => 'Head of institution', 'icon' => 'mdi-account-tie'],
                                        'role_label_corporation_secretary' => ['system' => 'Corporation Secretary', 'desc' => 'Board secretary / governance', 'icon' => 'mdi-briefcase-account'],
                                        'role_label_board_member' => ['system' => 'Board Member', 'desc' => 'Board directors', 'icon' => 'mdi-account-group'],
                                        'role_label_director' => ['system' => 'Director', 'desc' => 'Department directors', 'icon' => 'mdi-account-supervisor'],
                                        'role_label_division_head' => ['system' => 'Division Head', 'desc' => 'Division managers', 'icon' => 'mdi-account-hard-hat'],
                                        'role_label_dd_internal_audit' => ['system' => 'DD Internal Audit', 'desc' => 'Internal audit head', 'icon' => 'mdi-clipboard-check'],
                                        'role_label_chairperson' => ['system' => 'Chairperson', 'desc' => 'Board chairperson', 'icon' => 'mdi-account-star'],
                                    ] as $key => $info)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-picker-preview"><i class="mdi {{ $info['icon'] }}"></i></div>
                                            <div class="flex-grow-1">
                                                <label class="form-label-sm mb-0">{{ $info['desc'] }}</label>
                                                <div class="form-hint mb-1">System: <code>{{ $info['system'] }}</code></div>
                                                <input type="text" name="{{ $key }}" class="form-control form-control-sm" value="{{ setting($key, $info['system']) }}">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline me-1"></i> Save Role Labels</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ═══════════ EMAIL ═══════════ --}}
                <div class="tab-pane fade {{ $activeTab == 'email' ? 'show active' : '' }}" id="tab-email">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="header-icon"><i class="mdi mdi-email-fast-outline"></i></div>
                            <div>
                                <h5 class="mb-0 fw-bold">Email Configuration</h5>
                                <p class="text-muted mb-0 small">SMTP server settings for sending system emails</p>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <form action="{{ route('admin.settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="group" value="email">

                                <div class="section-title">SMTP Server</div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-7">
                                        <label class="form-label-sm">SMTP Host <span class="text-danger">*</span></label>
                                        <input type="text" name="mail_host" class="form-control" value="{{ setting('mail_host') }}" placeholder="smtp.gmail.com" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label-sm">Port <span class="text-danger">*</span></label>
                                        <input type="number" name="mail_port" class="form-control" value="{{ setting('mail_port', 587) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm">Encryption</label>
                                        <select name="mail_encryption" class="form-select">
                                            <option value="tls" {{ setting('mail_encryption') == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                            <option value="ssl" {{ setting('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="null" {{ setting('mail_encryption') == 'null' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                </div>

                                <hr class="section-divider">
                                <div class="section-title">Authentication</div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-sm">Username <span class="text-danger">*</span></label>
                                        <input type="text" name="mail_username" class="form-control" value="{{ setting('mail_username') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-sm">Password</label>
                                        <input type="password" name="mail_password" class="form-control" placeholder="Leave blank to keep current">
                                        <div class="form-hint">Only fill this if you want to change the password</div>
                                    </div>
                                </div>

                                <hr class="section-divider">
                                <div class="section-title">Sender Identity</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-sm">From Email <span class="text-danger">*</span></label>
                                        <input type="email" name="mail_from_address" class="form-control" value="{{ setting('mail_from_address') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-sm">From Name <span class="text-danger">*</span></label>
                                        <input type="text" name="mail_from_name" class="form-control" value="{{ setting('mail_from_name') }}" required>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex justify-content-between align-items-end">
                                    <div>
                                        <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline me-1"></i> Save Email Settings</button>
                                    </div>
                                </div>
                            </form>

                            <hr class="section-divider">
                            <div class="section-title">Test Configuration</div>
                            <form action="{{ route('admin.settings.test-email') }}" method="POST">
                                @csrf
                                <div class="test-email-group" style="max-width: 450px;">
                                    <input type="email" name="test_email" class="form-control" placeholder="recipient@example.com" required>
                                    <button type="submit" class="btn btn-outline-primary"><i class="mdi mdi-send-outline me-1"></i> Send Test</button>
                                </div>
                                <div class="form-hint mt-1">Send a test email to verify your SMTP configuration works</div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ═══════════ LANDING PAGE ═══════════ --}}
                <div class="tab-pane fade {{ $activeTab == 'landing' ? 'show active' : '' }}" id="tab-landing">
                    <form action="{{ route('admin.settings.landing') }}" method="POST">
                        @csrf

                        {{-- Visibility Toggles --}}
                        <div class="settings-card mb-3">
                            <div class="settings-card-header">
                                <div class="header-icon"><i class="mdi mdi-web"></i></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Landing Page</h5>
                                    <p class="text-muted mb-0 small">Configure your public-facing landing page content</p>
                                </div>
                            </div>
                            <div class="settings-card-body">
                                <div class="d-flex gap-4 flex-wrap">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="landing_show_features" value="1" id="sw-features" {{ setting('landing_show_features', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="sw-features">Show Features Section</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="landing_show_board_members" value="1" id="sw-members" {{ setting('landing_show_board_members', false) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="sw-members">Show Board Members Section</label>
                                    </div>
                                </div>

                                <hr class="section-divider">
                                <div class="section-title">Call to Action</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-sm">CTA Title</label>
                                        <input type="text" name="landing_cta_title" class="form-control" value="{{ setting('landing_cta_title') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-sm">CTA Subtitle</label>
                                        <input type="text" name="landing_cta_subtitle" class="form-control" value="{{ setting('landing_cta_subtitle') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">CTA Button Text</label>
                                        <input type="text" name="landing_cta_button_text" class="form-control" value="{{ setting('landing_cta_button_text', 'Access Board Portal') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hero Slides --}}
                        <div class="settings-card mb-3">
                            <div class="settings-card-header">
                                <div class="header-icon"><i class="mdi mdi-image-multiple-outline"></i></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Hero Slides</h5>
                                    <p class="text-muted mb-0 small">Carousel slides on the landing page hero section</p>
                                </div>
                            </div>
                            <div class="settings-card-body">
                                <div id="slides-container">
                                    @forelse($heroSlides as $i => $slide)
                                    <div class="repeater-card" data-index="{{ $i }}">
                                        <span class="card-number">Slide {{ $i + 1 }}</span>
                                        <button type="button" class="btn-remove-item" onclick="removeItem(this)"><i class="mdi mdi-close"></i></button>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label-sm">Title</label>
                                                <input type="text" name="slides[{{ $i }}][title]" class="form-control form-control-sm" value="{{ $slide['title'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label-sm">Subtitle</label>
                                                <input type="text" name="slides[{{ $i }}][subtitle]" class="form-control form-control-sm" value="{{ $slide['subtitle'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label-sm">Button Text</label>
                                                <input type="text" name="slides[{{ $i }}][button_text]" class="form-control form-control-sm" value="{{ $slide['button_text'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label-sm">Button URL</label>
                                                <input type="text" name="slides[{{ $i }}][button_url]" class="form-control form-control-sm" value="{{ $slide['button_url'] ?? '/login' }}">
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted text-center py-3">No slides yet. Add your first one below.</p>
                                    @endforelse
                                </div>
                                <button type="button" class="btn-add-item mt-2" onclick="addSlide()">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Add Slide
                                </button>
                            </div>
                        </div>

                        {{-- Features --}}
                        <div class="settings-card mb-3">
                            <div class="settings-card-header">
                                <div class="header-icon"><i class="mdi mdi-star-four-points-outline"></i></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Features</h5>
                                    <p class="text-muted mb-0 small">Feature cards displayed on the landing page</p>
                                </div>
                            </div>
                            <div class="settings-card-body">
                                <div id="features-container">
                                    @forelse($features as $i => $feature)
                                    <div class="repeater-card" data-index="{{ $i }}">
                                        <span class="card-number">Feature {{ $i + 1 }}</span>
                                        <button type="button" class="btn-remove-item" onclick="removeItem(this)"><i class="mdi mdi-close"></i></button>
                                        <div class="row g-2">
                                            <div class="col-md-2">
                                                <label class="form-label-sm">Icon</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="icon-picker-preview"><i class="{{ $feature['icon'] ?? 'mdi mdi-star' }}"></i></div>
                                                    <input type="text" name="features[{{ $i }}][icon]" class="form-control form-control-sm" value="{{ $feature['icon'] ?? '' }}" placeholder="mdi mdi-..." required onchange="updateIconPreview(this)">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label-sm">Title</label>
                                                <input type="text" name="features[{{ $i }}][title]" class="form-control form-control-sm" value="{{ $feature['title'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label-sm">Description</label>
                                                <input type="text" name="features[{{ $i }}][description]" class="form-control form-control-sm" value="{{ $feature['description'] ?? '' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted text-center py-3">No features yet. Add your first one below.</p>
                                    @endforelse
                                </div>
                                <button type="button" class="btn-add-item mt-2" onclick="addFeature()">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Add Feature
                                </button>
                            </div>
                        </div>

                        {{-- Board Members --}}
                        <div class="settings-card mb-3">
                            <div class="settings-card-header">
                                <div class="header-icon"><i class="mdi mdi-account-group-outline"></i></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Board Members</h5>
                                    <p class="text-muted mb-0 small">Board members displayed on the landing page (enable above)</p>
                                </div>
                            </div>
                            <div class="settings-card-body">
                                <div id="members-container">
                                    @forelse($boardMembers as $i => $member)
                                    <div class="repeater-card" data-index="{{ $i }}">
                                        <span class="card-number">Member {{ $i + 1 }}</span>
                                        <button type="button" class="btn-remove-item" onclick="removeItem(this)"><i class="mdi mdi-close"></i></button>
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <label class="form-label-sm">Full Name</label>
                                                <input type="text" name="members[{{ $i }}][name]" class="form-control form-control-sm" value="{{ $member['name'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label-sm">Designation</label>
                                                <input type="text" name="members[{{ $i }}][designation]" class="form-control form-control-sm" value="{{ $member['designation'] ?? '' }}" placeholder="e.g. Chairperson">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label-sm">Role / Title</label>
                                                <input type="text" name="members[{{ $i }}][role]" class="form-control form-control-sm" value="{{ $member['role'] ?? '' }}" placeholder="e.g. Board Director">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label-sm">Photo URL</label>
                                                <input type="text" name="members[{{ $i }}][photo_url]" class="form-control form-control-sm" value="{{ $member['photo_url'] ?? '' }}" placeholder="/uploads/members/photo.jpg">
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted text-center py-3">No board members added. Add them below.</p>
                                    @endforelse
                                </div>
                                <button type="button" class="btn-add-item mt-2" onclick="addMember()">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Add Board Member
                                </button>
                            </div>
                        </div>

                        <div class="text-end mb-4">
                            <a href="/" target="_blank" class="btn btn-outline-secondary rounded-pill me-2"><i class="mdi mdi-eye-outline me-1"></i> Preview Landing Page</a>
                            <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline me-1"></i> Save All Landing Page Settings</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewUpload(input) {
    const zone = input.closest('.upload-zone');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            zone.innerHTML = `<img src="${e.target.result}" class="preview-img"><div class="upload-text mt-1">Click to change</div>`;
            zone.appendChild(input);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeItem(btn) {
    const card = btn.closest('.repeater-card');
    card.style.opacity = '0';
    card.style.transform = 'scale(0.95)';
    card.style.transition = '0.2s';
    setTimeout(() => card.remove(), 200);
}

function getNextIndex(containerId) {
    const cards = document.querySelectorAll(`#${containerId} .repeater-card`);
    let max = -1;
    cards.forEach(c => { const idx = parseInt(c.dataset.index || 0); if (idx > max) max = idx; });
    return max + 1;
}

function addSlide() {
    const i = getNextIndex('slides-container');
    const container = document.getElementById('slides-container');
    container.querySelector('p.text-muted')?.remove();
    container.insertAdjacentHTML('beforeend', `
        <div class="repeater-card" data-index="${i}" style="animation: fadeIn .3s ease;">
            <span class="card-number">Slide ${i + 1}</span>
            <button type="button" class="btn-remove-item" onclick="removeItem(this)"><i class="mdi mdi-close"></i></button>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label-sm">Title</label>
                    <input type="text" name="slides[${i}][title]" class="form-control form-control-sm" required placeholder="Slide heading">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Subtitle</label>
                    <input type="text" name="slides[${i}][subtitle]" class="form-control form-control-sm" required placeholder="Supporting text">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Button Text</label>
                    <input type="text" name="slides[${i}][button_text]" class="form-control form-control-sm" value="Get Started">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Button URL</label>
                    <input type="text" name="slides[${i}][button_url]" class="form-control form-control-sm" value="/login">
                </div>
            </div>
        </div>
    `);
}

function addFeature() {
    const i = getNextIndex('features-container');
    const container = document.getElementById('features-container');
    container.querySelector('p.text-muted')?.remove();
    container.insertAdjacentHTML('beforeend', `
        <div class="repeater-card" data-index="${i}" style="animation: fadeIn .3s ease;">
            <span class="card-number">Feature ${i + 1}</span>
            <button type="button" class="btn-remove-item" onclick="removeItem(this)"><i class="mdi mdi-close"></i></button>
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label-sm">Icon</label>
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-picker-preview"><i class="mdi mdi-star"></i></div>
                        <input type="text" name="features[${i}][icon]" class="form-control form-control-sm" placeholder="mdi mdi-..." required onchange="updateIconPreview(this)">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label-sm">Title</label>
                    <input type="text" name="features[${i}][title]" class="form-control form-control-sm" required placeholder="Feature name">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Description</label>
                    <input type="text" name="features[${i}][description]" class="form-control form-control-sm" required placeholder="Short description">
                </div>
            </div>
        </div>
    `);
}

function addMember() {
    const i = getNextIndex('members-container');
    const container = document.getElementById('members-container');
    container.querySelector('p.text-muted')?.remove();
    container.insertAdjacentHTML('beforeend', `
        <div class="repeater-card" data-index="${i}" style="animation: fadeIn .3s ease;">
            <span class="card-number">Member ${i + 1}</span>
            <button type="button" class="btn-remove-item" onclick="removeItem(this)"><i class="mdi mdi-close"></i></button>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label-sm">Full Name</label>
                    <input type="text" name="members[${i}][name]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-sm">Designation</label>
                    <input type="text" name="members[${i}][designation]" class="form-control form-control-sm" placeholder="e.g. Chairperson">
                </div>
                <div class="col-md-3">
                    <label class="form-label-sm">Role / Title</label>
                    <input type="text" name="members[${i}][role]" class="form-control form-control-sm" placeholder="e.g. Board Director">
                </div>
                <div class="col-md-3">
                    <label class="form-label-sm">Photo URL</label>
                    <input type="text" name="members[${i}][photo_url]" class="form-control form-control-sm" placeholder="/uploads/members/photo.jpg">
                </div>
            </div>
        </div>
    `);
}

function updateIconPreview(input) {
    const preview = input.closest('.d-flex').querySelector('.icon-picker-preview i');
    if (preview) preview.className = input.value;
}
</script>
@endpush

@endsection
