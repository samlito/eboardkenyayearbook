
@extends('admin_dashboard')

@section('admin')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Str;

        $date = Carbon::parse($meeting->date);
        $daysRemaining = now()->diffInDays($date, false);

        // helper to check if a path points to a PDF
        function is_pdf($path) {
            try {
                return strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf';
            } catch (\Throwable $e) {
                return Str::endsWith(Str::lower($path ?? ''), '.pdf');
            }
        }
    @endphp

    <style>
        /* table niceties */
        .table thead th, .table tbody td { vertical-align: middle; }
        /* generic scoped viewer container (used when we must inline CSS) */
        .pdfjs-container { height: 80svh; overflow:auto; -webkit-overflow-scrolling:touch; padding:0; }
        .pdfjs-container canvas { width:100% !important; height:auto !important; display:block; margin:0 0 8px 0; }
    </style>

    <div class="container mt-4">
        <h3 class="mb-3">📌 Board Meeting: {{ $meeting->title }}</h3>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}</p>

        <ul class="nav nav-tabs mb-3" id="meetingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="papers-tab" data-bs-toggle="tab" data-bs-target="#papers" type="button" role="tab" aria-controls="papers" aria-selected="true">
                    📁 Board Papers
                </button>
            </li>
        </ul>

        <div class="tab-content" id="meetingTabsContent">
            <!-- Papers Tab -->
            <div class="tab-pane fade show active" id="papers" role="tabpanel" aria-labelledby="papers-tab">
                <div class="mt-4">
                    <h5 class="mb-3">📄 Board Papers</h5>

                    @if($orderedPapers->isEmpty())
                        <p class="text-muted">No board papers have been uploaded for this meeting.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Title</th>
                                    <th style="width: 420px;">Document(s)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orderedPapers as $index => $paper)
                                    @php
                                        $mainUrl = asset('storage/' . $paper->file);
                                        $mainIsPdf = is_pdf($paper->file);
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-semibold">{{ $paper->title }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">

                                                {{-- Main paper actions --}}
                                                @if($mainIsPdf)
                                                    <!-- View in modal (PDF.js) -->
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#mainPaperModal{{ $paper->id }}">
                                                        📄 View Here
                                                    </button>
                                                @endif

                                                <!-- Open in new tab -->
                                                <a href="{{ $mainUrl }}" target="_blank" rel="noopener"
                                                   class="btn btn-sm btn-outline-success">
                                                    🌐 Open in New Tab
                                                </a>

