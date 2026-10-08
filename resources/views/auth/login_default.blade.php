<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Log In Page | {{ setting('institution_name', 'eBoard System') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon-32x32.png')}}">

    <!-- Bootstrap css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="{{ asset('backend/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-style"/>
    <!-- icons -->
    <link href="{{ asset('backend/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Head js -->
    <script src="{{ asset('backend/assets/js/head.js')}}"></script>

    <!-- Additional custom styles -->
    <style>
        .auth-fluid {
            display: flex;
            height: 100vh;
        }
        .auth-fluid-form-box {
            width: 50%;
            padding: 30px;
        }
        .auth-fluid-right {
            width: 50%;
            background-image: url('backend/assets/images/covet.jpg'); /* replace with your image path */
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .auth-user-testimonial {
            color: white;
            text-align: center;
        }
    </style>

</head>

<body class="auth-fluid-pages pb-0">

<div class="auth-fluid">
    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="p-3">

                <!-- Logo -->
                <div class="auth-brand text-center">
                    <div class="auth-logo">
                        <a href="index.html" class="logo logo-dark">
                            <span class="logo-lg">
                                <img src="{{ asset(setting('logo_main', 'backend/assets/images/logo.png')) }}" alt="{{ setting('institution_acronym', 'ORG') }}" height="50">
                            </span>
                        </a>
                    </div>
                </div>

                <!-- title-->
                <h4 class="mt-0">Sign In</h4>
                <p class="text-muted mb-4">Enter your email address and password to access your account.</p>

                <!-- form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input class="form-control" type="email" @error('email') is-invalid @enderror name="email" id="email" required="" placeholder="Enter your email">
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" @error('password')is-invalid @enderror name="password" class="form-control" placeholder="Enter your password">

                            <div class="input-group-text" data-password="false">
                                <span class="password-eye"></span>
                            </div>
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                            <label class="form-check-label" for="checkbox-signin">Remember me</label>
                        </div>
                    </div>

                    <div class="text-center d-grid">
                        <button class="btn btn-primary" type="submit"> Log In </button>
                    </div>

                </form>
                <!-- end form-->

                <!-- Footer-->
                <footer class="footer footer-alt">
                    <p class="text-muted">Don't have an account? <a href="auth-register-2.html" class="text-muted ms-1"><b>Sign Up</b></a></p>
                </footer>

            </div> <!-- end .card-body -->
        </div> <!-- end .align-items-center.d-flex.h-100-->
    </div>
    <!-- end auth-fluid-form-box-->

    <!-- Auth fluid right content -->
    <div class="auth-fluid-right">
        <div class="auth-user-testimonial">
            <h2 style="color:darkblue">{{ setting('institution_name', 'eBoard System') }}</h2>
            <p class="lead"><i class="mdi mdi-format-quote-open"></i>
                {{ setting('institution_tagline', 'Board Management System') }}
                <i class="mdi mdi-format-quote-close"></i>
            </p>
        </div> <!-- end auth-user-testimonial-->
    </div>
    <!-- end Auth fluid right content -->
</div>
<!-- end auth-fluid-->

<!-- Vendor js -->
<script src="{{ asset('backend/assets/js/vendor.min.js')}}"></script>

<!-- App js -->
<script src="{{ asset('backend/assets/js/app.min.js')}}"></script>

</body>
</html>

{{--    <!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <title>TWWDA eBoard Login</title>--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    @vite('resources/css/app.css')--}}
{{--    <style>--}}
{{--        body {--}}
{{--            background-color: #fafafa;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body class="min-h-screen bg-gradient-to-br from-[#252525] to-[#89C74C] flex items-center justify-center">--}}

{{--<div class="w-full max-w-6xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden grid md:grid-cols-2">--}}

{{--    <!-- Left Branding Panel -->--}}
{{--    <div class="hidden md:flex flex-col justify-between p-10 bg-[#C8102E] text-white relative">--}}
{{--        <div>--}}
{{--            <img src="https://tanawwda.go.ke/wp-content/uploads/2021/04/banner.png" alt="TWWDA Logo" class="h-16 mb-6">--}}
{{--            <h2 class="text-3xl font-bold leading-tight mb-4">Welcome to the eBoard System</h2>--}}
{{--            <p class="text-sm leading-relaxed text-gray-100">--}}
{{--                The Tana Water Works Development Agency (TWWDA) eBoard system empowers transparency, collaboration,--}}
{{--                and timely decision-making. Log in to manage meetings, access papers, and engage securely from anywhere.--}}
{{--            </p>--}}
{{--        </div>--}}
{{--        <div class="text-xs text-gray-300 mt-10">&copy; {{ now()->year }} TWWDA. All rights reserved.</div>--}}
{{--    </div>--}}

{{--    <!-- Login Panel -->--}}
{{--    <div class="p-10 bg-white flex flex-col justify-center">--}}
{{--        <div class="mb-8 text-center">--}}
{{--            <h2 class="text-2xl font-bold text-[#252525]">Sign In</h2>--}}
{{--            <p class="text-sm text-gray-500 mt-2">Access your TWWDA Board Portal</p>--}}
{{--        </div>--}}

{{--        <form method="POST" action="{{ route('login') }}" class="space-y-6">--}}
{{--            @csrf--}}

{{--            <div>--}}
{{--                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>--}}
{{--                <input type="email" name="email" id="email" required autofocus--}}
{{--                       class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-[#89C74C] focus:border-[#89C74C] focus:outline-none">--}}
{{--                @error('email')--}}
{{--                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>--}}
{{--                @enderror--}}
{{--            </div>--}}

{{--            <div>--}}
{{--                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>--}}
{{--                <input type="password" name="password" id="password" required--}}
{{--                       class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-[#89C74C] focus:border-[#89C74C] focus:outline-none">--}}
{{--                @error('password')--}}
{{--                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>--}}
{{--                @enderror--}}
{{--            </div>--}}

{{--            <div class="flex justify-between items-center text-sm">--}}
{{--                <label class="flex items-center space-x-2">--}}
{{--                    <input type="checkbox" class="rounded border-gray-300" checked>--}}
{{--                    <span>Remember me</span>--}}
{{--                </label>--}}
{{--                <a href="#" class="text-[#252525] hover:underline">Forgot password?</a>--}}
{{--            </div>--}}

{{--            <button type="submit"--}}
{{--                    class="w-full py-2 px-4 bg-[#C8102E] hover:bg-[#1e1e1e] text-white font-semibold rounded-lg shadow-md transition-all duration-200">--}}
{{--                Log In--}}
{{--            </button>--}}
{{--        </form>--}}

{{--        <p class="text-center text-sm text-gray-500 mt-6">--}}
{{--            Don’t have an account?--}}
{{--            <a href="#" class="text-[#89C74C] hover:underline">Request Access</a>--}}
{{--        </p>--}}
{{--    </div>--}}
{{--</div>--}}

{{--</body>--}}
{{--</html>--}}



