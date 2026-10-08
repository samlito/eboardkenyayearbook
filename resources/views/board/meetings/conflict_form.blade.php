@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        <h4 class="mb-4">✍️ Conflict of Interest Declaration</h4>

        <div class="card shadow">
            <div class="card-body">
                <p>
                    <strong>Meeting Title:</strong> {{ $meeting->title }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}<br>
                    {{-- <strong>Venue:</strong> {{ $meeting->venue }}<br> --}}
                    {{-- <strong>Committee:</strong> {{ $meeting->committee->name ?? 'N/A' }} --}}
                </p>

                <hr>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ✅ Declaration Form --}}
                <form method="POST" action="{{ route('conflict.store', $meeting->id) }}">
                    @csrf

                    <div class="mb-3">
                        <label>Do you have any conflict of interest?</label>
                        <select name="conflict_status" id="conflictStatus" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>

                    <div id="conflictDetails" style="display:none;">
                        <div class="mb-3">
                            <label>Name of Person / Issue</label>
                            <input type="text" name="person_name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Address</label>
                            <input type="text" name="person_address" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Nature of the Interest</label>
                            <input type="text" name="interest_nature" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Estimated Value (KShs.)</label>
                            <input type="number" name="interest_value" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Date the Interest Ceased</label>
                            <input type="date" name="interest_ceased_on" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Remarks / Signature</label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>
                    </div>

{{--                    <button type="submit" class="btn btn-warning">Submit Declaration</button>--}}
                    <button type="submit" class="btn btn-warning"
                            onclick="return confirm('Are you sure you want to submit your declaration?');">
                        Submit Declaration
                    </button>

                </form>

                {{-- ✅ OTP Modal (shown after saving, if $declaration is set) --}}
                @if(session('success') && isset($declaration))
                    <div class="modal fade show" id="otpModal" style="display:block;" aria-modal="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Confirm Your Declaration</h5>
                                </div>
                                <div class="modal-body">
                                    <p>We sent a 6-digit OTP to your email <strong>{{ auth()->user()->email }}</strong>.</p>
                                    <p>Please enter it below to confirm your declaration.</p>

                                    <input type="text" id="otpInput" class="form-control text-center mb-3" maxlength="6" placeholder="Enter OTP">

                                    <div id="otpTimer" class="text-danger fw-bold mb-2"></div>

                                    <button id="verifyOtpBtn" data-id="{{ $declaration->id }}" class="btn btn-success w-100">
                                        Verify OTP
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ✅ JS --}}
    <script>
        // Toggle details if conflict = Yes
        document.getElementById("conflictStatus").addEventListener("change", function() {
            let details = document.getElementById("conflictDetails");
            details.style.display = this.value === "Yes" ? "block" : "none";
        });

        // OTP verification
        document.addEventListener("DOMContentLoaded", function() {
            let verifyBtn = document.getElementById("verifyOtpBtn");
            if (verifyBtn) {
                verifyBtn.addEventListener("click", function() {
                    let otp = document.getElementById("otpInput").value.trim();
                    let id = this.getAttribute("data-id");

                    fetch(`/conflict/verify/${id}`, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ otp: otp })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("✅ Verified", "Your declaration is now confirmed!", "success")
                                    .then(() => window.location.href = "{{ route('meetings.active') }}");
                            } else {
                                Swal.fire("❌ Error", data.message, "error");
                            }
                        })
                        .catch(() => Swal.fire("⚠️ Error", "Something went wrong.", "error"));
                });
            }
        });
    </script>
@endsection
