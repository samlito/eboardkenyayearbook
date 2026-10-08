@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Sign Document</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ route('quicksign.store') }}" method="POST" id="signForm">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Document Title <span class="text-danger">*</span></label>
                                    <input type="text" name="document_title" class="form-control" value="{{ old('document_title') }}" required>
                                    @error('document_title') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Prepared By <span class="text-danger">*</span></label>
                                    <input type="text" name="prepared_by" class="form-control" value="{{ old('prepared_by', Auth::user()->name) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" rows="3" class="form-control" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Signer Name <span class="text-danger">*</span></label>
                                    <input type="text" name="signer_name" class="form-control" value="{{ old('signer_name', Auth::user()->name) }}" required>
                                </div>

                                <!-- Signature Pad -->
                                <div class="mb-3">
                                    <label class="form-label">Signature <span class="text-danger">*</span></label>
                                    <div style="border: 2px dashed #ccc; border-radius: 6px; display: inline-block; background: #fafafa;">
                                        <canvas id="sigCanvas" width="460" height="160" style="cursor: crosshair; touch-action: none;"></canvas>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearSig()">Clear</button>
                                    <input type="hidden" name="signature_data" id="sigData">
                                    @error('signature_data') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <button type="submit" class="btn btn-success" onclick="return captureSig()">
                                    <i class="mdi mdi-check"></i> Sign & Save
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const c = document.getElementById('sigCanvas'), ctx = c.getContext('2d');
        let drawing = false, drawn = false, lx = 0, ly = 0;
        ctx.strokeStyle = '#000'; ctx.lineWidth = 2.5; ctx.lineCap = 'round';

        function pos(e) {
            const r = c.getBoundingClientRect(), sx = c.width/r.width, sy = c.height/r.height;
            const t = e.touches ? e.touches[0] : e;
            return { x: (t.clientX - r.left)*sx, y: (t.clientY - r.top)*sy };
        }
        function down(e) { e.preventDefault(); drawing = true; const p = pos(e); lx = p.x; ly = p.y; }
        function move(e) { e.preventDefault(); if (!drawing) return; drawn = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(lx,ly); ctx.lineTo(p.x,p.y); ctx.stroke(); lx = p.x; ly = p.y; }
        function up(e) { if(e) e.preventDefault(); drawing = false; }

        c.addEventListener('mousedown', down); c.addEventListener('mousemove', move);
        c.addEventListener('mouseup', up); c.addEventListener('mouseout', up);
        c.addEventListener('touchstart', down); c.addEventListener('touchmove', move);
        c.addEventListener('touchend', up);

        function clearSig() { ctx.clearRect(0,0,c.width,c.height); drawn = false; }
        function captureSig() {
            if (!drawn) { alert('Please sign before submitting.'); return false; }
            document.getElementById('sigData').value = c.toDataURL('image/png');
            return true;
        }
    </script>

@endsection
