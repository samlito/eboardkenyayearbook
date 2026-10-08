@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title">Board Meetings - Papers Ready for Review</h4>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    ← Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Notified On</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($meetings as $meeting)
                            <tr>
                                <td>{{ $meeting->title }}</td>
                                <td>{{ $meeting->date }}</td>
                                <td>{{ \Carbon\Carbon::parse($meeting->ceo_notified_at)->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('ceo.board.papers.show', $meeting->id) }}" class="btn btn-primary btn-sm">
                                        Review Papers
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
