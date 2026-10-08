@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-body">
                <h4>Verify Your Declaration</h4>
                <p>
                    Please enter the 6-digit OTP sent to <strong>{{ auth()->user()->email }}</strong>
                    to confirm your declaration.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('conflict.verify', $declaration->id) }}">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="otp" maxlength="6" class="form-control text-center" placeholder="Enter OTP">
                    </div>

                    <button type="submit" class="btn btn-success w-100">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
@endsection
