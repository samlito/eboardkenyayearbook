@extends('admin_dashboard')

@section('admin')
    <div class="container-fluid">
        <h4 class="mb-3">{{ $notice->title }}</h4>

        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($notice->date)->format('d M Y') }}</p>
                <p><strong>Venue:</strong> {{ $notice->venue }}</p>
                <p><strong>Committee:</strong> {{ optional($notice->committee)->name }}</p>

                <hr>
                <h5>Attached Board Papers</h5>
{{--                @forelse($notice->notice->attachments as $attachment)--}}
{{--                    <div class="mb-2">--}}
{{--                        📎 <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">{{ $attachment->title }}</a>--}}
{{--                    </div>--}}
{{--                @empty--}}
{{--                    <p>No attachments found.</p>--}}
{{--                @endforelse--}}
            </div>
        </div>

        <a href="{{ route('board.notices.index') }}" class="btn btn-secondary">← Back to Notices</a>
    </div>
@endsection
