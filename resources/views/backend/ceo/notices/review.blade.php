{{--@extends('admin_dashboard')--}}
{{--@section('admin')--}}

{{--    <div class="content">--}}
{{--        <div class="container-fluid">--}}
{{--            <h4 class="page-title">Review Notice for: {{ $notice->meeting->title }}</h4>--}}

{{--            <div class="card">--}}
{{--                <div class="card-body">--}}

{{--                    <h5 class="text-info">Notice Content</h5>--}}
{{--                    <div class="border p-3 mb-4" style="white-space: pre-wrap;">--}}
{{--                        {{ $notice->content }}--}}
{{--                    </div>--}}

{{--                    <form action="{{ route('ceo.notice.decision', $notice->id) }}" method="POST">--}}
{{--                        @csrf--}}

{{--                        <div class="form-group mb-3">--}}
{{--                            <label>CEO Comments (optional)</label>--}}
{{--                            <textarea name="ceo_comments" rows="4" class="form-control" placeholder="Add feedback or comments..."></textarea>--}}
{{--                        </div>--}}

{{--                        <div class="form-group">--}}
{{--                            <label>Decision</label><br>--}}
{{--                            <button type="submit" name="action" value="Approved" class="btn btn-success me-2">Approve</button>--}}
{{--                            <button type="submit" name="action" value="Rejected" class="btn btn-danger">Request Changes</button>--}}
{{--                        </div>--}}

{{--                    </form>--}}

{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}

{{--@endsection--}}
@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Review Notice for: {{ $notice->meeting->title }}</h4>

            <div class="card">
                <div class="card-body">

                    <!-- Reference Info -->
                    <div class="mb-3">
                        <strong>Reference Number:</strong> {{ $notice->reference_number ?? '-' }} <br>
                        <strong>Reference Date:</strong> {{ $notice->reference_date ? \Carbon\Carbon::parse($notice->reference_date)->format('d M Y') : '-' }}
                    </div>

                    <h5 class="text-info">Notice Content</h5>
                    <div class="border p-3 mb-4" style="white-space: pre-wrap;">
                        {{ $notice->content }}
                    </div>

                    <form action="{{ route('ceo.notice.decision', $notice->id) }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label>CEO Comments (optional)</label>
                            <textarea name="ceo_comments" rows="4" class="form-control" placeholder="Add feedback or comments..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Decision</label><br>
                            <button type="submit" name="action" value="Approved" class="btn btn-success me-2">Approve</button>
                            <button type="submit" name="action" value="Rejected" class="btn btn-danger">Request Changes</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
