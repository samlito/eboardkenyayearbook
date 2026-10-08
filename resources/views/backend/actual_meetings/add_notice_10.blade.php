@extends('admin_dashboard')

@section('admin')
    <style>
        #agenda-list li {
            cursor: grab;
        }
        #agenda-list li:active {
            cursor: grabbing;
        }

    </style>
    <div class="container mt-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">📨 Add 10 Days Meeting Notice</h4>
            <a href="{{ route('actual.meetings.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Meetings</a>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <h5><i class="bi bi-exclamation-triangle-fill me-1"></i> There were some errors:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Notice Form -->
{{--        <form action="{{ route('10day.notice.store', $meeting->id) }}" method="POST" onsubmit="return submitNoticeForm();">--}}
{{--            @csrf--}}
{{--            <!-- Reference Details -->--}}
{{--            <div class="card shadow-sm border-0 mb-4">--}}
{{--                <div class="card-body row g-3">--}}
{{--                    <div class="col-md-4">--}}
{{--                        <label class="form-label fw-semibold">📌 Reference Number</label>--}}
{{--                        <input type="text" name="reference_number" class="form-control" placeholder="Enter reference number" required>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-4">--}}
{{--                        <label class="form-label fw-semibold">📌 Reference</label>--}}
{{--                        <input type="text" name="reference" class="form-control" placeholder="Enter reference (e.g. circular code)">--}}
{{--                    </div>--}}
{{--                    <div class="col-md-4">--}}
{{--                        <label class="form-label fw-semibold">📅 Submission Date</label>--}}
{{--                        <input type="text" class="form-control" value="{{ now()->format('Y-m-d') }}" disabled>--}}
{{--                        <input type="hidden" name="submission_date" value="{{ now()->format('Y-m-d') }}">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!-- Quill Snow Editor -->--}}
{{--            <div class="card shadow-sm border-0 mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <label class="form-label fw-semibold mb-2">📝 Notice Content (Include time, location, agenda, CEO sign-off etc)</label>--}}
{{--                    <div id="snow-editor" style="height: 300px; background-color: #fff;"></div>--}}
{{--                    <input type="hidden" name="notice_text" id="notice_text">--}}
{{--                    <small class="text-muted">You can style the content using the toolbar above.</small>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <!-- Agenda Reordering -->--}}
{{--            <div class="card shadow-sm border-0 mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <label class="form-label fw-semibold mb-3">🧾 Agenda Items <small class="text-muted">(Drag to reorder)</small></label>--}}
{{--                    <ul class="list-group" id="agenda-list">--}}
{{--                        @foreach($meeting->boardPapers as $paper)--}}
{{--                            <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $paper->id }}">--}}
{{--                                <span><i class="bi bi-list me-2 text-muted"></i>{{ $paper->title }}</span>--}}
{{--                                <span class="badge bg-light text-dark">{{ $paper->status }}</span>--}}
{{--                            </li>--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                    <div id="agenda-order-inputs"></div>--}}
{{--                </div>--}}
{{--            </div>--}}



{{--            <!-- jQuery UI CSS -->--}}
{{--            <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">--}}

{{--            <style>--}}
{{--                #agenda-list-modal li {--}}
{{--                    cursor: grab;--}}
{{--                }--}}
{{--                #agenda-list-modal li:active {--}}
{{--                    cursor: grabbing;--}}
{{--                }--}}
{{--            </style>--}}

{{--            <!-- jQuery + jQuery UI -->--}}
{{--            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
{{--            <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>--}}

{{--            <script>--}}
{{--                $(function () {--}}
{{--                    $('#agenda-list-modal').sortable({--}}
{{--                        handle: '.bi-list',--}}
{{--                        update: function () {--}}
{{--                            let order = [];--}}
{{--                            $('#agenda-list-modal li').each(function () {--}}
{{--                                order.push($(this).data('id'));--}}
{{--                            });--}}

{{--                            // Show current order inside modal--}}
{{--                            $('#agenda-order').text("Current order: " + order.join(', '));--}}

{{--                            // Store hidden inputs in form for submission--}}
{{--                            $('#agenda-order-inputs').empty();--}}
{{--                            order.forEach(function(id) {--}}
{{--                                $('<input>', {--}}
{{--                                    type: 'hidden',--}}
{{--                                    name: 'agenda_order[]',--}}
{{--                                    value: id--}}
{{--                                }).appendTo('#agenda-order-inputs');--}}
{{--                            });--}}
{{--                        }--}}
{{--                    });--}}
{{--                });--}}
{{--            </script>--}}


{{--            <!-- Submit Button -->--}}
{{--            <button type="submit" class="btn btn-success px-4">--}}
{{--                <i class="bi bi-send-check-fill me-1"></i> Submit Official Notice--}}
{{--            </button>--}}
{{--        </form>--}}

        <form action="{{ route('10day.notice.store', $meeting->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return submitNoticeForm();">
            @csrf
            <!-- Reference Details -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">📌 Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="Enter reference number" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">📌 Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Enter reference (e.g. circular code)">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">📅 Submission Date</label>
                        <input type="text" class="form-control" value="{{ now()->format('Y-m-d') }}" disabled>
                        <input type="hidden" name="submission_date" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <!-- Quill Snow Editor -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold mb-2">📝 Notice Content (Include time, location, agenda, CEO sign-off etc)</label>
                    <div id="snow-editor" style="height: 300px; background-color: #fff;"></div>
                    <input type="hidden" name="notice_text" id="notice_text">
                    <small class="text-muted">You can style the content using the toolbar above.</small>
                </div>
            </div>
            <!-- PDF Upload -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">📂 Upload Supporting Document (PDF)</label>
                    <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
                    <small class="text-muted">Upload the official notice PDF or supporting document (optional).</small>
                </div>
            </div>
            <!-- Agenda Reordering -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold mb-3">🧾 Agenda Items <small class="text-muted">(Drag to reorder)</small></label>
                    <ul class="list-group" id="agenda-list">
                        @foreach($meeting->boardPapers as $paper)
                            <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $paper->id }}">
                                <span><i class="bi bi-list me-2 text-muted"></i>{{ $paper->title }}</span>
                                <span class="badge bg-light text-dark">{{ $paper->status }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div id="agenda-order-inputs"></div>
                </div>
            </div>



            <!-- Submit Button -->
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-send-check-fill me-1"></i> Submit Official Notice
            </button>
        </form>

    </div>
@endsection
@push('styles')
    <!-- Quill Snow Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

@endpush

@push('scripts')
    <!-- jQuery + jQuery UI (for sortable) -->

    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    {{--    <!-- Quill JS -->--}}
    {{--    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>--}}
    <script>
        $(function () {
            // Initialize Quill
            const quill = new Quill('#snow-editor', {
                theme: 'snow',
                placeholder: 'Type the official notice here...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            // Sync Quill HTML content on form submission
            window.submitNoticeForm = function () {
                const htmlContent = quill.root.innerHTML;
                document.getElementById('notice_text').value = htmlContent;
                return true;
            };

            // Function to create hidden inputs for agenda order
            function updateAgendaOrderInputs() {
                $('#agenda-order-inputs').empty();
                $('#agenda-list li').each(function () {
                    $('<input>', {
                        type: 'hidden',
                        name: 'agenda_order[]',
                        value: $(this).data('id')
                    }).appendTo('#agenda-order-inputs');
                });
            }

            // Make agenda items sortable
            $('#agenda-list').sortable({
                cancel: ".badge",
                handle: ".bi-list",
                update: updateAgendaOrderInputs
            }).disableSelection();

            // Trigger input generation on page load
            updateAgendaOrderInputs();
        });
    </script>


@endpush



<!-- Agenda Reordering -->
{{--            <div class="card shadow-sm border-0 mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <label class="form-label fw-semibold mb-3">--}}
{{--                        🧾 Agenda Items <small class="text-muted">(Drag to reorder)</small>--}}
{{--                    </label>--}}

{{--                    <!-- Trigger button -->--}}
{{--                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#agendaModal">--}}
{{--                        Open Reorder Modal--}}
{{--                    </button>--}}

{{--                    <!-- hidden inputs will be injected here -->--}}
{{--                    <div id="agenda-order-inputs"></div>--}}
{{--                </div>--}}
{{--            </div>--}}

<!-- Modal -->
{{--            <div class="modal fade" style="display: none" id="agendaModal" tabindex="-1" aria-labelledby="agendaModalLabel" aria-hidden="true">--}}
{{--                <div class="modal-dialog modal-lg">--}}
{{--                    <div class="modal-content">--}}

{{--                        <div class="modal-header">--}}
{{--                            <h5 class="modal-title fw-bold" id="agendaModalLabel">Drag & Drop Agenda</h5>--}}
{{--                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                        </div>--}}

{{--                        <div class="modal-body">--}}
{{--                            <h6 class="fw-semibold mb-3">🧾 Drag to reorder</h6>--}}
{{--                            <ul class="list-group" id="agenda-list-modal">--}}
{{--                                @foreach($meeting->boardPapers as $paper)--}}
{{--                                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $paper->id }}">--}}
{{--                                        <span><i class="bi bi-list me-2 text-muted"></i>{{ $paper->title }}</span>--}}
{{--                                        <span class="badge bg-light text-dark">{{ $paper->status }}</span>--}}
{{--                                    </li>--}}
{{--                                @endforeach--}}
{{--                            </ul>--}}

{{--                            <div id="agenda-order" class="mt-3 text-muted small"></div>--}}
{{--                        </div>--}}

{{--                        <div class="modal-footer">--}}
{{--                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Done</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
