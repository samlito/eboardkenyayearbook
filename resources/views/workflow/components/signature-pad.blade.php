{{-- Signature Pad Component --}}
{{-- Usage: @include('workflow.components.signature-pad', ['inputName' => 'ceo_signature', 'label' => 'CEO Signature']) --}}

@php
    $inputName = $inputName ?? 'signature';
    $label = $label ?? 'Signature';
    $padId = 'sig-pad-' . uniqid();
@endphp

<div class="twwda-signature-pad mb-3">
    <label class="form-label fw-semibold">{{ $label }}</label>
    <div class="border rounded p-2 bg-white position-relative" style="height: 200px;">
        <canvas id="{{ $padId }}" style="width: 100%; height: 100%; cursor: crosshair;"></canvas>
        <button type="button" class="btn btn-sm btn-outline-secondary position-absolute" style="top: 5px; right: 5px;" onclick="clearSignature_{{ str_replace('-', '_', $padId) }}()">
            <i class="mdi mdi-eraser"></i> Clear
        </button>
    </div>
    <input type="hidden" name="{{ $inputName }}" id="{{ $padId }}-input">
    <small class="text-muted">Use your mouse or stylus to sign above.</small>
</div>

@push('scripts')
<script>
(function() {
    var canvas = document.getElementById('{{ $padId }}');
    var ctx = canvas.getContext('2d');
    var drawing = false;
    var input = document.getElementById('{{ $padId }}-input');
    var isReady = false;

    function resizeCanvas() {
        var rect = canvas.parentElement.getBoundingClientRect();
        var w = Math.floor(rect.width - 16);
        var h = Math.floor(rect.height - 16);

        // Only resize if the container is actually visible (has real dimensions)
        if (w > 10 && h > 10) {
            canvas.width = w;
            canvas.height = h;
            isReady = true;
        }
    }

    // Initial resize (works if canvas is visible on page load)
    resizeCanvas();

    // Handle window resize
    window.addEventListener('resize', resizeCanvas);

    // Handle canvas inside hidden containers (modals, tabs, accordions, etc.)
    // IntersectionObserver fires when the canvas becomes visible
    if (window.IntersectionObserver) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !isReady) {
                    // Small delay to ensure the container has its final dimensions
                    setTimeout(resizeCanvas, 50);
                }
            });
        }, { threshold: 0.1 });
        observer.observe(canvas);
    }

    // Also listen for Bootstrap modal shown events (covers modals specifically)
    var modal = canvas.closest('.modal');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function() {
            setTimeout(resizeCanvas, 50);
        });
    }

    // Also listen for Bootstrap tab/pill shown events
    document.addEventListener('shown.bs.tab', function() {
        if (canvas.offsetParent !== null) {
            resizeCanvas();
        }
    });

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var clientX = e.touches ? e.touches[0].clientX : e.clientX;
        var clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: clientX - rect.left, y: clientY - rect.top };
    }

    function startDraw(e) {
        // Ensure canvas is properly sized before first stroke
        if (!isReady) resizeCanvas();
        drawing = true;
        ctx.beginPath();
        var p = getPos(e);
        ctx.moveTo(p.x, p.y);
    }

    function draw(e) {
        if (!drawing) return;
        var p = getPos(e);
        ctx.lineTo(p.x, p.y);
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.stroke();
    }

    function endDraw() {
        if (!drawing) return;
        drawing = false;
        // Save signature data on every stroke end
        input.value = canvas.toDataURL('image/png');
    }

    // Mouse events
    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', endDraw);
    canvas.addEventListener('mouseleave', function() {
        if (drawing) endDraw();
    });

    // Touch events (stylus / mobile)
    canvas.addEventListener('touchstart', function(e) { e.preventDefault(); startDraw(e); });
    canvas.addEventListener('touchmove', function(e) { e.preventDefault(); draw(e); });
    canvas.addEventListener('touchend', function(e) { e.preventDefault(); endDraw(); });

    window['clearSignature_{{ str_replace("-", "_", $padId) }}'] = function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        input.value = '';
    };
})();
</script>
@endpush
