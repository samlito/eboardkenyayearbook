<section class="section" id="features">
    <div class="container">
        <div class="section-head reveal">
            <span class="section-kicker">Platform</span>
            <h2 class="section-title">Why {{ setting('institution_acronym', 'ORG') }} eBoard?</h2>
            <p class="section-lead">A comprehensive platform for modern board governance</p>
        </div>

        <div class="row g-4">
            @forelse($features as $index => $feature)
            <div class="col-md-6 col-lg-4 reveal" style="transition-delay: {{ ($index % 3) * 0.1 }}s;">
                <div class="feature-card {{ $index % 2 === 1 ? 'is-green' : '' }}">
                    <div class="feature-icon" aria-hidden="true">
                        <i class="{{ $feature['icon'] ?? 'mdi mdi-star-outline' }}"></i>
                    </div>
                    <h3>{{ $feature['title'] ?? 'Feature' }}</h3>
                    <p>{{ $feature['description'] ?? '' }}</p>
                </div>
            </div>
            @empty
            <div class="col-md-6 col-lg-4 reveal">
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true"><i class="mdi mdi-calendar-check"></i></div>
                    <h3>Meeting Management</h3>
                    <p>Schedule, track, and manage board meetings with an intuitive digital almanac.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal" style="transition-delay: .1s;">
                <div class="feature-card is-green">
                    <div class="feature-icon" aria-hidden="true"><i class="mdi mdi-file-document-outline"></i></div>
                    <h3>Board Documents</h3>
                    <p>Securely upload, review, and share board papers and supporting documents.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal" style="transition-delay: .2s;">
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true"><i class="mdi mdi-shield-lock-outline"></i></div>
                    <h3>Secure Governance</h3>
                    <p>Role-based access control, OTP verification, and comprehensive audit trails.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>
