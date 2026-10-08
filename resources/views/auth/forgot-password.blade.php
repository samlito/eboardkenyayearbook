<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - {{ setting('institution_acronym', 'eBoard') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #d7f4e3, #f2fdf7);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .forgot-container {
            max-width: 500px;
            margin: 4rem auto;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
        }

        .logo {
            width: 200px;
            margin-bottom: 1rem;
        }

        .btn-custom {
            background-color: #099139;
            color: #fff;
            font-weight: 600;
        }

        .btn-custom:hover {
            background-color: #077b2d;
        }

        .form-control:focus {
            border-color: #099139;
            box-shadow: 0 0 0 0.2rem rgba(9, 145, 57, 0.25);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="forgot-container text-center">
        <img src="{{ asset(setting('logo_main', 'backend/assets/images/logo.png')) }}" alt="{{ setting('institution_acronym', 'eBoard') }} Logo" class="logo img-fluid">
        <h3 class="mb-3">Forgot your password?</h3>
        <p class="text-muted mb-4">
            No problem. Enter your email address and we’ll send you a password reset link.
        </p>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3 text-start">
                <label for="email" class="form-label">Email address</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>

                @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-custom btn-lg">
                    Email Password Reset Link
                </button>
            </div>
        </form>

        <div class="mt-4">
            <a href="{{ route('login') }}" class="text-decoration-none text-success">
                ← Back to login
            </a>
        </div>
    </div>
</div>

</body>
</html>
