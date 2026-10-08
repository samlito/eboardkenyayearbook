@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Pending Board Notices</h4>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Meeting</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Submitted On</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($notices as $notice)
                            <tr>
                                <td>{{ $notice->meeting->title }}</td>
                                <td>{{ $notice->meeting->date }}</td>
                                <td>{{ $notice->meeting->venue }}</td>
                                <td>{{ $notice->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('ceo.notice.review', $notice->id) }}" class="btn btn-sm btn-primary">Review</a>
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
