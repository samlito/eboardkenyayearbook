@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="card shadow">
            <div class="card-body text-center">
                <h4>Declaration Submitted ✅</h4>
                <p>
                    Thank you, <strong>{{ auth()->user()->name }}</strong>.
                    Your declaration for <strong>{{ $declaration->meeting->title }}</strong> has been received.
                </p>
                <p>
                    An OTP has been sent to <strong>{{ auth()->user()->email }}</strong>.
                    Please confirm to complete your submission.
                </p>

                <a href="{{ route('conflict.verify.form', $declaration->id) }}" class="btn btn-primary mt-3">
                    Enter OTP
                </a>
            </div>
        </div>
    </div>
@endsection
