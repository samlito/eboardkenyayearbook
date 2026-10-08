@extends('admin_dashboard')
@section('admin')

    <style>
        .pdf-viewer-container {
            background: #525659;
            border-radius: 8px;
            padding: 20px;
            min-height: 600px;
        }
        .pdf-page-wrapper {
            position: relative;
            display: inline-block;
            margin: 0 auto 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            background: #fff;
            cursor: crosshair;
        }
        .pdf-page-wrapper canvas {
            display: block;
        }
        .pdf-controls {
            background: #333;
            border-radius: 8px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .pdf-controls button {
            background: #555;
            border: none;
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .pdf-controls button:hover { background: #777; }
        .pdf-controls button:disabled { opacity: 0.4; cursor: not-allowed; }
        .pdf-controls span { color: #fff; font-size: 14px; }

        .signature-overlay {
            position: absolute;
            pointer-events: none;
            z-index: 10;
        }
        .signature-overlay img {
            max-height: 60px;
            opacity: 0.9;
        }

        .signature-ghost {
            position: absolute;
            pointer-events: none;
            z-index: 20;
            opacity: 0.5;
            display: none;
        }
        .signature-ghost img {
            max-height: 60px;
        }

        .sig-pad-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            background: #fafafa;
            display: inline-block;
        }
        .sig-pad-area canvas {
            cursor: crosshair;
            touch-action: none;
        }

        .mode-badge {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>

    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ route('pdfsign.index') }}" class="btn btn-secondary btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Back
                            </a>
                        </div>
                        <h4 class="page-title">
                            {{ $document->title }}
                            @if($document->status === 'signed')
                                <span class="badge bg-success ms-2">Signed</span>
                            @else
                                <span class="badge bg-warning ms-2">Pending Signature</span>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left: PDF Viewer -->
                <div class="col-lg-9">
                    <div class="pdf-viewer-container text-center">

                        <!-- Controls -->
                        <div class="pdf-controls">
                            <button onclick="prevPage()" id="btnPrev">◀ Prev</button>
                            <span id="pageInfo">Page 1 / 1</span>
                            <button onclick="nextPage()" id="btnNext">Next ▶</button>
                            <span style="border-left:1px solid #666; padding-left:15px;">
                            <button onclick="zoomOut()">−</button>
                            <span id="zoomLevel">100%</span>
                            <button onclick="zoomIn()">+</button>
                        </span>
                        </div>

                        <!-- PDF Canvas -->
                        <div id="pdfPageWrapper" class="pdf-page-wrapper" onclick="handlePageClick(event)">
                            <canvas id="pdfCanvas"></canvas>

                            <!-- Ghost signature that follows mouse when in placement mode -->
                            <div id="signatureGhost" class="signature-ghost">
                                <img id="ghostImg" src="" alt="">
                            </div>

                            <!-- Placed signature overlay -->
                            @if($document->signature_data && $document->status === 'signed')
                                {{-- Will be rendered via JS on the correct page --}}
                            @endif
                            <div id="signatureOverlay" class="signature-overlay" style="display:none;">
                                <img id="overlayImg" src="" alt="Signature">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right: Signature Panel -->
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="mdi mdi-pen"></i> Signature</h5>
                        </div>
                        <div class="card-body">

                            @if($document->status === 'signed')
                                <!-- Already signed -->
                                <div class="text-center mb-3">
                                    <p class="text-success fw-bold"><i class="mdi mdi-check-circle"></i> Document Signed</p>
                                    <p class="text-muted small">
                                        By: {{ $document->signer->name ?? 'N/A' }}<br>
                                        On: {{ $document->signed_at->format('d M Y, h:i A') }}<br>
                                        Page: {{ $document->signature_page }}
                                    </p>
                                    <div style="border:1px solid #ddd; border-radius:6px; padding:10px; background:#f9f9f9;">
                                        <img src="{{ $document->signature_data }}" style="max-width:100%; max-height:80px;">
                                    </div>
                                </div>

                                <form action="{{ route('pdfsign.unsign', $document->id) }}" method="POST" onsubmit="return confirm('Remove signature and re-sign?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="mdi mdi-eraser"></i> Remove & Re-sign
                                    </button>
                                </form>

                            @else
                                <!-- Step 1: Draw signature -->
                                <p class="text-muted small mb-2">Step 1: Draw your signature below</p>
                                <div class="sig-pad-area text-center mb-2">
                                    <canvas id="sigCanvas" width="240" height="100"></canvas>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-3" onclick="clearSig()">
                                    <i class="mdi mdi-eraser"></i> Clear
                                </button>

                                <!-- Step 2: Place it -->
                                <p class="text-muted small mb-2">Step 2: Click the button, then click on the PDF where you want to place it</p>
                                <button class="btn btn-warning w-100 mb-3" id="btnPlaceMode" onclick="enterPlaceMode()">
                                    <i class="mdi mdi-cursor-default-click"></i> Place Signature on PDF
                                </button>

                                <!-- Step 3: Confirm -->
                                <form action="{{ route('pdfsign.sign', $document->id) }}" method="POST" id="signForm">
                                    @csrf
                                    <input type="hidden" name="signature_data" id="inputSigData">
                                    <input type="hidden" name="signature_page" id="inputSigPage">
                                    <input type="hidden" name="signature_x" id="inputSigX">
                                    <input type="hidden" name="signature_y" id="inputSigY">

                                    <button type="submit" class="btn btn-success w-100" id="btnConfirm" disabled onclick="return confirmSign()">
                                        <i class="mdi mdi-check-bold"></i> Confirm & Sign
                                    </button>
                                </form>

                                @error('signature_data') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif

                        </div>
                    </div>

                    <!-- Document Info -->
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-1"><strong>Title:</strong> {{ $document->title }}</p>
                            <p class="mb-1"><strong>Uploaded:</strong> {{ $document->created_at->format('d M Y') }}</p>
                            <p class="mb-0"><strong>By:</strong> {{ $document->uploader->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Floating mode indicator -->
    <div id="modeBadge" class="mode-badge bg-warning text-dark" style="display:none;">
        📌 Click anywhere on the PDF to place your signature
        <button onclick="exitPlaceMode()" class="btn btn-sm btn-dark ms-2">Cancel</button>
    </div>

    <!-- PDF.js from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // ── PDF.js Setup ────────────────────────────────────────────────────
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const pdfUrl = "{{ asset('storage/' . $document->original_pdf) }}";
        let pdfDoc = null, currentPage = 1, totalPages = 0, scale = 1.2;
        let placementMode = false, sigDrawn = false;

        // Existing signature data (if already signed)
        const existingSig = {
            data: {!! json_encode($document->signature_data) !!},
            page: {{ $document->signature_page ?? 'null' }},
            x: {{ $document->signature_x ?? 'null' }},
            y: {{ $document->signature_y ?? 'null' }},
            signed: {{ $document->status === 'signed' ? 'true' : 'false' }}
        };

        // Load PDF
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            totalPages = pdf.numPages;
            renderPage(currentPage);
        }).catch(function(err) {
            console.error('PDF load error:', err);
            document.getElementById('pdfCanvas').parentElement.innerHTML =
                '<p style="color:#fff;padding:40px;">Failed to load PDF. Make sure the file exists and storage link is created.<br><code>php artisan storage:link</code></p>';
        });

        function renderPage(num) {
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: scale });
                const canvas = document.getElementById('pdfCanvas');
                const ctx = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                page.render({ canvasContext: ctx, viewport: viewport }).promise.then(function() {
                    document.getElementById('pageInfo').textContent = 'Page ' + num + ' / ' + totalPages;
                    document.getElementById('btnPrev').disabled = (num <= 1);
                    document.getElementById('btnNext').disabled = (num >= totalPages);

                    // Show existing signature on its page
                    showExistingSignature(num);
                });
            });
        }

        function prevPage() { if (currentPage > 1) { currentPage--; renderPage(currentPage); } }
        function nextPage() { if (currentPage < totalPages) { currentPage++; renderPage(currentPage); } }
        function zoomIn() { scale = Math.min(scale + 0.2, 3.0); renderPage(currentPage); updateZoom(); }
        function zoomOut() { scale = Math.max(scale - 0.2, 0.5); renderPage(currentPage); updateZoom(); }
        function updateZoom() { document.getElementById('zoomLevel').textContent = Math.round(scale * 100 / 1.2 * 100) / 100 + '%'; }

        // ── Show existing signature on the correct page ─────────────────────
        function showExistingSignature(pageNum) {
            const overlay = document.getElementById('signatureOverlay');
            if (existingSig.signed && existingSig.page === pageNum) {
                const canvas = document.getElementById('pdfCanvas');
                document.getElementById('overlayImg').src = existingSig.data;
                overlay.style.left = (existingSig.x / 100 * canvas.width) + 'px';
                overlay.style.top = (existingSig.y / 100 * canvas.height) + 'px';
                overlay.style.display = 'block';
            } else {
                overlay.style.display = 'none';
            }
        }

        // ── Signature Pad (right panel) ─────────────────────────────────────
        const sc = document.getElementById('sigCanvas');
        if (sc) {
            const sctx = sc.getContext('2d');
            let drawing = false, slx = 0, sly = 0;
            sctx.strokeStyle = '#000'; sctx.lineWidth = 2; sctx.lineCap = 'round';

            function spos(e) {
                const r = sc.getBoundingClientRect(), sx = sc.width/r.width, sy = sc.height/r.height;
                const t = e.touches ? e.touches[0] : e;
                return { x: (t.clientX-r.left)*sx, y: (t.clientY-r.top)*sy };
            }

            sc.addEventListener('mousedown', e => { e.preventDefault(); drawing=true; const p=spos(e); slx=p.x; sly=p.y; });
            sc.addEventListener('mousemove', e => { e.preventDefault(); if(!drawing)return; sigDrawn=true; const p=spos(e); sctx.beginPath(); sctx.moveTo(slx,sly); sctx.lineTo(p.x,p.y); sctx.stroke(); slx=p.x; sly=p.y; });
            sc.addEventListener('mouseup', e => { if(e)e.preventDefault(); drawing=false; });
            sc.addEventListener('mouseout', e => { drawing=false; });
            sc.addEventListener('touchstart', e => { e.preventDefault(); drawing=true; const p=spos(e); slx=p.x; sly=p.y; });
            sc.addEventListener('touchmove', e => { e.preventDefault(); if(!drawing)return; sigDrawn=true; const p=spos(e); sctx.beginPath(); sctx.moveTo(slx,sly); sctx.lineTo(p.x,p.y); sctx.stroke(); slx=p.x; sly=p.y; });
            sc.addEventListener('touchend', e => { if(e)e.preventDefault(); drawing=false; });
        }

        function clearSig() {
            if (!sc) return;
            sc.getContext('2d').clearRect(0, 0, sc.width, sc.height);
            sigDrawn = false;
            exitPlaceMode();
            document.getElementById('signatureOverlay').style.display = 'none';
            document.getElementById('btnConfirm').disabled = true;
        }

        // ── Placement Mode ──────────────────────────────────────────────────
        function enterPlaceMode() {
            if (!sigDrawn) { alert('Please draw your signature first.'); return; }
            placementMode = true;
            document.getElementById('modeBadge').style.display = 'block';
            document.getElementById('btnPlaceMode').classList.add('disabled');

            // Show ghost
            const ghost = document.getElementById('signatureGhost');
            document.getElementById('ghostImg').src = sc.toDataURL('image/png');
            ghost.style.display = 'block';

            // Track mouse on the wrapper
            document.getElementById('pdfPageWrapper').addEventListener('mousemove', moveGhost);
        }

        function exitPlaceMode() {
            placementMode = false;
            document.getElementById('modeBadge').style.display = 'none';
            document.getElementById('signatureGhost').style.display = 'none';
            const btn = document.getElementById('btnPlaceMode');
            if (btn) btn.classList.remove('disabled');
            document.getElementById('pdfPageWrapper').removeEventListener('mousemove', moveGhost);
        }

        function moveGhost(e) {
            const wrapper = document.getElementById('pdfPageWrapper');
            const rect = wrapper.getBoundingClientRect();
            const ghost = document.getElementById('signatureGhost');
            ghost.style.left = (e.clientX - rect.left - 30) + 'px';
            ghost.style.top = (e.clientY - rect.top - 15) + 'px';
        }

        // ── Click to Place ──────────────────────────────────────────────────
        function handlePageClick(e) {
            if (!placementMode) return;

            const wrapper = document.getElementById('pdfPageWrapper');
            const canvas = document.getElementById('pdfCanvas');
            const rect = wrapper.getBoundingClientRect();

            // Position as percentage of canvas
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;
            const percentX = (clickX / canvas.width) * 100;
            const percentY = (clickY / canvas.height) * 100;

            // Place signature overlay
            const overlay = document.getElementById('signatureOverlay');
            const sigDataUrl = sc.toDataURL('image/png');
            document.getElementById('overlayImg').src = sigDataUrl;
            overlay.style.left = clickX + 'px';
            overlay.style.top = clickY + 'px';
            overlay.style.display = 'block';

            // Fill hidden form fields
            document.getElementById('inputSigData').value = sigDataUrl;
            document.getElementById('inputSigPage').value = currentPage;
            document.getElementById('inputSigX').value = percentX.toFixed(2);
            document.getElementById('inputSigY').value = percentY.toFixed(2);

            // Enable confirm button
            document.getElementById('btnConfirm').disabled = false;

            exitPlaceMode();
        }

        function confirmSign() {
            if (!document.getElementById('inputSigData').value) {
                alert('Please place your signature on the document first.');
                return false;
            }
            return confirm('Are you sure you want to sign this document? This will mark it as signed.');
        }
    </script>

@endsection
