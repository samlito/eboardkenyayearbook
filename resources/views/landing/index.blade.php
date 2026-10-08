<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111111">
    <meta name="description" content="{{ setting('institution_name', 'eBoard System') }} — {{ setting('institution_tagline', 'Digital Board Governance Platform') }}">
    <script>document.documentElement.classList.add('js');</script>
    <title>{{ setting('institution_acronym', 'ORG') }} eBoard System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    @if(setting('favicon'))
        <link rel="icon" href="{{ asset(setting('favicon')) }}" type="image/x-icon">
    @endif

    @include('landing.partials._styles')
</head>
<body>
    <a href="#home" class="back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></a>

    @include('landing.partials._navbar')
    @include('landing.partials._hero', ['slides' => $heroSlides])

    @if(setting('landing_show_features', true))
        @include('landing.partials._features', ['features' => $features])
    @endif

    @if(setting('landing_show_board_members', false) && count($boardMembers) > 0)
        @include('landing.partials._board', ['members' => $boardMembers])
    @endif

    @include('landing.partials._cta')
    @include('landing.partials._footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            var navbar = document.getElementById('mainNav');
            var backToTop = document.querySelector('.back-to-top');

            function onScroll() {
                var scrolled = window.scrollY > 40;
                if (navbar) navbar.classList.toggle('navbar-scrolled', scrolled);
                if (backToTop) backToTop.classList.toggle('active', window.scrollY > 400);
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();

            // Close the mobile menu after picking a link
            document.querySelectorAll('#navbarNav a').forEach(function (link) {
                link.addEventListener('click', function () {
                    var menu = document.getElementById('navbarNav');
                    if (menu && menu.classList.contains('show') && window.bootstrap) {
                        bootstrap.Collapse.getOrCreateInstance(menu).hide();
                    }
                });
            });

            // Subtle reveal-on-scroll
            var items = document.querySelectorAll('.reveal');
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce || !('IntersectionObserver' in window)) {
                items.forEach(function (el) { el.classList.add('is-visible'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            items.forEach(function (el) { io.observe(el); });
        })();
    </script>
</body>
</html>
