<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ setting('institution_acronym', 'eBoard') }} OTP Verification</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>

    <style>
        :root {
            --primary-color: #9B0C23;
            --accent-color: #76b82a;
            --danger-color: #dc3545;
            --gray-light: #fafafa;
        }
        body {
            background: linear-gradient(135deg, #2b2b2b 0%, #0c4b33 100%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .otp-container {
            max-width: 850px;
            width: 100%;
        }
        .otp-card {
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            animation: fadeIn 0.9s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .otp-left {
            flex: 1 1 300px;
            background: var(--gray-light);
            padding: 2rem 1.5rem;
            text-align: center;
            border-right: 5px solid var(--primary-color);
        }
        .otp-left img { max-height: 80px; margin-bottom: 1rem; }
        .otp-left h6 { font-size: 1rem; font-weight: 700; color: var(--primary-color); }

        .otp-right {
            flex: 2 1 400px;
            padding: 3rem 2rem;
        }
        .otp-right h4 { font-weight: 700; color: var(--primary-color); margin-bottom: 0.5rem; }
        .otp-input {
            font-size: 1.8rem;
            text-align: center;
            border-radius: 12px;
            border: 1px solid #dcdcdc;
            padding: 1rem;
            font-weight: bold;
            letter-spacing: 0.6rem;
        }
        .btn-verify {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.9rem;
            transition: 0.3s;
        }
        .btn-verify:hover { background-color: #111111; }
        .btn-resend {
            background-color: var(--accent-color);
            color: #fff;
            border-radius: 12px;
            font-weight: 600;
            padding: 0.9rem;
        }
        .footer-note {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #777;
        }

        /* Mobile tweaks */
        @media (max-width: 768px) {
            .otp-card {
                flex-direction: column;
            }
            .otp-left {
                border-right: none;
                border-bottom: 5px solid var(--primary-color);
            }
            .otp-right {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="otp-container">
    <div class="otp-card">
        <!-- Left -->
        <div class="otp-left">
            <img src="{{ asset(setting('logo_main', 'backend/assets/images/logo.png')) }}" alt="{{ setting('institution_acronym', 'eBoard') }} Logo"/>
            <h6>{{ setting('institution_name', 'eBoard System') }}</h6>
        </div>

        <!-- Right -->
        <div class="otp-right">
            <h4 class="mb-1">OTP Verification</h4>
            <p class="text-muted mb-4">Enter the 6-digit code sent to your email to complete login.</p>

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" style="border-left: 4px solid #0fee5d; border-radius: 10px;">
                    <i class="bi bi-info-circle-fill me-1"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('message'))
                <div class="alert alert-success alert-dismissible fade show" style="border-left: 4px solid #198754; border-radius: 10px;">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('dev_otp'))
                <div class="alert alert-warning alert-dismissible fade show" style="border-left: 4px solid #ffc107; border-radius: 10px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Dev Mode:</strong> OTP: <strong style="font-size: 1.2rem; letter-spacing: 3px;">{{ session('dev_otp') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 10px;">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}">
                @csrf
                <div class="mb-4">
                    <input type="text" maxlength="6" name="otp" class="form-control otp-input @error('otp') is-invalid @enderror" placeholder="______" required>
                    @error('otp')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-verify">Verify OTP</button>
                </div>

                <div class="d-grid">
                    <a href="{{ route('otp.resend') }}" class="btn btn-resend">Resend OTP</a>
                </div>
            </form>

            <div class="footer-note">
                © {{ date('Y') }} {{ setting('institution_name', 'eBoard System') }} — All rights reserved
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
