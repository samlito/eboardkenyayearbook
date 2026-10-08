
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
                            {{--                            <li><strong><i class="bi bi-geo-alt-fill text-danger"></i> Venue:</strong> {{ $meeting->venue }}</li>--}}
                            {{--                            <li><strong><i class="bi bi-tag-fill text-warning"></i> Type:</strong> {{ $meeting->type }}</li>--}}
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
                {{--                <h4 class="fw-bold mb-3 text-dark">📌 Agenda</h4>--}}
                <div class="border-start border-3 border-info ps-3">
                    {{--                    <p class="fs-5">{!! nl2br(e($meeting->agenda ?? '-')) !!}</p>--}}
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
                                {{--                                <th>Submitted By</th>--}}
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($meeting->boardPapers as $index => $paper)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $paper->title }}</td>
                                    {{--                                    <td>{{ $paper->user->name ?? 'N/A' }}</td>--}}
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

                                                        <!-- Annexure Modal -->
                                                        <div class="modal fade" id="annexureModal{{ $annexure->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">{{ $annexure->title }}</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body" style="height:80vh;">
                                                                        <iframe src="{{ asset('storage/' . $annexure->file) }}" frameborder="0" width="100%" height="100%"></iframe>
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

                                <!-- Main Paper Modal -->
                                <div class="modal fade" id="mainModal{{ $paper->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $paper->title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body" style="height:80vh;">
                                                <iframe src="{{ asset('storage/' . $paper->file) }}" frameborder="0" width="100%" height="100%"></iframe>
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
@endsection
