@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Approved Notice for: {{ $meeting->title }}</h4>
                <span class="badge bg-success">Status: {{ $meeting->notice->status }}</span>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary">Meeting Details</h5>
                    <p><strong>Date:</strong> {{ $meeting->date }}</p>
                    <p><strong>Venue:</strong> {{ $meeting->venue }}</p>
                    <p><strong>Type:</strong> {{ $meeting->type }}</p>

                    <hr>

                    <h5 class="text-info">Notice Content</h5>
                    <div class="border p-3 mb-4" style="white-space: pre-wrap; background-color: #f9f9f9;">
                        {{ $meeting->notice->content }}
                    </div>

                    @if($meeting->notice->ceo_comments)
                        <h6 class="text-danger">CEO Comments</h6>
                        <div class="border p-3" style="background-color: #fff6f6;">
                            {{ $meeting->notice->ceo_comments }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

@endsection
