@extends('setup.layout')

@section('content')
<div class="text-center py-4">
    <div class="mb-4">
        <i class="mdi mdi-rocket-launch-outline" style="font-size: 4rem; color: var(--setup-primary);"></i>
    </div>
    <h2 class="section-title">Welcome to eBoard Setup</h2>
    <p class="section-subtitle mx-auto" style="max-width: 500px;">
        This wizard will help you configure the eBoard Management System for your institution.
        You'll set up your institution's identity, branding, workflow preferences, and admin account.
    </p>

    <div class="row g-3 mt-4 mb-4 text-start" style="max-width: 600px; margin: 0 auto;">
        <div class="col-md-6">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-domain text-primary mt-1"></i>
                <div>
                    <strong>Institution Identity</strong>
                    <div class="text-muted small">Name, address, contacts</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-palette text-primary mt-1"></i>
                <div>
                    <strong>Branding</strong>
                    <div class="text-muted small">Logo, colors, appearance</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-cog-outline text-primary mt-1"></i>
                <div>
                    <strong>Workflow Settings</strong>
                    <div class="text-muted small">Reference numbers, signatories</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-email-outline text-primary mt-1"></i>
                <div>
                    <strong>Email & Admin</strong>
                    <div class="text-muted small">SMTP config, admin account</div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('setup.show', 1) }}" class="btn btn-setup btn-lg mt-3">
        <i class="mdi mdi-arrow-right"></i> Start Setup
    </a>
</div>
@endsection
