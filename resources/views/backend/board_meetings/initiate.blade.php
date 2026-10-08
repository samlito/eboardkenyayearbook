{{--@extends('admin_dashboard')--}}
{{--@section('admin')--}}

{{--    <div class="content">--}}
{{--        <div class="container-fluid">--}}

{{--            <div class="page-title-box d-flex justify-content-between align-items-center">--}}
{{--                <h4 class="page-title">Initiate Meeting: {{ $meeting->title }}</h4>--}}
{{--                <div class="text-danger fw-bold">Meeting in: {{ $daysRemaining }} day(s)</div>--}}
{{--            </div>--}}

{{--            <div class="card">--}}
{{--                <div class="card-body">--}}

{{--                    <form action="{{ route('board.notice.store') }}" method="POST">--}}
{{--                        @csrf--}}

{{--                        <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">--}}

{{--                        <div class="form-group mb-3">--}}
{{--                            <label>Meeting Title</label>--}}
{{--                            <input type="text" class="form-control" value="{{ $meeting->title }}" readonly>--}}
{{--                        </div>--}}

{{--                        <div class="form-group mb-3">--}}
{{--                            <label>Meeting Date</label>--}}
{{--                            <input type="text" class="form-control" value="{{ $meeting->date }}" readonly>--}}
{{--                        </div>--}}

{{--                        <div class="form-group mb-3">--}}
{{--                            <label>Venue</label>--}}
{{--                            <input type="text" class="form-control" value="{{ $meeting->venue }}" readonly>--}}
{{--                        </div>--}}

{{--                        <div class="form-group mb-3">--}}
{{--                            <label>Notice Content</label>--}}
{{--                            <textarea name="contentb" rows="6" class="form-control" required placeholder="Enter notice details, agenda items, expectations etc..."></textarea>--}}
{{--                        </div>--}}

{{--                        <button type="submit" class="btn btn-primary">Submit Notice to CEO</button>--}}
{{--                    </form>--}}

{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}

{{--@endsection--}}
@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Initiate Meeting: {{ $meeting->title }}</h4>
                <div class="text-danger fw-bold">Meeting in: {{ $daysRemaining }} day(s)</div>
            </div>

            <div class="card">
                <div class="card-body">
                    {{-- Validation errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('board.notice.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">

                        <div class="form-group mb-3">
                            <label>Meeting Title</label>
                            <input type="text" class="form-control "
                                   value="{{ $meeting->title }}"  name="title" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Meeting Date</label>
                            <input type="text" class="form-control bg-light text-muted"
                                   value="{{ $meeting->date }}" readonly>
                        </div>


                        {{--                        <div class="form-group mb-3">--}}
{{--                            <label>Venue</label>--}}
{{--                            <input type="text" class="form-control" value="{{ $meeting->venue }}" readonly>--}}
{{--                        </div>--}}

                        <!-- Reference Number -->
                        <div class="form-group mb-3">
                            <label>Reference Number</label>
                            <input type="text" name="reference_number" class="form-control"
                                   value="{{ old('reference_number') }}" required placeholder="e.g. {{ setting('institution_acronym', 'ORG') }}/BOARD/{{ date('Y') }}/001">
                        </div>

                        <!-- Reference Date -->
                        <div class="form-group mb-3">
                            <label>Reference Date</label>
                            <input type="date" name="reference_date" class="form-control"
                                   value="{{ old('reference_date', now()->toDateString()) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-semibold mb-2" style="font-size: 15px; color: #111111;">Notice Content <span class="text-danger">*</span></label>
                            {{-- Hidden input carries the HTML from Quill to the server --}}
                            <input type="hidden" name="contentb" id="contentb-input">
                            <div id="notice-editor" style="min-height: 220px;">{!! old('contentb') !!}</div>
                            @error('contentb')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Notice to CEO</button>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection

@push('styles')
<style>
/* ── Quill editor styling for Notice ───────────────────── */
#notice-editor {
    background: #fff;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 6px 6px;
    font-size: 15px;
    color: #111111;
    line-height: 1.7;
}
#notice-editor .ql-editor {
    min-height: 200px;
    font-size: 15px;
    color: #111111;
    padding: 14px 16px;
}
#notice-editor .ql-editor.ql-blank::before {
    font-size: 15px;
    color: #9ca3af;
    font-style: normal;
}
/* Toolbar polish */
#notice-editor + .ql-toolbar,
.ql-toolbar.ql-snow {
    border: 1px solid #ced4da;
    border-radius: 6px 6px 0 0;
    background: #f9fafb;
}
/* Ensure toolbar appears ABOVE the editor */
.ql-toolbar.ql-snow + .ql-container.ql-snow {
    border-top: none;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var hiddenInput = document.getElementById('contentb-input');

    // Initialize Quill with Snow theme
    var quill = new Quill('#notice-editor', {
        theme: 'snow',
        placeholder: 'Enter notice details, agenda items, expectations etc...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['blockquote'],
                ['link'],
                ['clean']
            ]
        }
    });

    // Helper: get clean HTML from Quill (empty if blank)
    function getQuillHtml() {
        var html = quill.root.innerHTML;
        if (!html || html === '<p><br></p>' || html.replace(/<[^>]*>/g, '').trim() === '') {
            return '';
        }
        return html;
    }

    // ── Real-time sync: keep hidden input updated on every keystroke ──
    quill.on('text-change', function () {
        hiddenInput.value = getQuillHtml();
    });

    // ── Also sync on form submit as safety net ──
    var form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        // Force-sync one more time right before submit
        hiddenInput.value = getQuillHtml();

        // Client-side required check
        if (!hiddenInput.value) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Notice Content Required',
                text: 'Please enter the notice content before submitting.',
                confirmButtonColor: '{{ setting('primary_color', '#1a1a1a') }}',
                confirmButtonText: 'OK'
            });
            quill.focus();
            return false;
        }
    });
});
</script>
@endpush
