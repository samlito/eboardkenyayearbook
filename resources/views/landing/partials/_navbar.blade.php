<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            @if(setting('logo_main'))
                <img src="{{ asset(setting('logo_main')) }}" alt="{{ setting('institution_acronym', 'ORG') }}" height="48">
            @endif
            <span class="brand-text {{ setting('logo_main') ? 'd-none d-sm-inline' : '' }}">
                <small>{{ setting('institution_acronym', 'ORG') }}</small>
                <span>eBoard System</span>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Home</a>
                </li>
                @if(setting('landing_show_features', true))
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                @endif
                @if(setting('landing_show_board_members', false) && count($boardMembers ?? []) > 0)
                    <li class="nav-item">
                        <a class="nav-link" href="#board">Board</a>
                    </li>
                @endif
                @if (Route::has('login'))
                    @auth
                        <li class="nav-item ms-lg-2">
                            <a href="{{ url('/dashboard') }}" class="btn btn-accent px-4 py-2">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-2">
                            <a href="{{ url('/login') }}" class="btn btn-brand px-4 py-2">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Log in to eBoard</span>
                            </a>
                        </li>
                    @endauth
                @endif
            </ul>
        </div>
    </div>
</nav>
