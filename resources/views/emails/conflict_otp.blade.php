{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <title>Conflict of Interest Declaration Confirmation</title>--}}
{{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">--}}

{{--    <style>--}}
{{--        body {--}}
{{--            font-family: Arial, sans-serif;--}}
{{--            background-color: #f8f9fa;--}}
{{--            padding: 30px;--}}
{{--        }--}}
{{--        .card {--}}
{{--            max-width: 600px;--}}
{{--            margin: auto;--}}
{{--            border-radius: 12px;--}}
{{--            box-shadow: 0 2px 8px rgba(0,0,0,0.1);--}}
{{--            border: none;--}}
{{--        }--}}
{{--        .card-header {--}}
{{--            background-color: #e00214;--}}
{{--            color: white;--}}
{{--            font-size: 18px;--}}
{{--            font-weight: bold;--}}
{{--            text-align: center;--}}
{{--            border-radius: 12px 12px 0 0;--}}
{{--            padding: 20px;--}}
{{--        }--}}
{{--        .card-body {--}}
{{--            padding: 25px;--}}
{{--            color: #333;--}}
{{--        }--}}
{{--        .btn-primary {--}}
{{--            background-color: #e00214;--}}
{{--            border-color: #e00214;--}}
{{--            border-radius: 6px;--}}
{{--            padding: 12px 24px;--}}
{{--            font-size: 16px;--}}
{{--            font-weight: bold;--}}
{{--            text-decoration: none;--}}
{{--            display: inline-block;--}}
{{--        }--}}
{{--        .btn-primary:hover {--}}
{{--            background-color: #202020;--}}
{{--        }--}}
{{--        .footer {--}}
{{--            margin-top: 30px;--}}
{{--            font-size: 13px;--}}
{{--            text-align: center;--}}
{{--            color: #6c757d;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<div class="card">--}}
{{--    <div class="card-header">--}}
{{--        Conflict of Interest Declaration Confirmation--}}
{{--    </div>--}}
{{--    <div class="card-body">--}}
{{--        <p>Dear <strong>{{ auth()->user()->name }}</strong>,</p>--}}

{{--        <p>--}}
{{--            You submitted a <strong>Conflict of Interest Declaration</strong> for the board meeting:--}}
{{--        </p>--}}

{{--        <p>--}}
{{--            <strong>{{ $meeting->title }}</strong><br>--}}
{{--            📅 Date: {{ $meeting->date?->format('l, d M Y') }}--}}
{{--        </p>--}}

{{--        <p>--}}
{{--            To confirm your declaration, please click the button below:--}}
{{--        </p>--}}

{{--        <p class="text-center">--}}
{{--            <a href="{{ route('conflict.verify', [$declaration->id, $otp]) }}" class="btn btn-primary">--}}
{{--                ✅ Confirm Declaration--}}
{{--            </a>--}}
{{--        </p>--}}

{{--        <p class="mt-4">--}}
{{--            ⚠️ This OTP link is unique to your declaration. If you did not submit this declaration, please--}}
{{--            notify the Secretariat immediately.--}}
{{--        </p>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="footer">--}}
{{--    &copy; {{ date('Y') }} Board Secretariat. All rights reserved.--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ setting('institution_acronym', 'eBoard') }} - Conflict of Interest Declaration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 30px;
        }
        .email-wrapper {
            max-width: 600px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
            background: #ffffff;
        }
        .card-body {
            padding: 25px;
            color: #333;
        }
        .otp-box {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 6px;
            text-align: center;
            background: #f1f1f1;
            border-radius: 8px;
            padding: 15px;
            margin: 20px auto;
            width: 220px;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    @include('emails.partials._header')
    <div class="card-body">
        <p>Dear <strong>{{ auth()->user()->name }}</strong>,</p>

        <p>
            You submitted a <strong>Conflict of Interest Declaration</strong> for the board meeting:
        </p>

        <p>
            <strong>{{ $meeting->title }}</strong><br>
            📅 Date: {{ $meeting->date?->format('l, d M Y') }}
        </p>

        <p>
            Please confirm your declaration by entering the following OTP:
        </p>

        <div class="otp-box">
            {{ $otp }}
        </div>

        <p class="text-center mt-4">
            Or click the button below to verify automatically:
        </p>

{{--        <p class="text-center">--}}
{{--            <a href="{{ route('conflict.verify.form', $declaration->id) }}" class="btn btn-primary">--}}
{{--                ✅ Verify Declaration--}}
{{--            </a>--}}
{{--        </p>--}}

        <p class="mt-4">
            ⚠️ This OTP is valid for a limited time and unique to your declaration.
            If you did not submit this declaration, please notify the Secretariat immediately.
        </p>
    </div>
    @include('emails.partials._footer')
</div>
</body>
</html>
