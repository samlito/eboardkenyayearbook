<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>2FA Verification | {{ setting('institution_name', 'eBoard System') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Verify your 2FA code to access the system" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <style>
        body {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to right, #191919, #c42330);
            color: white;
            font-family: 'Arial', sans-serif;
        }
        .verification-container {
            text-align: center;
            max-width: 500px;
            padding: 40px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
        }
        .verification-container h2 {
            font-size: 28px;
            margin-bottom: 15px;
        }
        .verification-container p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        .btn-custom {
            background: #f39c12;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: #e67e22;
        }
        .verification-boxes {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .form-control {
            font-size: 1.5rem;
            text-align: center;
            padding: 1rem;
            height: 60px;
            border-radius: 8px;
            border: 2px solid #ccc;
            background-color: #f3f3f3;
            width: 60px;
        }
        .disabled {
            background-color: #ccc;
            pointer-events: none;
        }
        .countdown-timer {
            font-size: 20px;
            margin-top: 20px;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="verification-container">
    <h2>Enter Verification Code</h2>
    <p>We've sent a 5-digit code to your email. Please check your inbox and enter the code below.</p>

    <!-- Displaying error message (if any) -->
    @if(session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    <!-- Display validation error messages (if any) -->
    <div class="error-message">
        @if($errors->any())
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        @endif
    </div>

    <!-- 2FA Form -->
    <form method="POST" action="{{ route('verify.code') }}">
        @csrf
        <div class="verification-boxes">
            @for($i = 0; $i < 5; $i++)
                <input type="text" id="verification_code_{{ $i }}" name="verification_code_{{ $i }}" class="form-control" maxlength="1" required pattern="\d" inputmode="numeric">
            @endfor
        </div>

        <div class="countdown-timer">
            Time remaining: <span id="countdown">90</span> seconds
        </div>

        <button type="submit" class="btn btn-custom btn-lg w-100 mt-4">Verify Code</button>
    </form>

    <!-- Resend code button (disabled by default) -->
    <button type="button" id="resend-btn" class="btn btn-custom mt-3 disabled w-100" disabled>Resend Code</button>
</div>

<script>
    let countdownElement = document.getElementById('countdown');
    let resendBtn = document.getElementById('resend-btn');

    // Fetch the remaining time from session (in seconds)
    let expiresAt = new Date("{{ session('expires_at')->toDateString() }}");

    let secondsRemaining = Math.max(0, Math.floor((expiresAt - new Date()) / 1000));

    // Countdown timer logic
    const countdownInterval = setInterval(() => {
        secondsRemaining--;
        countdownElement.textContent = secondsRemaining;

        // Enable resend button when timer reaches 0
        if (secondsRemaining <= 0) {
            clearInterval(countdownInterval);
            resendBtn.disabled = false;
            resendBtn.classList.remove('disabled');
        }
    }, 1000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
