@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ route('quicksign.create') }}" class="btn btn-info btn-sm">
                                <i class="mdi mdi-plus"></i> Sign New Document
                            </a>
                        </div>
                        <h4 class="page-title">Signed Documents</h4>
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
                                    <th>Prepared By</th>
                                    <th>Signer</th>
                                    <th>Signature</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($signatures as $key => $sig)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $sig->document_title }}</td>
                                        <td>{{ $sig->prepared_by }}</td>
                                        <td>{{ $sig->signer_name }}</td>
                                        <td><img src="{{ $sig->signature_data }}" style="max-height:40px;"></td>
                                        <td>{{ $sig->created_at->format('d M Y, h:i A') }}</td>
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
