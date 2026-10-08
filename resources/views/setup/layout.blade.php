<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>eBoard Setup Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root {
            --setup-primary: #C8102E;
            --setup-accent: #7AC143;
            --setup-bg: #f0f2f5;
        }
        body { background: var(--setup-bg); min-height: 100vh; }
        .setup-container { max-width: 800px; margin: 0 auto; padding: 2rem 1rem; }
        .setup-header { text-align: center; margin-bottom: 2rem; }
        .setup-header h1 { color: var(--setup-primary); font-size: 1.75rem; font-weight: 700; }
        .setup-header p { color: #6c757d; }
        .setup-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); padding: 2rem; }
        .step-indicator { display: flex; justify-content: center; gap: 0.25rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .step-item { display: flex; align-items: center; gap: 0.25rem; }
        .step-circle {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem; font-weight: 600;
            border: 2px solid #dee2e6; color: #6c757d; background: #fff;
            transition: all 0.3s;
        }
        .step-circle.active { background: var(--setup-primary); color: #fff; border-color: var(--setup-primary); }
        .step-circle.completed { background: var(--setup-accent); color: #fff; border-color: var(--setup-accent); }
        .step-label { font-size: 0.7rem; color: #6c757d; max-width: 80px; text-align: center; display: none; }
        .step-connector { width: 30px; height: 2px; background: #dee2e6; margin: 0 2px; align-self: center; }
        .step-connector.completed { background: var(--setup-accent); }
        .btn-setup { background: var(--setup-primary); color: #fff; border: none; padding: 0.6rem 2rem; border-radius: 8px; font-weight: 600; }
        .btn-setup:hover { background: #1a1a1a; color: #fff; }
        .btn-setup-accent { background: var(--setup-accent); color: #fff; border: none; padding: 0.6rem 2rem; border-radius: 8px; font-weight: 600; }
        .btn-setup-accent:hover { background: #5f9a2f; color: #fff; }
        .form-label { font-weight: 600; color: #344054; font-size: 0.875rem; }
        .form-control:focus { border-color: var(--setup-primary); box-shadow: 0 0 0 0.15rem rgba(200, 16, 46,0.15); }
        .section-title { font-size: 1.25rem; font-weight: 700; color: var(--setup-primary); margin-bottom: 0.5rem; }
        .section-subtitle { color: #6c757d; margin-bottom: 1.5rem; font-size: 0.9rem; }
        @media (min-width: 768px) { .step-label { display: block; } }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1><i class="mdi mdi-clipboard-check-outline"></i> eBoard Setup Wizard</h1>
            <p>Configure your eBoard system for your institution</p>
        </div>

        @if(isset($currentStep))
        <div class="step-indicator">
            @foreach($steps as $num => $label)
                <div class="step-item">
                    <div class="text-center">
                        <div class="step-circle {{ $num < $currentStep ? 'completed' : ($num == $currentStep ? 'active' : '') }}">
                            @if($num < $currentStep)
                                <i class="mdi mdi-check"></i>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <div class="step-label">{{ $label }}</div>
                    </div>
                </div>
                @if($num < count($steps))
                    <div class="step-connector {{ $num < $currentStep ? 'completed' : '' }}"></div>
                @endif
            @endforeach
        </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="setup-card">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
