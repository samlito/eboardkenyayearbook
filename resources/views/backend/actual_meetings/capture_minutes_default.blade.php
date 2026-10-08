@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        <h3 class="mb-3">📝 Capture Minutes – {{ $meeting->title }}</h3>
        <p><strong>Committee:</strong> {{ $meeting->committee->name ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->date)->format('l, d M Y') }}</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('actual.meetings.minutes.store', $meeting->id) }}">
            @csrf

            <div class="mb-3">
                <label for="discussion" class="form-label fw-bold">Discussions <span class="text-danger">*</span></label>
                <textarea name="discussion" id="discussion" class="form-control" rows="5" required>{{ old('discussion') }}</textarea>
                @error('discussion') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="comments" class="form-label fw-bold">Comments (optional)</label>
                <textarea name="comments" id="comments" class="form-control" rows="3">{{ old('comments') }}</textarea>
                @error('comments') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="resolutions" class="form-label fw-bold">Resolutions <span class="text-danger">*</span></label>
                <textarea name="resolutions" id="resolutions" class="form-control" rows="4" required>{{ old('resolutions') }}</textarea>
                @error('resolutions') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="recommendations" class="form-label fw-bold">Recommendations (optional)</label>
                <textarea name="recommendations" id="recommendations" class="form-control" rows="3">{{ old('recommendations') }}</textarea>
                @error('recommendations') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">
                    💾 Save Minutes
                </button>
            </div>
        </form>
    </div>
@endsection