{{--                                                <!-- Download -->--}}
{{--                                                <a href="{{ $mainUrl }}" download--}}
{{--                                                   class="btn btn-sm btn-outline-dark">--}}
{{--                                                    ⬇️ Download--}}
{{--                                                </a>--}}

                                                {{-- Annexures toggle --}}
                                                @if($paper->annexures->count())
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#annexures-{{ $paper->id }}"
                                                            aria-expanded="false"
                                                            aria-controls="annexures-{{ $paper->id }}">
                                                        📎 Annexures ({{ $paper->annexures->count() }})
                                                    </button>
                                                @endif

                                                {{-- Supporting docs (may be PDF or not) --}}
                                                @if($paper->supporting_docs)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-info"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#supportingDocsModal{{ $paper->id }}">
                                                        📚 Supporting Docs
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Annexures list --}}
                                            @if($paper->annexures->count())
                                                <div class="collapse mt-2" id="annexures-{{ $paper->id }}">
                                                    <ul class="list-group">
                                                        @foreach($paper->annexures as $annexure)
                                                            @php
                                                                $annUrl = asset('storage/' . $annexure->file);
                                                                $annIsPdf = is_pdf($annexure->file);
                                                            @endphp
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>
                                <strong>{{ $annexure->title }}</strong><br>
                                <small class="text-muted">{{ $annexure->description }}</small>
                              </span>
                                                                <div class="d-flex gap-2">
                                                                    @if($annIsPdf)
                                                                        <button class="btn btn-sm btn-outline-info"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#annexureModal{{ $annexure->id }}">
                                                                            View in Modal
                                                                        </button>
                                                                    @endif
                                                                    <a class="btn btn-sm btn-outline-success" href="{{ $annUrl }}" target="_blank" rel="noopener">
                                                                        Open
                                                                    </a>
                                                                    <a class="btn btn-sm btn-outline-dark" href="{{ $annUrl }}" download>
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            </li>

                                                            {{-- Annexure Modal (PDF only) --}}
{{--                                                            @if($annIsPdf)--}}
{{--                                                                <div class="modal fade pdfjs-modal"--}}
{{--                                                                     id="annexureModal{{ $annexure->id }}"--}}
{{--                                                                     tabindex="-1" aria-hidden="true"--}}
{{--                                                                     data-pdf-url="{{ $annUrl }}"--}}
{{--                                                                     data-container-id="pdfContainer-annexure-{{ $annexure->id }}">--}}
{{--                                                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">--}}
{{--                                                                        <div class="modal-content">--}}
{{--                                                                            <div class="modal-header">--}}
{{--                                                                                <h5 class="modal-title">{{ $annexure->title }}</h5>--}}
{{--                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>--}}
{{--                                                                            </div>--}}
{{--                                                                            <div class="modal-body p-0">--}}
{{--                                                                                --}}{{-- scoped CSS already provided globally; container is per-modal --}}
{{--                                                                                <div id="pdfContainer-annexure-{{ $annexure->id }}" class="pdfjs-container"></div>--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                            @endif--}}
                                                            @if($annIsPdf)
                                                                <div class="modal fade pdfjs-modal"
                                                                     id="annexureModal{{ $annexure->id }}"
                                                                     tabindex="-1" aria-hidden="true"
                                                                     data-pdf-url="{{ $annUrl }}"
                                                                     data-container-id="pdfContainer-annexure-{{ $annexure->id }}">
                                                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">{{ $annexure->title }}</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                            </div>

                                                                            <ul class="nav nav-tabs px-3 pt-2" id="annTabs-{{ $annexure->id }}" role="tablist">
                                                                                <li class="nav-item" role="presentation">
                                                                                    <button class="nav-link active" id="view-tab-ann-{{ $annexure->id }}" data-bs-toggle="tab"
                                                                                            data-bs-target="#view-pane-ann-{{ $annexure->id }}" type="button" role="tab">
                                                                                        Viewer
                                                                                    </button>
                                                                                </li>
                                                                                <li class="nav-item" role="presentation">
                                                                                    <button class="nav-link" id="open-tab-ann-{{ $annexure->id }}" data-bs-toggle="tab"
                                                                                            data-bs-target="#open-pane-ann-{{ $annexure->id }}" type="button" role="tab">
                                                                                        Open / Download
                                                                                    </button>
                                                                                </li>
                                                                            </ul>

                                                                            <div class="tab-content">
                                                                                <div class="tab-pane fade show active" id="view-pane-ann-{{ $annexure->id }}" role="tabpanel">
                                                                                    <div class="modal-body p-0">
                                                                                        <style>
                                                                                            #annexureModal{{ $annexure->id }} .pdfjs-container{
                                                                                                height:80svh; overflow:auto; -webkit-overflow-scrolling:touch; padding:0;
                                                                                            }
                                                                                            #annexureModal{{ $annexure->id }} .pdfjs-container canvas{
                                                                                                width:100% !important; height:auto !important; display:block; margin:0 0 8px 0;
                                                                                            }
                                                                                        </style>
                                                                                        <div id="pdfContainer-annexure-{{ $annexure->id }}" class="pdfjs-container">
                                                                                            <div class="p-3 text-muted">Loading…</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="tab-pane fade" id="open-pane-ann-{{ $annexure->id }}" role="tabpanel">
                                                                                    <div class="p-3 d-flex flex-wrap gap-2">
                                                                                        <a class="btn btn-sm btn-outline-success" href="{{ $annUrl }}" target="_blank" rel="noopener">🌐 Open in New Tab</a>
                                                                                        <a class="btn btn-sm btn-outline-dark" href="{{ $annUrl }}" download>⬇️ Download</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="modal-footer">
                                                                                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

