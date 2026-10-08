<footer class="site-footer">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <div class="col-md-5 col-lg-4">
                <h5 class="fw-bold mb-3">{{ setting('institution_acronym', 'ORG') }} eBoard</h5>
                <p class="mb-0">{{ setting('institution_tagline', 'Digital Board Governance Platform') }}</p>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-5">
                <h6>Contact</h6>
                <ul class="list-unstyled footer-contact mb-0">
                    @if(setting('physical_address'))
                        <li><i class="bi bi-geo-alt"></i><span>{{ setting('physical_address') }}</span></li>
                    @endif
                    @if(setting('postal_address'))
                        <li><i class="bi bi-mailbox"></i><span>{{ setting('postal_address') }}</span></li>
                    @endif
                    @if(setting('phone_primary'))
                        <li><i class="bi bi-telephone"></i><a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('phone_primary')) }}">{{ setting('phone_primary') }}</a></li>
                    @endif
                    @if(setting('email_info'))
                        <li><i class="bi bi-envelope"></i><a href="mailto:{{ setting('email_info') }}">{{ setting('email_info') }}</a></li>
                    @endif
                </ul>
            </div>
            <div class="col-sm-6 col-md-3">
                <h6>Quick Links</h6>
                <ul class="list-unstyled footer-links mb-0">
                    <li><a href="{{ url('/login') }}"><i class="bi bi-arrow-right me-2"></i>Login</a></li>
                    @if(setting('website_url'))
                        <li><a href="{{ setting('website_url') }}" target="_blank" rel="noopener"><i class="bi bi-arrow-right me-2"></i>Official Website</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <p class="mb-0">&copy; {{ date('Y') }} {{ setting('institution_name', 'eBoard System') }}. All rights reserved.</p>
        </div>
    </div>
</footer>
