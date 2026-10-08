@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title">My Submissions for: {{ $meeting->title }}</h4>
{{--                <a href="{{ route('division.head.submit', $meeting->id) }}" class="btn btn-success">--}}
{{--                    + Add New Paper--}}
{{--                </a>--}}
            </div>

            @forelse($papers as $paper)
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $paper->title }}</h5>
                            <small class="text-muted">Submitted on {{ $paper->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                        <span class="badge
                        @if($paper->status === 'Approved') bg-success
                        @elseif($paper->status === 'Rejected') bg-danger
                        @else bg-warning text-dark @endif">
                        {{ $paper->status }}
                    </span>
                    </div>

                    <div class="card-body">
                        <!-- Main Paper -->
                        <p>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#fileModal{{ $paper->id }}">
                                📄 View Main Paper
                            </button>
                        </p>

                        <!-- Annexures -->
                        @if($paper->annexures->count())
                            <h6>Annexures</h6>
                            <ul class="list-group">
                                @foreach($paper->annexures as $annexure)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $annexure->title }}</strong><br>
                                            <small class="text-muted">{{ $annexure->description }}</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#annexureModal{{ $annexure->id }}">
                                            View
                                        </button>
                                    </li>

                                    <!-- Annexure Modal -->
                                    <div class="modal fade" id="annexureModal{{ $annexure->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $annexure->title }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body" style="height:80vh">
                                                    <iframe src="{{ asset('storage/'.$annexure->file) }}" width="100%" height="100%" frameborder="0"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No annexures attached.</p>
                        @endif
                    </div>
                </div>

                <!-- Main Paper Modal -->
                <div class="modal fade" id="fileModal{{ $paper->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $paper->title }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" style="height:80vh">
                                <iframe src="{{ asset('storage/'.$paper->file) }}" width="100%" height="100%" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning">No board papers submitted yet.</div>
            @endforelse

        </div>
    </div>

@endsection
