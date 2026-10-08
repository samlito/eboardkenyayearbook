@extends('admin_dashboard')
@section('admin')
    @php
        use Carbon\Carbon;
        $date = Carbon::parse($meeting->date);
        $daysRemaining = now()->diffInDays($date, false);
    @endphp

    <div class="container mt-5">
        <!-- Executive Meeting Overview -->
        <div class="row g-4 mb-5">
            <!-- Calendar Block -->
            <div class="col-md-2">
                <div class="bg-dark text-white text-center p-4 rounded shadow-lg border-start border-4 border-warning">
                    <h1 class="display-3 fw-bold" style="color: #FFFFFF">{{ $date->format('d') }}</h1>
                    <div class="text-uppercase">{{ $date->format('F') }}</div>
                    <small>{{ $date->format('Y') }}</small>
                    <hr class="my-2 border-light">
                    <div class="small"><i class="bi bi-clock"></i> {{ $date->format('g:i A') }}</div>
                </div>
            </div>

            <!-- Meeting Details -->
            <div class="col-md-10">
                <div class="card border-0 shadow-lg bg-light">
                    <div class="card-body">
                        <h2 class="fw-bold text-dark">{{ $meeting->title }}</h2>
                        <p class="text-muted fst-italic">Scheduled Executive Board Meeting</p>
                        <ul class="list-unstyled">
                            <li><strong><i class="bi bi-people-fill text-primary"></i> Committee:</strong> {{ $meeting->committee->name ?? 'N/A' }}</li>
                            <li><strong><i class="bi bi-calendar-check-fill text-success"></i> Status:</strong>
                                @if($daysRemaining > 0)
                                    <span class="badge bg-info">{{ $daysRemaining }} Days to Meeting</span>
                                @elseif($daysRemaining === 0)
                                    <span class="badge bg-success">Meeting is Today — <em></em> 🔔</span>
                                @else
                                    <span class="badge bg-secondary">Meeting Passed — <em></em></span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agenda Section -->
        <div class="card mb-5 border-0 shadow-sm">
            <div class="card-body">
                <div class="border-start border-3 border-info ps-3">
                    {{-- Agenda content if needed --}}
                </div>
            </div>
        </div>

        <!-- Board Papers Table -->
        <div class="card shadow-lg border-0">
            <div class="card-body">
                <h4 class="fw-bold mb-4 text-dark">📄 Submitted Board Papers</h4>

                @if($meeting->boardPapers->isEmpty())
                    <div class="alert alert-warning">No board papers have been uploaded for this meeting yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle shadow-sm">
                            <thead class="table-dark text-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($meeting->boardPapers as $index => $paper)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $paper->title }}</td>
                                    <td class="text-center">
                                        <!-- Main Paper -->
                                        <button class="btn btn-sm btn-outline-primary mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#mainModal{{ $paper->id }}">
                                            📄 View Board Paper
                                        </button>

                                        <!-- Annexures -->
                                        @if($paper->annexures->count())
                                            <button class="btn btn-sm btn-outline-secondary mb-1"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#annexures-{{ $paper->id }}">
                                                📎 Annexures ({{ $paper->annexures->count() }})
                                            </button>

                                            <div class="collapse mt-2" id="annexures-{{ $paper->id }}">
                                                <ul class="list-group">
                                                    @foreach($paper->annexures as $annexure)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span>
                                                                <strong>{{ $annexure->title }}</strong><br>
                                                                <small class="text-muted">{{ $annexure->description }}</small>
                                                            </span>
                                                            <button class="btn btn-sm btn-outline-info"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#annexureModal{{ $annexure->id }}">
                                                                View
                                                            </button>
                                                        </li>

                                                        <!-- Annexure Modal (PDF.js) -->
                                                        <div class="modal fade pdfjs-modal"
                                                             id="annexureModal{{ $annexure->id }}"
                                                             tabindex="-1"
                                                             aria-hidden="true"
                                                             data-pdf-url="{{ asset('storage/' . $annexure->file) }}"
                                                             data-container-id="pdfContainer-annexure-{{ $annexure->id }}">
                                                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">{{ $annexure->title }}</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body p-0">
                                                                        <style>
                                                                            #annexureModal{{ $annexure->id }} .pdfjs-container{
                                                                                height:80vh;overflow:auto;-webkit-overflow-scrolling:touch;padding:0
                                                                            }
                                                                            #annexureModal{{ $annexure->id }} .pdfjs-container canvas{
                                                                                width:100% !important;height:auto !important;display:block;margin:0 0 8px 0
                                                                            }
                                                                        </style>
                                                                        <div id="pdfContainer-annexure-{{ $annexure->id }}" class="pdfjs-container"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Main Paper Modal (PDF.js) -->
                                <div class="modal fade pdfjs-modal"
                                     id="mainModal{{ $paper->id }}"
                                     tabindex="-1"
                                     aria-hidden="true"
                                     data-pdf-url="{{ asset('storage/' . $paper->file) }}"
                                     data-container-id="pdfContainer-main-{{ $paper->id }}">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $paper->title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <style>
                                                    #mainModal{{ $paper->id }} .pdfjs-container{
                                                        height:80vh;overflow:auto;-webkit-overflow-scrolling:touch;padding:0
                                                    }
                                                    #mainModal{{ $paper->id }} .pdfjs-container canvas{
                                                        width:100% !important;height:auto !important;display:block;margin:0 0 8px 0
                                                    }
                                                </style>
                                                <div id="pdfContainer-main-{{ $paper->id }}" class="pdfjs-container"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- PDF.js from CDN (load once) --}}
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
    <script>
        // Configure the worker from CDN as well
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            "https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js";

        // Utility to render all pages of a PDF into a container (as canvases)
        async function renderPdfIntoContainer(pdfUrl, container) {
            container.innerHTML = "";
            const loadingTask = pdfjsLib.getDocument(pdfUrl);
            const pdf = await loadingTask.promise;

            // Choose scale based on container width for nice readability
            const targetWidth = container.clientWidth || container.offsetWidth || 1000;
            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                const page = await pdf.getPage(pageNumber);
                // Start with scale 1, then compute to match width
                const viewport = page.getViewport({ scale: 1 });
                const scale = targetWidth / viewport.width;
                const scaledViewport = page.getViewport({ scale: Math.max(1.2, Math.min(scale, 2.0)) });

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = Math.floor(scaledViewport.width);
                canvas.height = Math.floor(scaledViewport.height);
                container.appendChild(canvas);

                await page.render({ canvasContext: ctx, viewport: scaledViewport }).promise;
            }
        }

        // Attach to any modal with class .pdfjs-modal
        document.querySelectorAll('.pdfjs-modal').forEach(modalEl => {
            const pdfUrl = modalEl.getAttribute('data-pdf-url');
            const containerId = modalEl.getAttribute('data-container-id');
            let rendered = false;

            modalEl.addEventListener('shown.bs.modal', async () => {
                // Render only once per open to keep it snappy
                const container = document.getElementById(containerId);
                if (!rendered && container) {
                    try {
                        await renderPdfIntoContainer(pdfUrl, container);
                        rendered = true;
                    } catch (e) {
                        container.innerHTML = '<div class="p-3 text-danger">Failed to load PDF.</div>';
                        console.error(e);
                    }
                }
            });

            modalEl.addEventListener('hidden.bs.modal', () => {
                // Optional: clear canvases to free memory when closing
                const container = document.getElementById(containerId);
                if (container) container.innerHTML = '';
                rendered = false;
            });
        });
    </script>
@endsection
