@extends('setup.layout')

@section('content')
<h3 class="section-title"><i class="mdi mdi-palette"></i> Branding & Appearance</h3>
<p class="section-subtitle">Upload your institution's logos and set brand colors.</p>

<form action="{{ route('setup.process', 2) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">
        <div class="col-12"><h6 class="text-muted mb-0">Logos</h6></div>

        <div class="col-md-6">
            <label class="form-label">Main Logo</label>
            <input type="file" name="logo_main" class="form-control" accept="image/*">
            <small class="text-muted">Used on landing page, login, emails. Max 2MB.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Sidebar Logo</label>
            <input type="file" name="logo_sidebar" class="form-control" accept="image/*">
            <small class="text-muted">Small logo for the admin sidebar. Max 2MB.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Letterhead Image</label>
            <input type="file" name="logo_letterhead" class="form-control" accept="image/*">
            <small class="text-muted">Full-width letterhead for PDF documents. Max 4MB.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Favicon</label>
            <input type="file" name="favicon" class="form-control" accept=".ico,.png,.jpg,.svg">
            <small class="text-muted">Browser tab icon. Max 512KB.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">CEO Signature Image</label>
            <input type="file" name="ceo_signature_image" class="form-control" accept="image/*">
            <small class="text-muted">Digital signature for official documents. Max 2MB.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Login Background Image</label>
            <input type="file" name="login_background" class="form-control" accept="image/*">
            <small class="text-muted">Background image for the login page. Max 4MB.</small>
        </div>

        <div class="col-12"><hr class="my-2"><h6 class="text-muted mb-0">Brand Colors</h6></div>

        <div class="col-md-4">
            <label class="form-label">Primary Color <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $savedData['branding']['primary_color'] ?? '#232323') }}" style="width: 50px;">
                <input type="text" class="form-control" value="{{ old('primary_color', $savedData['branding']['primary_color'] ?? '#232323') }}" id="primary_color_text" readonly>
            </div>
            <small class="text-muted">Main brand color (headers, buttons)</small>
        </div>
        <div class="col-md-4">
            <label class="form-label">Accent Color <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="color" name="accent_color" class="form-control form-control-color" value="{{ old('accent_color', $savedData['branding']['accent_color'] ?? '#7AC143') }}" style="width: 50px;">
                <input type="text" class="form-control" value="{{ old('accent_color', $savedData['branding']['accent_color'] ?? '#7AC143') }}" id="accent_color_text" readonly>
            </div>
            <small class="text-muted">Secondary brand color (highlights)</small>
        </div>
        <div class="col-md-4">
            <label class="form-label">Secondary Color</label>
            <div class="input-group">
                <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color', $savedData['branding']['secondary_color'] ?? '#222222') }}" style="width: 50px;">
                <input type="text" class="form-control" value="{{ old('secondary_color', $savedData['branding']['secondary_color'] ?? '#222222') }}" id="secondary_color_text" readonly>
            </div>
            <small class="text-muted">Used for links, accents</small>
        </div>

        <div class="col-12 mt-3">
            <label class="form-label">Preview</label>
            <div class="d-flex gap-2 align-items-center p-3 rounded" id="colorPreview" style="background: #f8f9fa;">
                <div class="rounded px-3 py-2 text-white" id="previewPrimary" style="background: #1a1a1a;">Primary</div>
                <div class="rounded px-3 py-2 text-white" id="previewAccent" style="background: #7AC143;">Accent</div>
                <div class="rounded px-3 py-2 text-white" id="previewSecondary" style="background: #1a1a1a;">Secondary</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('setup.show', 1) }}" class="btn btn-outline-secondary"><i class="mdi mdi-arrow-left"></i> Back</a>
        <button type="submit" class="btn btn-setup">Next: Workflow <i class="mdi mdi-arrow-right"></i></button>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.querySelectorAll('input[type="color"]').forEach(picker => {
    const textInput = picker.parentElement.querySelector('input[type="text"]');
    const previewId = 'preview' + picker.name.replace('_color','').replace(/^\w/, c => c.toUpperCase()).replace(/_(\w)/g, (m, c) => c.toUpperCase());
    const previewEl = document.getElementById(previewId);
    picker.addEventListener('input', function() {
        if (textInput) textInput.value = this.value;
        if (previewEl) previewEl.style.background = this.value;
    });
});
</script>
@endsection
