<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="theme-color" content="#111111" />
  <title>{{ setting('institution_acronym', 'eBoard') }} Login</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <style>
    :root {
      --red: #cc1f2b;
      --red-dark: #a3161f;
      --green: #23803a;
      --green-dark: #19662c;
      --green-deep: #054d1f;
      --black: #111111;
      --ink: #1f2937;
      --muted: #6b7280;
      --line: #e5e7eb;
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      min-height: 100vh;
      margin: 0;
      color: var(--ink);
      background: #f4f6f5;
      -webkit-font-smoothing: antialiased;
    }

    a { color: var(--green-dark); }
    a:hover { color: var(--green-deep); }

    .auth {
      display: grid;
      grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
      min-height: 100vh;
    }

    /* ===== Brand panel ===== */
    .brand {
      position: relative;
      overflow: hidden;
      color: #fff;
      padding: 3rem 3.5rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: var(--black) url('{{ asset('images/bg.jpg') }}') center / cover no-repeat;
    }
    .brand::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(60% 50% at 0% 100%, rgba(9, 145, 57, 0.45) 0%, transparent 70%),
        radial-gradient(55% 45% at 100% 0%, rgba(200, 16, 46, 0.5) 0%, transparent 70%),
        linear-gradient(160deg, rgba(17, 17, 17, 0.88) 0%, rgba(17, 17, 17, 0.94) 100%);
      backdrop-filter: grayscale(1);
      -webkit-backdrop-filter: grayscale(1);
    }
    .brand::after {
      content: "";
      position: absolute;
      left: 0; right: 0; bottom: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--red) 0%, var(--black) 50%, var(--green) 100%);
    }
    .brand > * { position: relative; z-index: 1; }

    .brand-top { display: flex; align-items: center; gap: 0.85rem; }
    .brand-logo {
      height: 56px;
      padding: 8px 12px;
      border-radius: 14px;
      background: #fff;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }
    .brand-acronym { font-weight: 800; font-size: 1.15rem; letter-spacing: 0.02em; }
    .brand-acronym small { display: block; font-weight: 500; font-size: 0.8rem; color: rgba(255,255,255,0.7); letter-spacing: 0.04em; }

    .brand-body { max-width: 30rem; padding: 3rem 0; }
    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: #fff;
      padding: 0.4rem 0.85rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.16);
    }
    .eyebrow .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--green); box-shadow: 0 0 0 3px rgba(9,145,57,0.3); }
    .brand h1 {
      font-size: clamp(1.9rem, 3.2vw, 2.75rem);
      font-weight: 800;
      line-height: 1.15;
      letter-spacing: -0.025em;
      margin: 1.25rem 0 1rem;
    }
    .brand h1 span {
      background: linear-gradient(90deg, #ff4d5e 0%, #3ddc78 100%);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    .brand p.lead { color: rgba(255, 255, 255, 0.82); font-size: 1.05rem; line-height: 1.65; }

    .perks { list-style: none; padding: 0; margin: 2rem 0 0; display: grid; gap: 0.9rem; }
    .perks li { display: flex; align-items: center; gap: 0.85rem; color: rgba(255,255,255,0.92); font-weight: 500; }
    .perks i {
      width: 38px; height: 38px;
      display: inline-flex; align-items: center; justify-content: center;
      border-radius: 11px;
      font-size: 1.1rem;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .perks li:nth-child(odd) i { color: #ff6b78; }
    .perks li:nth-child(even) i { color: #4ee38a; }

    .brand-foot { font-size: 0.85rem; color: rgba(255, 255, 255, 0.6); }

    /* ===== Form panel ===== */
    .panel {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem 2rem;
      background:
        radial-gradient(40% 30% at 100% 0%, rgba(9, 145, 57, 0.07) 0%, transparent 70%),
        radial-gradient(40% 30% at 0% 100%, rgba(200, 16, 46, 0.06) 0%, transparent 70%),
        #f7f8f7;
    }
    .form-card {
      width: 100%;
      max-width: 440px;
      background: #fff;
      border-radius: 22px;
      padding: 2.5rem 2.25rem;
      border: 1px solid rgba(17, 17, 17, 0.06);
      box-shadow: 0 1px 2px rgba(17,17,17,0.04), 0 20px 50px rgba(17,17,17,0.08);
      animation: rise 0.6s cubic-bezier(.2,.7,.2,1);
    }
    @keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

    .form-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.75rem; }
    .form-head h2 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.02em; color: var(--black); margin: 0 0 0.35rem; }
    .form-head p { color: var(--muted); margin: 0; font-size: 0.95rem; }
    .date-chip {
      flex-shrink: 0;
      font-size: 0.78rem;
      font-weight: 600;
      color: var(--green-deep);
      background: #e6f6ec;
      border-radius: 999px;
      padding: 0.35rem 0.7rem;
    }

    .form-label { font-weight: 600; font-size: 0.875rem; color: var(--ink); margin-bottom: 0.4rem; }

    .field { position: relative; }
    .field > .bi:first-child {
      position: absolute;
      left: 1rem; top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 1.05rem;
      pointer-events: none;
      transition: color 0.15s ease;
    }
    .field .form-control {
      height: 52px;
      padding-left: 2.75rem;
      border-radius: 12px;
      border: 1.5px solid var(--line);
      background: #fafafa;
      font-size: 0.98rem;
      color: var(--ink);
      transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }
    .field .form-control::placeholder { color: #9ca3af; }
    .field .form-control:focus {
      border-color: var(--green);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(9, 145, 57, 0.12);
    }
    .field:focus-within > .bi:first-child { color: var(--green); }
    .field .form-control.is-invalid { border-color: var(--red); background-image: none; }
    .field .form-control.is-invalid:focus { box-shadow: 0 0 0 4px rgba(200, 16, 46, 0.12); }
    .field.has-toggle .form-control { padding-right: 3rem; }
    .toggle-pass {
      position: absolute;
      right: 0.5rem; top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: transparent;
      width: 38px; height: 38px;
      border-radius: 10px;
      color: var(--muted);
      font-size: 1.15rem;
    }
    .toggle-pass:hover { background: #f1f1f1; color: var(--black); }

    .strength-meter {
      height: 5px;
      width: 0;
      border-radius: 4px;
      margin-top: 8px;
      transition: width 0.3s ease, background 0.3s ease;
    }
    .weak { background: var(--red); width: 33%; }
    .medium { background: #f59e0b; width: 66%; }
    .strong { background: var(--green); width: 100%; }

    .captcha {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .captcha-box {
      flex-shrink: 0;
      height: 52px;
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0 0.6rem 0 1rem;
      border-radius: 12px;
      background: var(--black);
      color: #fff;
      font-weight: 700;
      font-size: 1.05rem;
      letter-spacing: 0.04em;
      font-variant-numeric: tabular-nums;
    }
    .captcha-refresh {
      display: inline-flex; align-items: center; justify-content: center;
      width: 34px; height: 34px;
      border-radius: 9px;
      background: rgba(255,255,255,0.1);
      color: #4ee38a;
      cursor: pointer;
      transition: transform 0.4s ease, background 0.15s ease;
    }
    .captcha-refresh:hover { transform: rotate(180deg); background: rgba(255,255,255,0.18); }
    .captcha .field { flex: 1; }

    .form-check-input { width: 2.4em; height: 1.3em; cursor: pointer; }
    .form-check-input:checked { background-color: var(--green); border-color: var(--green); }
    .form-check-input:focus { box-shadow: 0 0 0 4px rgba(9, 145, 57, 0.15); border-color: var(--green); }
    .form-check-label { font-size: 0.9rem; color: var(--ink); cursor: pointer; padding-left: 0.25rem; }
    .forgot { font-size: 0.9rem; font-weight: 600; text-decoration: none; }

    .btn {
      height: 52px;
      font-weight: 700;
      font-size: 1rem;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }
    .btn-login {
      position: relative;
      color: #fff;
      border: 0;
      background: linear-gradient(135deg, #d4142f 0%, var(--red-dark) 100%);
      box-shadow: 0 8px 20px rgba(200, 16, 46, 0.28);
    }
    .btn-login:hover, .btn-login:focus { color: #fff; transform: translateY(-1px); box-shadow: 0 12px 26px rgba(200, 16, 46, 0.36); }
    .btn-login:active { transform: none; }
    .btn-reset {
      color: var(--green-deep);
      background: #fff;
      border: 1.5px solid #b8e6c8;
    }
    .btn-reset:hover { color: #fff; background: var(--green-dark); border-color: var(--green-dark); }

    .btn.loading { pointer-events: none; opacity: 0.9; }
    .btn.loading::after {
      content: "";
      position: absolute;
      right: 18px;
      top: 50%;
      width: 18px;
      height: 18px;
      margin-top: -9px;
      border: 2px solid #fff;
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .divider { display: flex; align-items: center; gap: 0.75rem; color: #9ca3af; font-size: 0.8rem; margin: 1rem 0; }
    .divider::before, .divider::after { content: ""; flex: 1; height: 1px; background: var(--line); }

    .alert-danger {
      border: 0;
      border-left: 4px solid var(--red);
      border-radius: 12px;
      background: #fdecee;
      color: #8a0b1f;
      font-weight: 500;
      display: flex;
      gap: 0.6rem;
      align-items: flex-start;
    }

    .secure-note { display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 1.5rem; font-size: 0.82rem; color: var(--muted); }
    .secure-note i { color: var(--green); }
    .footer-note { text-align: center; margin-top: 0.5rem; font-size: 0.8rem; color: #9ca3af; }

    .mobile-brand { display: none; }

    @media (max-width: 991.98px) {
      .auth { grid-template-columns: 1fr; }
      .brand { display: none; }
      .panel { padding: 2rem 1rem; min-height: 100vh; flex-direction: column; }
      .mobile-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        font-weight: 800;
        color: var(--black);
      }
      .mobile-brand img { height: 44px; }
      .mobile-brand .bar { width: 4px; height: 32px; border-radius: 4px; background: linear-gradient(180deg, var(--red), var(--green)); }
    }
    @media (max-width: 420px) {
      .form-card { padding: 1.75rem 1.25rem; border-radius: 18px; }
      .form-head { flex-direction: column-reverse; gap: 0.75rem; }
    }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation: none !important; transition: none !important; }
    }
  </style>
</head>

<body>
  <main class="auth">
    <!-- Brand panel -->
    <aside class="brand">
      <div class="brand-top">
        @if(setting('logo_main'))
          <img class="brand-logo" src="{{ asset(setting('logo_main')) }}" alt="{{ setting('institution_acronym', 'ORG') }}" />
        @endif
        <div class="brand-acronym">
          {{ setting('institution_acronym', 'ORG') }}
          <small>eBoard System</small>
        </div>
      </div>

      <div class="brand-body">
        <span class="eyebrow"><span class="dot"></span> Digital Board Governance</span>
        <h1>{{ setting('institution_name', 'eBoard System') }} <span>Board Portal</span></h1>
        <p class="lead">Meetings, board papers, resolutions and approvals — securely in one place.</p>
        <ul class="perks">
          <li><i class="bi bi-shield-lock"></i> Role-based, secure access</li>
          <li><i class="bi bi-file-earmark-text"></i> Board papers &amp; minutes on demand</li>
          <li><i class="bi bi-clipboard-check"></i> Full audit trail of every action</li>
        </ul>
      </div>

      <div class="brand-foot">© {{ date('Y') }} {{ setting('institution_name', 'eBoard System') }}</div>
    </aside>

    <!-- Form panel -->
    <section class="panel">
      <div class="mobile-brand">
        @if(setting('logo_main'))
          <img src="{{ asset(setting('logo_main')) }}" alt="{{ setting('institution_acronym', 'ORG') }}" />
        @else
          <span class="bar"></span>
        @endif
        <span>{{ setting('institution_acronym', 'ORG') }} eBoard</span>
      </div>

      <div class="form-card">
        <div class="form-head">
          <div>
            <h2>Welcome back</h2>
            <p>Sign in to your {{ setting('institution_acronym', 'ORG') }} eBoard account.</p>
          </div>
          <span class="date-chip" data-bs-toggle="tooltip" title="Today's date"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('d/m/Y') }}</span>
        </div>

        @if($errors->any())
          <div class="alert alert-danger fade show" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ $errors->first() }}</span>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
          @csrf

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <div class="field">
              <i class="bi bi-envelope"></i>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                     placeholder="name@example.com" required autocomplete="username" autofocus value="{{ old('email') }}">
            </div>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="field has-toggle">
              <i class="bi bi-lock"></i>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                     name="password" placeholder="Enter your password" required autocomplete="current-password">
              <button type="button" class="toggle-pass" onclick="togglePassword()" aria-label="Show or hide password">
                <i class="bi bi-eye-slash" id="toggleIcon"></i>
              </button>
            </div>
            <div id="strengthBar" class="strength-meter"></div>
            <small id="capsLockWarning" class="text-danger d-none"><i class="bi bi-exclamation-triangle-fill"></i> Caps Lock is ON</small>
          </div>

          <!-- Captcha -->
          <div class="mb-3">
            <label for="captchaAnswer" class="form-label">Security check</label>
            <div class="captcha">
              <div class="captcha-box">
                <span><span id="num1"></span> + <span id="num2"></span> =</span>
                <span class="captcha-refresh" onclick="generateCaptcha()" title="New question" role="button" aria-label="New question">
                  <i class="bi bi-arrow-clockwise"></i>
                </span>
              </div>
              <div class="field">
                <i class="bi bi-calculator"></i>
                <input type="number" id="captchaAnswer" class="form-control" placeholder="Answer" required>
              </div>
            </div>
          </div>

          <!-- Remember + Forgot -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="remember" name="remember">
              <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
          </div>

          <!-- Buttons -->
          <div class="d-grid">
            <button type="submit" id="loginBtn" class="btn btn-login">
              Sign in <i class="bi bi-arrow-right"></i>
            </button>
          </div>

          <div class="divider">or</div>

          <div class="d-grid">
            <a href="{{ route('password.request') }}" class="btn btn-reset"><i class="bi bi-key"></i> Reset password</a>
          </div>
        </form>

        <div class="secure-note"><i class="bi bi-shield-check"></i> Protected, encrypted connection</div>
      </div>

      <div class="footer-note">
        © {{ date('Y') }} {{ setting('institution_name', 'eBoard System') }} — All rights reserved | v1.0
      </div>
    </section>
  </main>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

    // Toggle password
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      const toggleIcon = document.getElementById("toggleIcon");
      passwordInput.type = passwordInput.type === "password" ? "text" : "password";
      toggleIcon.classList.toggle("bi-eye");
      toggleIcon.classList.toggle("bi-eye-slash");
    }

    // Captcha
    let correctAnswer = 0;
    function generateCaptcha() {
      const a = Math.floor(Math.random() * 20) + 1;
      const b = Math.floor(Math.random() * 20) + 1;
      document.getElementById("num1").textContent = a;
      document.getElementById("num2").textContent = b;
      correctAnswer = a + b;
    }
    window.onload = generateCaptcha;

    // Password Strength
    const passwordInput = document.getElementById("password");
    const strengthBar = document.getElementById("strengthBar");
    passwordInput.addEventListener("input", function () {
      const val = passwordInput.value;
      let strength = 0;
      if (val.match(/[a-z]/)) strength++;
      if (val.match(/[A-Z]/)) strength++;
      if (val.match(/[0-9]/)) strength++;
      if (val.match(/[$@#&!]/)) strength++;
      if (val.length >= 8) strength++;

      strengthBar.className = "strength-meter";
      if (!val) return;
      if (strength <= 2) strengthBar.classList.add("weak");
      else if (strength === 3) strengthBar.classList.add("medium");
      else strengthBar.classList.add("strong");
    });

    // Caps Lock warning
    passwordInput.addEventListener("keyup", function (event) {
      const capsWarning = document.getElementById("capsLockWarning");
      capsWarning.classList.toggle("d-none", !event.getModifierState("CapsLock"));
    });

    // Loading spinner
    document.getElementById("loginBtn").addEventListener("click", function () {
      this.classList.add("loading");
    });
  </script>
</body>
</html>
