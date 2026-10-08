<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Access Restricted | {{ setting('institution_name', 'eBoard System') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Access Denied - Contact System Admin" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App CSS -->
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style"/>
    <!-- Icons -->
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

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
        .restricted-container {
            text-align: center;
            max-width: 500px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
        }
        .restricted-container img {
            width: 120px;
            margin-bottom: 20px;
        }
        .restricted-container h2 {
            font-size: 28px;
            margin-bottom: 15px;
        }
        .restricted-container p {
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
    </style>
</head>
<body>

<div class="restricted-container">
    <img src="{{ asset('backend/assets/images/restricted.png') }}" alt="Restricted">
    <h2>Access Denied</h2>
    <p>Your account is currently inactive, suspended, or disabled. Please contact the system administrator for assistance.</p>
    <a href="{{ route('login') }}" class="btn btn-custom">Return to Login</a>
</div>

</body>
</html>
