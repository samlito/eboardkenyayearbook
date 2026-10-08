<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Code | {{ setting('institution_name', 'eBoard System') }}</title>
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Icons -->
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

    <style>
        .verification-code-input {
            width: 3rem;
            height: 3rem;
            font-size: 1.5rem;
            text-align: center;
            margin: 0.3rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.3s ease;
        }

        .verification-code-input:focus {
            outline: none;
            border-color: #d90012;
            box-shadow: 0 0 0 0.2rem rgba(217, 0, 18, 0.25);
        }

        .verification-code-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .timer {
            font-size: 1.2rem;
            color: red;
        }

        .error {
            color: red;
        }

        .error ul {
            padding-left: 0;
            list-style-type: none;
        }

        .error ul li {
            margin-bottom: 5px;
        }

        .btn-resend {
            margin-top: 20px;
            background-color: #28a745;
            color: white;
        }

        .card-img {
            height: 100%;
            object-fit: cover;
        }

        body {
            margin-top: 20px;
            background: #f9f9f9;
        }

        .icon-circle[class*=text-] [fill]:not([fill=none]),
        .icon-circle[class*=text-] svg:not([fill=none]),
        .svg-icon[class*=text-] [fill]:not([fill=none]),
        .svg-icon[class*=text-] svg:not([fill=none]) {
            fill: currentColor!important;
        }

        .svg-icon-xl>svg {
            width: 3.25rem;
            height: 3.25rem;
        }

        .hover-lift-light {
            transition: box-shadow .25s ease,transform .25s ease,color .25s ease,background-color .15s ease-in;
        }

        .mt-4 {
            margin-top: 1.5rem!important;
        }

        .w-100 {
            width: 100%!important;
        }

        .btn-group-lg>.btn,
        .btn-lg {
            padding: 0.8rem 1.85rem;
            font-size: 1.1rem;
            border-radius: 0.3rem;
        }

        .btn-purple {
            color: #fff;
            background-color: #e13746;
            border-color: #e13746;
        }

        .text-center {
            text-align: center!important;
        }

        .py-4 {
            padding-top: 1.5rem!important;
            padding-bottom: 1.5rem!important;
        }

        .form-control-lg {
            min-height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 1rem;
            font-size: 1.25rem;
            border-radius: 0.3rem;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #161616;
            background-color: #f9f9f9;
            background-clip: padding-box;
            border: 1px solid #dee2e6;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
    </style>
</head>

<body>

<!-- Wrapper -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg">
                        <div class="row g-0">
                            <!-- Left Side -->
                            <div class="col-lg-7 border-end">
                                <div class="card-body">
                                    <div class="p-5">
                                        <div class="text-start mb-4">
                                            <img src="{{ asset(setting('logo_main', 'backend/assets/images/logo.png')) }}" width="100" alt="{{ setting('institution_acronym', 'eBoard') }}">
                                        </div>
                                        <h4 class="font-weight-bold">Enter Verification Code</h4>
                                        <p class="text-muted mb-4">Please enter the 6-digit verification code sent to your email.</p>

                                        <!-- Validation errors -->
                                        @if ($errors->any())
                                            <div class="error">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <!-- Timer -->
                                        <p>Verification code expires in: <span class="timer" id="timer">{{ $expires_in }} seconds</span></p>

                                        <!-- Verification Code Form -->
                                        <form method="POST" action="/verify-code">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ session('user_id') }}">
                                            <div class="verification-code-wrapper">
                                                <!-- 6 Digit Input Fields -->
                                                @for ($i = 1; $i <= 6; $i++)
                                                    <input type="text" class="verification-code-input" maxlength="1" name="digit{{ $i }}" required pattern="\d">
                                                @endfor
                                            </div>

                                            <!-- Submit Button -->
                                            <button type="submit" class="btn btn-purple w-100">Verify Code</button>
                                        </form>

                                        <!-- Resend Code -->
                                        <div id="resend-code" style="display:none;">
                                            <form action="{{ route('resend.code') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ session('user_id') }}">
                                                <button type="submit" class="btn btn-resend w-100">Resend Code</button>
                                            </form>
                                        </div>

                                        <!-- Back to Login Button -->
                                        <a href="{{ route('login') }}" class="btn btn-secondary w-100 mt-3">Back to Login</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side Image -->
                            <div class="col-lg-5">
                                <img src="{{ asset('backend/assets/images/forgot-password-frent-img.jpg') }}"  width="150%"  height="100%" alt="Illustration">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Wrapper -->

<script>
    // Set the time remaining (in seconds)
    let timeRemaining = {{ $expires_in }};
    const timerElement = document.getElementById('timer');
    const resendElement = document.getElementById('resend-code');

    // Update the timer every second
    const countdown = setInterval(function() {
        if (timeRemaining <= 0) {
            clearInterval(countdown);
            timerElement.textContent = "Code expired";
            resendElement.style.display = 'block'; // Show resend link
        } else {
            timerElement.textContent = timeRemaining + " seconds";
            timeRemaining--;
        }
    }, 1000);
</script>

</body>

</html>
