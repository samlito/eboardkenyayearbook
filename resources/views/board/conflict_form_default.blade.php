@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        <h4 class="mb-4">✍️ Conflict of Interest Declaration</h4>

        <div class="card shadow">
            <div class="card-body">
                <p>
                    <strong>Meeting Title:</strong> {{ $meeting->title }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}<br>
                    {{--                    <strong>Venue:</strong> {{ $meeting->venue }}<br>--}}
                    {{--                    <strong>Committee:</strong> {{ $meeting->committee->name ?? 'N/A' }}--}}
                </p>

                <hr>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif


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

                    <button type="submit" class="btn btn-warning">Submit Declaration</button>
                </form>
                <!-- OTP Modal -->
                <div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Confirm Your Declaration</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>We have sent a 6-digit OTP to your email <strong>{{ auth()->user()->email }}</strong>.</p>
                                <p>Please enter it below to confirm your Conflict of Interest declaration.</p>

                                <div class="mb-3">
                                    <input type="text" id="otpInput" class="form-control text-center" maxlength="6" placeholder="Enter OTP">
                                </div>

                                <div id="otpTimer" class="text-danger fw-bold mb-2"></div>

                                <button id="verifyOtpBtn" class="btn btn-success w-100">Verify OTP</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        document.getElementById("verifyOtpBtn").addEventListener("click", function() {
            let otp = document.getElementById("otpInput").value;

            fetch("{{ route('conflict.verify', $declaration->id ?? 0) }}", {
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
                        alert("✅ Declaration confirmed successfully!");
                        location.reload();
                    } else {
                        alert("❌ " + data.message);
                    }
                });
        });

    </script>

    <script>
        document.getElementById("conflictStatus").addEventListener("change", function() {
            let details = document.getElementById("conflictDetails");
            if(this.value === "Yes") {
                details.style.display = "block";
            } else {
                details.style.display = "none";
            }
        });
    </script>

@endsection
