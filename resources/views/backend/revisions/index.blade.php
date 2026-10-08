@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">
            <h4 class="mb-4">📄 Board Papers Needing Revision</h4>

            @if($papers->isEmpty())
                <div class="alert alert-info">No papers currently require revision.</div>
            @endif

            @foreach($papers as $paper)
                <div class="card shadow-sm mb-4 border-left-warning">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-primary">
                            {{ $paper->title }}
                            <small class="text-muted d-block">Meeting: {{ $paper->meeting->title ?? 'N/A' }}</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">CEO Comments:</label>
                            <div class="border rounded p-2 bg-light">
                                {{ $paper->ceo_comments }}
                            </div>
                        </div>

                        <form action="{{ route('manager.revisions.resubmit', $paper->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Upload Revised Paper (PDF/DOC)</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Supporting Document (optional)</label>
                                <input type="file" name="supporting_docs" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Submit Revised Paper
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection
