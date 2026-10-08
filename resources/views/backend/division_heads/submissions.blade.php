{{--@extends('admin_dashboard')--}}
{{--@section('admin')--}}

{{--    <div class="content">--}}
{{--        <div class="container-fluid">--}}

{{--            <div class="d-flex justify-content-between align-items-center mb-3">--}}
{{--                <h4 class="page-title">My Submissions for: {{ $meeting->title }}</h4>--}}
{{--                <a href="{{ route('division.head.submit', $meeting->id) }}" class="btn btn-success">--}}
{{--                    + Add New Paper--}}
{{--                </a>--}}
{{--            </div>--}}

{{--            @forelse($papers as $paper)--}}
{{--                <div class="card shadow-sm mb-4">--}}
{{--                    <div class="card-header d-flex justify-content-between align-items-center">--}}
{{--                        <div>--}}
{{--                            <h5 class="mb-0">{{ $paper->title }}</h5>--}}
{{--                            <small class="text-muted">Submitted on {{ $paper->created_at->format('d M Y, h:i A') }}</small>--}}
{{--                        </div>--}}
{{--                        <span class="badge--}}
{{--                        @if($paper->status === 'Approved') bg-success--}}
{{--                        @elseif($paper->status === 'Rejected') bg-danger--}}
{{--                        @else bg-warning text-dark @endif">--}}
{{--                        {{ $paper->status }}--}}
{{--                    </span>--}}
{{--                    </div>--}}

{{--                    <div class="card-body">--}}
{{--                        <!-- Main Paper -->--}}
{{--                        <p>--}}
{{--                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#fileModal{{ $paper->id }}">--}}
{{--                                📄 View Main Paper--}}
{{--                            </button>--}}
{{--                        </p>--}}

{{--                        <!-- Annexures -->--}}
{{--                        @if($paper->annexures->count())--}}
{{--                            <h6>Annexures</h6>--}}
{{--                            <ul class="list-group">--}}
{{--                                @foreach($paper->annexures as $annexure)--}}
{{--                                    <li class="list-group-item d-flex justify-content-between align-items-center">--}}
{{--                                        <div>--}}
{{--                                            <strong>{{ $annexure->title }}</strong><br>--}}
{{--                                            <small class="text-muted">{{ $annexure->description }}</small>--}}
{{--                                        </div>--}}
{{--                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#annexureModal{{ $annexure->id }}">--}}
{{--                                            View--}}
{{--                                        </button>--}}
{{--                                    </li>--}}

{{--                                    <!-- Annexure Modal -->--}}
{{--                                    <div class="modal fade" id="annexureModal{{ $annexure->id }}" tabindex="-1" aria-hidden="true">--}}
{{--                                        <div class="modal-dialog modal-xl">--}}
{{--                                            <div class="modal-content">--}}
{{--                                                <div class="modal-header">--}}
{{--                                                    <h5 class="modal-title">{{ $annexure->title }}</h5>--}}
{{--                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>--}}
{{--                                                </div>--}}
{{--                                                <div class="modal-body" style="height:80vh">--}}
{{--                                                    <iframe src="{{ asset('storage/'.$annexure->file) }}" width="100%" height="100%" frameborder="0"></iframe>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}
{{--                            </ul>--}}
{{--                        @else--}}
{{--                            <p class="text-muted">No annexures attached.</p>--}}
{{--                        @endif--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- Main Paper Modal -->--}}
{{--                <div class="modal fade" id="fileModal{{ $paper->id }}" tabindex="-1" aria-hidden="true">--}}
{{--                    <div class="modal-dialog modal-xl">--}}
{{--                        <div class="modal-content">--}}
{{--                            <div class="modal-header">--}}
{{--                                <h5 class="modal-title">{{ $paper->title }}</h5>--}}
{{--                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>--}}
{{--                            </div>--}}
{{--                            <div class="modal-body" style="height:80vh">--}}
{{--                                <iframe src="{{ asset('storage/'.$paper->file) }}" width="100%" height="100%" frameborder="0"></iframe>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @empty--}}
{{--                <div class="alert alert-warning">No board papers submitted yet.</div>--}}
{{--            @endforelse--}}

{{--        </div>--}}
{{--    </div>--}}

{{--@endsection--}}
{{-- resources/views/backend/division_heads/submissions.blade.php --}}
@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title">My Submissions for: {{ $meeting->title }}</h4>
                <a href="{{ route('division.head.submit', $meeting->id) }}" class="btn btn-success">+ Add New Paper</a>
            </div>

            @forelse($papers as $paper)
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $paper->title }}</h5>
                            <small class="text-muted">Submitted on {{ $paper->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                        <div class="d-flex gap-2">
            <span class="badge
                @if($paper->status === 'Approved') bg-success
                @elseif($paper->status === 'Rejected') bg-danger
                @else bg-warning text-dark @endif">
                {{ $paper->status }}
            </span>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPaperModal{{ $paper->id }}">
                                Edit
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Main Paper --}}
                        <p>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#fileModal{{ $paper->id }}">
                                📄 View Main Paper
                            </button>
                        </p>

                        {{-- Add Annexure --}}
                        <div class="border rounded p-3 mb-3">
                            <form action="{{ route('division.head.annexure.store', $paper->id) }}" method="POST" enctype="multipart/form-data" class="row g-2">
                                @csrf
                                <div class="col-md-3">
                                    <input type="text" name="title" class="form-control" placeholder="Annexure title" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="description" class="form-control" placeholder="Description (optional)">
                                </div>
                                <div class="col-md-3">
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button class="btn btn-success btn-sm">Add</button>
                                </div>
                            </form>
                        </div>

                        {{-- Annexures list --}}
                        @if($paper->annexures->count())
                            <h6>Annexures</h6>
                            <ul class="list-group">
                                @foreach($paper->annexures as $annexure)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $annexure->title }}</strong><br>
                                            <small class="text-muted">{{ $annexure->description }}</small>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#annexureModal{{ $annexure->id }}">View</button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editAnnexureModal{{ $annexure->id }}">Edit</button>
                                            <form action="{{ route('division.head.annexure.destroy', $annexure->id) }}" method="POST" onsubmit="return confirm('Remove annexure?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </li>

                                    {{-- Annexure View Modal --}}
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

                                    {{-- Annexure Edit Modal --}}
                                    <div class="modal fade" id="editAnnexureModal{{ $annexure->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('division.head.annexure.update', $annexure->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Annexure</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" name="title" class="form-control" value="{{ $annexure->title }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <input type="text" name="description" class="form-control" value="{{ $annexure->description }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Replace File (optional)</label>
                                                            <input type="file" name="file" class="form-control">
                                                            <small class="text-muted">Leave blank to keep current file.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary">Save Changes</button>
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </form>
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

                {{-- Main Paper View Modal --}}
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

                {{-- Main Paper Edit Modal --}}
                <div class="modal fade" id="editPaperModal{{ $paper->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('division.head.paper.update', $paper->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PATCH')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Board Paper</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="title" class="form-control" value="{{ $paper->title }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3">{{ $paper->description }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Replace Main File (optional)</label>
                                        <input type="file" name="file" class="form-control">
                                        <small class="text-muted">Leave blank to keep current file.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary">Save Changes</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @empty
                <div class="alert alert-warning">No board papers submitted yet.</div>
            @endforelse

        </div>
    </div>

@endsection