{{--                                    --}}{{-- Main Paper Modal (PDF only) --}}
{{--                                    @if($mainIsPdf)--}}
{{--                                        <div class="modal fade pdfjs-modal"--}}
{{--                                             id="mainPaperModal{{ $paper->id }}"--}}
{{--                                             tabindex="-1" aria-hidden="true"--}}
{{--                                             data-pdf-url="{{ $mainUrl }}"--}}
{{--                                             data-container-id="pdfContainer-main-{{ $paper->id }}">--}}
{{--                                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">--}}
{{--                                                <div class="modal-content">--}}
{{--                                                    <div class="modal-header">--}}
{{--                                                        <h5 class="modal-title">{{ $paper->title }} — Main Paper</h5>--}}
{{--                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="modal-body p-0">--}}
{{--                                                        <div id="pdfContainer-main-{{ $paper->id }}" class="pdfjs-container"></div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
                                    @if($mainIsPdf)
                                        <div class="modal fade pdfjs-modal"
                                             id="mainPaperModal{{ $paper->id }}"
                                             tabindex="-1" aria-hidden="true"
                                             data-pdf-url="{{ $mainUrl }}"
                                             data-container-id="pdfContainer-main-{{ $paper->id }}">
                                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ $paper->title }} — Main Paper</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    {{-- Tabs --}}
                                                    <ul class="nav nav-tabs px-3 pt-2" id="mainTabs-{{ $paper->id }}" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="view-tab-{{ $paper->id }}" data-bs-toggle="tab"
                                                                    data-bs-target="#view-pane-{{ $paper->id }}" type="button" role="tab" aria-controls="view-pane-{{ $paper->id }}" aria-selected="true">
                                                                Viewer
                                                            </button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="open-tab-{{ $paper->id }}" data-bs-toggle="tab"
                                                                    data-bs-target="#open-pane-{{ $paper->id }}" type="button" role="tab" aria-controls="open-pane-{{ $paper->id }}" aria-selected="false">
                                                                Open / Download
                                                            </button>
                                                        </li>
                                                    </ul>

                                                    <div class="tab-content">
                                                        {{-- Viewer pane --}}
                                                        <div class="tab-pane fade show active" id="view-pane-{{ $paper->id }}" role="tabpanel" aria-labelledby="view-tab-{{ $paper->id }}">
                                                            <div class="modal-body p-0">
                                                                <style>
                                                                    #mainPaperModal{{ $paper->id }} .pdfjs-container{
                                                                        height:80svh; overflow:auto; -webkit-overflow-scrolling:touch; padding:0;
                                                                    }
                                                                    #mainPaperModal{{ $paper->id }} .pdfjs-container canvas{
                                                                        width:100% !important; height:auto !important; display:block; margin:0 0 8px 0;
                                                                    }
                                                                </style>
                                                                <div id="pdfContainer-main-{{ $paper->id }}" class="pdfjs-container">
                                                                    <div class="p-3 text-muted">Loading…</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Open/Download pane --}}
                                                        <div class="tab-pane fade" id="open-pane-{{ $paper->id }}" role="tabpanel" aria-labelledby="open-tab-{{ $paper->id }}">
                                                            <div class="p-3 d-flex flex-wrap gap-2">
                                                                <a class="btn btn-sm btn-outline-success" href="{{ $mainUrl }}" target="_blank" rel="noopener">🌐 Open in New Tab</a>
                                                                <a class="btn btn-sm btn-outline-dark" href="{{ $mainUrl }}" download>⬇️ Download</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Supporting Docs Modal (PDF if PDF; otherwise show open/download options) --}}
{{--                                    @if($paper->supporting_docs)--}}
{{--                                        @php--}}
{{--                                            $suppUrl  = asset('storage/' . $paper->supporting_docs);--}}
{{--                                            $suppIsPdf = is_pdf($paper->supporting_docs);--}}
{{--                                        @endphp--}}
{{--                                        <div class="modal fade {{ $suppIsPdf ? 'pdfjs-modal' : '' }}"--}}
{{--                                             id="supportingDocsModal{{ $paper->id }}"--}}
{{--                                             tabindex="-1" aria-hidden="true"--}}
{{--                                             @if($suppIsPdf)--}}
{{--                                                 data-pdf-url="{{ $suppUrl }}"--}}
{{--                                             data-container-id="pdfContainer-supp-{{ $paper->id }}"--}}
{{--                                            @endif--}}
{{--                                        >--}}
{{--                                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">--}}
{{--                                                <div class="modal-content">--}}
{{--                                                    <div class="modal-header">--}}
{{--                                                        <h5 class="modal-title">{{ $paper->title }} — Supporting Documents</h5>--}}
{{--                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="modal-body p-0">--}}
{{--                                                        @if($suppIsPdf)--}}
{{--                                                            <div id="pdfContainer-supp-{{ $paper->id }}" class="pdfjs-container"></div>--}}
{{--                                                        @else--}}
{{--                                                            <div class="p-3">--}}
{{--                                                                <p class="mb-3">This file isn’t a PDF. Choose an action:</p>--}}
{{--                                                                <div class="d-flex gap-2">--}}
{{--                                                                    <a class="btn btn-sm btn-outline-success" href="{{ $suppUrl }}" target="_blank" rel="noopener">Open in New Tab</a>--}}
{{--                                                                    <a class="btn btn-sm btn-outline-dark" href="{{ $suppUrl }}" download>Download</a>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
                                    @if($paper->supporting_docs)
                                        @php
                                            $suppUrl  = asset('storage/' . $paper->supporting_docs);
                                            $suppIsPdf = is_pdf($paper->supporting_docs);
                                        @endphp

                                        <div class="modal fade {{ $suppIsPdf ? 'pdfjs-modal' : '' }}"
                                             id="supportingDocsModal{{ $paper->id }}"
                                             tabindex="-1" aria-hidden="true"
                                             @if($suppIsPdf)
                                                 data-pdf-url="{{ $suppUrl }}"
                                             data-container-id="pdfContainer-supp-{{ $paper->id }}"
                                            @endif>
                                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ $paper->title }} — Supporting Documents</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    @if($suppIsPdf)
                                                        <ul class="nav nav-tabs px-3 pt-2" id="suppTabs-{{ $paper->id }}" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="view-tab-supp-{{ $paper->id }}" data-bs-toggle="tab"
                                                                        data-bs-target="#view-pane-supp-{{ $paper->id }}" type="button" role="tab">
                                                                    Viewer
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="open-tab-supp-{{ $paper->id }}" data-bs-toggle="tab"
                                                                        data-bs-target="#open-pane-supp-{{ $paper->id }}" type="button" role="tab">
                                                                    Open / Download
                                                                </button>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content">
                                                            <div class="tab-pane fade show active" id="view-pane-supp-{{ $paper->id }}" role="tabpanel">
                                                                <div class="modal-body p-0">
                                                                    <style>
                                                                        #supportingDocsModal{{ $paper->id }} .pdfjs-container{
                                                                            height:80svh; overflow:auto; -webkit-overflow-scrolling:touch; padding:0;
                                                                        }
                                                                        #supportingDocsModal{{ $paper->id }} .pdfjs-container canvas{
                                                                            width:100% !important; height:auto !important; display:block; margin:0 0 8px 0;
                                                                        }
                                                                    </style>
                                                                    <div id="pdfContainer-supp-{{ $paper->id }}" class="pdfjs-container">
                                                                        <div class="p-3 text-muted">Loading…</div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="tab-pane fade" id="open-pane-supp-{{ $paper->id }}" role="tabpanel">
                                                                <div class="p-3 d-flex flex-wrap gap-2">
                                                                    <a class="btn btn-sm btn-outline-success" href="{{ $suppUrl }}" target="_blank" rel="noopener">🌐 Open in New Tab</a>
                                                                    <a class="btn btn-sm btn-outline-dark" href="{{ $suppUrl }}" download>⬇️ Download</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- Non-PDF: show actions directly --}}
                                                        <div class="modal-body">
                                                            <p class="mb-3">This file isn’t a PDF. Choose an action:</p>
                                                            <div class="d-flex gap-2">
                                                                <a class="btn btn-sm btn-outline-success" href="{{ $suppUrl }}" target="_blank" rel="noopener">Open in New Tab</a>
                                                                <a class="btn btn-sm btn-outline-dark" href="{{ $suppUrl }}" download>Download</a>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- PDF.js from CDN (load once) --}}
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
    <script>
        // Configure the worker from CDN
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            "https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js";

        // Render all pages of a PDF into a container (as canvases)
        async function renderPdfIntoContainer(pdfUrl, container) {
            container.innerHTML = "";
            const loadingTask = pdfjsLib.getDocument(pdfUrl);
            const pdf = await loadingTask.promise;

            // adapt scale to container width for readability
            const targetWidth = container.clientWidth || container.offsetWidth || 1000;

            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                const page = await pdf.getPage(pageNumber);
                const initialViewport = page.getViewport({ scale: 1 });
                const scale = targetWidth / initialViewport.width;
                const scaledViewport = page.getViewport({
                    scale: Math.max(1.1, Math.min(scale, 2.0))
                });

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = Math.floor(scaledViewport.width);
                canvas.height = Math.floor(scaledViewport.height);
                container.appendChild(canvas);

                await page.render({ canvasContext: ctx, viewport: scaledViewport }).promise;
            }
        }

        // Auto-bind to all PDF modals on show/hide
        document.querySelectorAll('.pdfjs-modal').forEach(modalEl => {
            const pdfUrl = modalEl.getAttribute('data-pdf-url');
            const containerId = modalEl.getAttribute('data-container-id');
            let rendered = false;

            modalEl.addEventListener('shown.bs.modal', async () => {
                const container = document.getElementById(containerId);
                if (!rendered && container) {
                    try {
                        await renderPdfIntoContainer(pdfUrl, container);
                        rendered = true;
                    } catch (e) {
                        if (container) container.innerHTML =
                            '<div class="p-3 text-danger">Failed to load PDF.</div>';
                        console.error(e);
                    }
                }
            });

            modalEl.addEventListener('hidden.bs.modal', () => {
                const container = document.getElementById(containerId);
                if (container) container.innerHTML = '';
                rendered = false;
            });
        });

        // Safety: ensure body scroll restored (some iOS quirks)
        document.addEventListener('hidden.bs.modal', function () {
            document.body.style.overflow = '';
        });
    </script>
@endsection
