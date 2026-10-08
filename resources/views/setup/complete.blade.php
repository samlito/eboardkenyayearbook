@extends('setup.layout')

@section('content')
<div class="text-center py-5">
    <div class="mb-4">
        <i class="mdi mdi-check-circle-outline" style="font-size: 5rem; color: var(--setup-accent);"></i>
    </div>
    <h2 class="section-title" style="color: var(--setup-accent);">Setup Complete!</h2>
    <p class="section-subtitle mx-auto" style="max-width: 500px;">
        Your eBoard system has been configured successfully. You can now log in with your admin account
        and start using the system. All settings can be modified later from the Admin Settings panel.
    </p>

    <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="{{ url('/login') }}" class="btn btn-setup btn-lg">
            <i class="mdi mdi-login"></i> Go to Login
        </a>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg">
            <i class="mdi mdi-home-outline"></i> View Landing Page
        </a>
    </div>
</div>
@endsection
