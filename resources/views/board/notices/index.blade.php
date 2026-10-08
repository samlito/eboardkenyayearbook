@extends('admin_dashboard')

@section('admin')
    <div class="container-fluid">
        <h4 class="mb-4">📑 Board Meeting Notices</h4>

        @if($notices->count())
            <div class="card shadow border-0 rounded-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover table-striped">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Meeting Title</th>
                                <th>Date</th>
{{--                                <th>Venue</th>--}}
                                <th>Committee</th>
                                <th class="text-center">Notices</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notices as $index => $meeting)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $meeting->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($meeting->date)->format('l, jS F Y') }}</td>
{{--                                    <td>{{ $meeting->venue }}</td>--}}
                                    <td>{{ optional($meeting->committee)->name }}</td>
                                    <td class="text-center">
                                        @if($meeting->officialNotice10)
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 me-2"
                                                  role="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#notice10Modal{{ $meeting->id }}">
                                                <i class="fa-solid fa-file-pdf me-1"></i> 10-Day
                                            </span>
                                        @endif

                                        @if($meeting->officialNotice)
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2"
                                                  role="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#notice3Modal{{ $meeting->id }}">
                                                <i class="fa-solid fa-file-pdf me-1"></i> 3-Day
                                            </span>
                                        @endif
                                    </td>
                                </tr>


                                @if($meeting->officialNotice10)
                                    <div class="modal fade" id="notice10Modal{{ $meeting->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fa-solid fa-file-pdf me-2"></i>
                                                        10-Day Notice – {{ $meeting->title }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    <iframe src="{{ route('board.notices.preview10day', $meeting->officialNotice10->id) }}"
                                                            style="width:100%; height:80vh;" frameborder="0"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif



                                @if($meeting->officialNotice)
                                    <div class="modal fade" id="notice3Modal{{ $meeting->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fa-solid fa-file-pdf me-2"></i>
                                                        3-Day Notice – {{ $meeting->title }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    <iframe src="{{ route('actual.meetings.notice.preview', $meeting->id) }}"
                                                            style="width:100%; height:80vh;" frameborder="0"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">No meeting notices available at this time.</div>
        @endif
    </div>


@endsection

