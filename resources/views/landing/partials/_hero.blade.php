@php($showFeaturesLink = setting('landing_show_features', true))
<section class="hero text-center" id="home">
    <div class="hero-bg" aria-hidden="true"></div>
    <div class="hero-overlay" aria-hidden="true"></div>

    <div class="container">
        <div class="hero-content">
            <div class="reveal">
                <span class="hero-eyebrow">
                    <span class="dot" aria-hidden="true"></span>
                    {{ setting('institution_acronym', 'ORG') }} &middot; Digital Board Governance
                </span>
            </div>

            @if(count($slides) > 0)
            <div id="heroCarousel" class="carousel slide carousel-fade reveal {{ count($slides) > 1 ? 'has-indicators' : '' }}" data-bs-ride="carousel" data-bs-interval="6000" style="transition-delay: .1s;">
                <div class="carousel-inner">
                    @foreach($slides as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <h1 class="hero-title">{{ $slide['title'] ?? 'Welcome' }}</h1>
                        @if(!empty($slide['subtitle']))
                            <p class="hero-subtitle">{{ $slide['subtitle'] }}</p>
                        @endif
                        <div class="hero-actions">
                            <a href="{{ $slide['button_url'] ?? '/login' }}" class="btn btn-brand btn-lg-pill">
                                {{ $slide['button_text'] ?? 'Get Started' }} <i class="bi bi-arrow-right"></i>
                            </a>
                            @if($showFeaturesLink)
                                <a href="#features" class="btn btn-ghost-light btn-lg-pill">
                                    Explore features <i class="bi bi-arrow-down"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @if(count($slides) > 1)
                <div class="carousel-indicators">
                    @foreach($slides as $index => $slide)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                                class="{{ $index === 0 ? 'active' : '' }}" @if($index === 0) aria-current="true" @endif
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                @endif
            </div>
            @else
            <div class="reveal" style="transition-delay: .1s;">
                <h1 class="hero-title">Digital Governance Made Seamless</h1>
                <p class="hero-subtitle">
                    Welcome to the {{ setting('institution_acronym', 'ORG') }} eBoard System
                </p>
                <div class="hero-actions">
                    <a href="{{ url('/login') }}" class="btn btn-brand btn-lg-pill">
                        Log in to eBoard <i class="bi bi-arrow-right"></i>
                    </a>
                    @if($showFeaturesLink)
                        <a href="#features" class="btn btn-ghost-light btn-lg-pill">
                            Explore features <i class="bi bi-arrow-down"></i>
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <ul class="hero-trust reveal" style="transition-delay: .2s;">
                <li><i class="bi bi-shield-lock"></i>Role-based access</li>
                <li><i class="bi bi-phone"></i>OTP verification</li>
                <li><i class="bi bi-journal-check"></i>Full audit trail</li>
            </ul>
        </div>
    </div>
</section>
