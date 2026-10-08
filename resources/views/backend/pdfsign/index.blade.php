@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ route('pdfsign.upload') }}" class="btn btn-info btn-sm">
                                <i class="mdi mdi-upload"></i> Upload PDF to Sign
                            </a>
                        </div>
                        <h4 class="page-title">PDF Document Signing</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Document Title</th>
                                    <th>Uploaded By</th>
                                    <th>Status</th>
                                    <th>Signed By</th>
                                    <th>Signed At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($documents as $key => $doc)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $doc->title }}</td>
                                        <td>{{ $doc->uploader->name }}</td>
                                        <td>
                                            @if($doc->status === 'signed')
                                                <span class="badge bg-success"><i class="mdi mdi-check"></i> Signed</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $doc->signer->name ?? '—' }}</td>
                                        <td>{{ $doc->signed_at ? $doc->signed_at->format('d M Y, h:i A') : '—' }}</td>
                                        <td>
                                            <a href="{{ route('pdfsign.viewer', $doc->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-eye"></i> View / Sign
                                            </a>
                                            <form action="{{ route('pdfsign.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="mdi mdi-delete"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
