<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | {{ setting('institution_acronym', 'eBoard') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: {{ setting('primary_color', '#1a1a1a') }};
        }
        body {
            background: linear-gradient(135deg, #f4f4f4 0%, #e9e9e9 100%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .verify-card {
            max-width: 520px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            animation: slideUp 0.6s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .verify-header {
            background: var(--brand);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .verify-header .icon-circle {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .verify-header .icon-circle i {
            font-size: 2rem;
        }
        .verify-header h4 {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .verify-header p {
            opacity: 0.85;
            font-size: 0.9rem;
            margin: 0;
        }
        .verify-body {
            padding: 2rem;
        }
        .verify-body .info-text {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        .verify-body .email-highlight {
            font-weight: 600;
            color: var(--brand);
        }
        .btn-verify {
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: 0.3s;
        }
        .btn-verify:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            color: #fff;
        }
        .alert-success-custom {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .tips-list {
            list-style: none;
            padding: 0;
            margin: 1rem 0 1.5rem;
        }
        .tips-list li {
            padding: 0.4rem 0;
            color: #666;
            font-size: 0.88rem;
            display: flex;
            align-items: start;
            gap: 0.5rem;
        }
        .tips-list li i {
            color: var(--brand);
            margin-top: 3px;
        }
        .divider {
            border-top: 1px solid #eee;
            margin: 1.5rem 0;
        }
        .verify-footer {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="verify-header">
            @if(setting('logo_main'))
                <img src="{{ asset(setting('logo_main')) }}" alt="{{ setting('institution_acronym', 'ORG') }}" height="50" class="mb-2">
            @else
                <div class="icon-circle">
                    <i class="bi bi-envelope-check"></i>
                </div>
            @endif
            <h4>Verify Your Email</h4>
            <p>{{ setting('institution_name', 'eBoard System') }}</p>
        </div>

        <div class="verify-body">
            @if (session('status') == 'verification-link-sent')
                <div class="alert-success-custom mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <p class="info-text">
                Before you can access the dashboard, please verify your email address
                <span class="email-highlight">{{ auth()->user()->email ?? '' }}</span>
                by clicking the link we sent to your inbox.
            </p>

            <p class="info-text mb-1" style="font-weight: 600; color: #333;">Didn't get the email?</p>
            <ul class="tips-list">
                <li><i class="bi bi-check2"></i> Check your spam or junk folder</li>
                <li><i class="bi bi-check2"></i> Make sure your email address is correct</li>
                <li><i class="bi bi-check2"></i> Click the button below to resend</li>
            </ul>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-verify">
                    <i class="bi bi-arrow-repeat me-1"></i> Resend Verification Email
                </button>
            </form>

            <div class="divider"></div>

            <div class="verify-footer">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-muted text-decoration-none" style="font-size: 0.9rem;">
                        <i class="bi bi-box-arrow-left me-1"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
