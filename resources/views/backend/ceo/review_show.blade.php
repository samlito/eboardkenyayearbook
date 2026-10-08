@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <h4 class="page-title">Review Papers for: {{ $meeting->title }}</h4>
            @if($allApproved)
                <div class="text-end mb-3">
                    <form action="{{ route('ceo.send.notice', $meeting->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Send Notice to Board Members
                        </button>
                    </form>
                </div>


            @endif

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Division Head</th>
                            <th>Title</th>
                            <th>Paper</th>
                            <th>Supporting Docs</th>
                            <th>Status</th>
                            <th>CEO Comments</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($papers as $paper)
                            <tr>
                                <td>{{ $paper->user->name }}</td>
                                <td>{{ $paper->title }}</td>
                                <td>
{{--                                    <a href="{{ asset('storage/' . $paper->file) }}" target="_blank">View</a>--}}
                                    <a href="javascript:void(0)" onclick="openPdfModal('{{ asset('storage/' . $paper->file) }}')">View</a>

                                </td>
{{--                                <td>--}}
{{--                                    @if($paper->supporting_docs)--}}
{{--                                        <a href="{{ asset('storage/' . $paper->supporting_docs) }}" target="_blank">View</a>--}}
{{--                                        <a href="javascript:void(0)" onclick="openPdfModal('{{ asset('storage/' . $paper->supporting_docs) }}')">View</a>--}}

{{--                                    @else--}}
{{--                                        <span class="text-muted">N/A</span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
                                <td>
                                    @if($paper->annexures->count())
                                        <ul class="list-unstyled">
                                            @foreach($paper->annexures as $annexure)
                                                <li>
                                                    <a href="javascript:void(0)"
                                                       onclick="openPdfModal('{{ asset('storage/'.$annexure->file) }}')">
                                                        📎 {{ $annexure->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                <td>
                                    @if($paper->status === 'Approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($paper->status === 'Needs Revision')
                                        <span class="badge bg-danger">Needs Revision</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $paper->ceo_comments ?? '---' }}</td>
                                <td>
{{--                                    <form method="POST" action="{{ route('ceo.board.paper.decision', $paper->id) }}">--}}
{{--                                        @csrf--}}
{{--                                        <select name="status" class="form-control form-select mb-1" required>--}}
{{--                                            <option value="">--Decision--</option>--}}
{{--                                            <option value="Approved">Approve</option>--}}
{{--                                            <option value="Needs Revision">Request Changes</option>--}}
{{--                                        </select>--}}
{{--                                        <textarea name="ceo_comments" class="form-control mb-2" rows="2" placeholder="Optional comments..."></textarea>--}}
{{--                                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>--}}
{{--                                    </form>--}}
                                    @if($paper->status !== 'Approved')
                                        <button class="btn btn-sm btn-primary" onclick="showDecisionModal({{ $paper->id }}, '{{ $paper->status }}', `{{ $paper->ceo_comments }}`)">Take Action</button>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <!-- Modal Viewer -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Document Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe src="" id="pdfFrame" frameborder="0" width="100%" height="600px"></iframe>
                </div>
            </div>
        </div>
    </div>


        <script>
            function openPdfModal(url) {
                document.getElementById('pdfFrame').src = url;
                var myModal = new bootstrap.Modal(document.getElementById('pdfModal'), {});
                myModal.show();
            }
        </script>

    <!-- Decision Modal -->
    <div class="modal fade" id="decisionModal" tabindex="-1" aria-labelledby="decisionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="decisionForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">CEO Decision</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <select name="status" class="form-control mb-2" required>
                            <option value="">--Select Decision--</option>
                            <option value="Approved">Approve</option>
                            <option value="Needs Revision">Request Changes</option>
                        </select>
                        <textarea name="ceo_comments" class="form-control" rows="3" placeholder="Add comments..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


        <script>
            function showDecisionModal(paperId, status, comment) {
                const form = document.getElementById('decisionForm');
                form.action = `/ceo/board-paper/decision/${paperId}`; // Adjust if using named route
                form.status.value = status;
                form.ceo_comments.value = comment ?? '';
                var modal = new bootstrap.Modal(document.getElementById('decisionModal'));
                modal.show();
            }
        </script>


@endsection
