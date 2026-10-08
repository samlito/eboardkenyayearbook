<footer class="footer">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                &copy; {{ now()->year }}
                <a href="{{ setting('website_url', '#') }}" target="_blank">
                    {{ setting('institution_name', 'eBoard System') }}
                </a>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span style="opacity: 0.75;">eBoard Management System</span>
            </div>
        </div>
    </div>
</footer>
