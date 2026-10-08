@extends('admin_dashboard')

@section('admin')
    @php use Carbon\Carbon; @endphp

    <style>
        :root { --brand: {{ setting('primary_color', '#1a1a1a') }}; --brand-soft: #fdecee; }
        .card-shadow { border: 0; border-radius: 16px; box-shadow: 0 8px 22px rgba(0,0,0,.06); }
        .text-brand { color: var(--brand) !important; }
        .bg-brand-soft { background-color: var(--brand-soft) !important; }
        .waiting-icon {
            font-size: 72px; color: var(--brand); opacity: .6;
            animation: waitBounce 2s ease-in-out infinite;
        }
        @keyframes waitBounce {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
    </style>

    <div class="content">
        <div class="container-fluid">

            <div class="row justify-content-center mt-4">
                <div class="col-md-8 col-lg-6">

                    <div class="card card-shadow">
                        <div class="card-body text-center py-5 px-4">

                            <div class="mb-3">
                                <i class="mdi mdi-clock-alert-outline waiting-icon"></i>
                            </div>

                            <h4 class="fw-bold mb-2">Meeting Not Yet Initiated</h4>

                            <div class="bg-brand-soft rounded-3 p-3 text-start mb-4">
                                <div class="mb-2">
                                    <span class="text-muted small">Meeting</span><br>
                                    <strong>{{ $meeting->title }}</strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small">Committee</span><br>
                                    <strong>{{ optional($meeting->boardCommittee)->name ?? 'Board Meeting' }}</strong>
                                </div>
                                <div>
                                    <span class="text-muted small">Date</span><br>
                                    <strong>
                                        {{ Carbon::parse($meeting->meeting_date)->isoFormat('dddd, D MMMM YYYY') }}
                                        @if($meeting->meeting_time)
                                            &middot; {{ Carbon::parse($meeting->meeting_time)->format('g:i A') }}
                                        @endif
                                    </strong>
                                </div>
                                @if($meeting->venue)
                                    <div class="mt-2">
                                        <span class="text-muted small">Venue</span><br>
                                        <strong>{{ $meeting->venue }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="alert alert-warning text-start mb-4">
                                <i class="mdi mdi-information-outline me-1"></i>
                                The meeting has not been started by the Corporation Secretary or Deputy Director Internal Audit.
                                Please wait for the meeting to be initiated before proceeding.
                            </div>

                            <a href="{{ route('board.portal.active') }}" class="btn btn-outline-primary">
                                <i class="mdi mdi-arrow-left"></i> Back to Active Meetings
                            </a>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
