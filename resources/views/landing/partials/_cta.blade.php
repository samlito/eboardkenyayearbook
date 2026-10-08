<section class="cta-section">
    <div class="container">
        <div class="cta-band reveal">
            <span class="accent-rule" style="background: linear-gradient(90deg, #fff, rgba(255,255,255,0.4));" aria-hidden="true"></span>
            <h2>
                {{ setting('landing_cta_title', "Join " . setting('institution_name', 'our') . "'s digital transformation journey") }}
            </h2>
            <p>
                {{ setting('landing_cta_subtitle', 'Experience the future of board governance with our comprehensive eBoard platform.') }}
            </p>
            <a href="{{ url('/login') }}" class="btn btn-light-solid">
                {{ setting('landing_cta_button_text', 'Get Started') ?: 'Get Started' }} <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
