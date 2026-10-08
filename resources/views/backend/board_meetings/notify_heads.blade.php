{{--@extends('admin_dashboard')--}}
{{--@section('admin')--}}

{{--    <div class="content">--}}
{{--        <div class="container-fluid">--}}
{{--            <h4 class="page-title">Notify Division Heads for: {{ $meeting->title }}</h4>--}}
{{--            @if($errors->any())--}}
{{--                <div class="alert alert-danger">--}}
{{--                    <ul class="mb-0">--}}
{{--                        @foreach($errors->all() as $error)--}}
{{--                            <li>{{ $error }}</li>--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            <form action="{{ route('board.meetings.send.heads', $meeting->id) }}" method="POST">--}}
{{--                @csrf--}}

{{--                <div class="form-group">--}}
{{--                    <label>Select Division Heads to Notify:</label>--}}
{{--                    <div class="border rounded p-3">--}}
{{--                        @foreach($heads as $head)--}}
{{--                            <div class="form-check">--}}
{{--                                <input class="form-check-input" type="checkbox" name="heads[]" value="{{ $head->id }}" id="head{{ $head->id }}">--}}
{{--                                <label class="form-check-label" for="head{{ $head->id }}">--}}
{{--                                    {{ $head->name }}-Of Division {{ $head->division->name ?? '—' }}--}}

{{--                                </label>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <button type="submit" class="btn btn-primary mt-3">Send Notification</button>--}}
{{--            </form>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--@endsection--}}
@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📢 Notify Division Heads for: <span class="fw-bold">{{ $meeting->title }}</span></h4>
                </div>
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="notifyForm" action="{{ route('board.meetings.send.heads', $meeting->id) }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="fw-bold mb-2">Select Division Heads to Notify:</label>
                            <div class="row">
{{--                                @foreach($heads as $head)--}}
{{--                                    <div class="col-md-6 mb-2">--}}
{{--                                        <div class="form-check border rounded p-2">--}}
{{--                                            <input class="form-check-input" type="checkbox"--}}
{{--                                                   name="heads[]"--}}
{{--                                                   value="{{ $head->id }}"--}}
{{--                                                   id="head{{ $head->id }}">--}}
{{--                                            <label class="form-check-label" for="head{{ $head->id }}">--}}
{{--                                                {{ $head->name }}--}}
{{--                                                <span class="text-muted">— Division {{ $head->division->name ?? '—' }}</span>--}}
{{--                                            </label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}
                                @foreach($heads as $head)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check border rounded p-2">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="heads[]"
                                                   value="{{ $head->id }}"
                                                   id="head{{ $head->id }}"

                                                   @if(in_array($head->id, $alreadyAssigned))
                                                       disabled
                                                @endif
                                            >

                                            <label class="form-check-label for="head{{ $head->id }}">
                                            {{ $head->name }}

                                            @if(in_array($head->id, $alreadyAssigned))
                                                <span class="text-muted">— Already Assigned</span>
                                            @else
                                                <span class="text-muted">— Division {{ $head->division->name ?? '—' }}</span>
                                                @endif
                                                </label>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-success px-4">
                            <span id="btnText">🚀 Send Notification</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 for confirmation --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('notifyForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This will send notifications to selected Division Heads.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Send It!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable button + show spinner
                    let btn = document.getElementById('submitBtn');
                    document.getElementById('btnText').innerText = 'Sending...';
                    document.getElementById('btnSpinner').classList.remove('d-none');
                    btn.disabled = true;

                    // Submit form
                    e.target.submit();
                }
            });
        });
    </script>

@endsection
