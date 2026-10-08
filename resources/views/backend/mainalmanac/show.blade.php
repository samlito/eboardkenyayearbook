@extends('admin_dashboard')
@section('admin')



    <div class="content">
        <div class="container-fluid">

            <!-- Start Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title">All Almanac List</h4>
                        <div class="page-title-right">

                            <ol class="breadcrumb m-0">
                                @if( $almanac->status == 'Approved')
                                 <li class="btn btn-success"><a href="{{route('almanac.add.toapprovedalmanac',$almanac->id)}}">Additional Schedule to Already approved Almanac</a></li>
                                    @elseif( $almanac->status == 'Pending')
                                    <li class="btn btn-success"><a href="{{route('almanac.add',$almanac->id)}}">Add New Schedule</a></li>

                                @endif
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Page Title -->
            <div class="mb-3">

                <a href="{{ route('all.almanac.main') }}" class="btn btn-outline-secondary">
                    ← Back
                </a>

            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <div class="card border-info shadow-lg mb-4">
                                <div class="card-header bg-info text-white text-center fw-bold">
                                    Almanac Details
                                </div>
                                <div class="card-body bg-light p-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-3 fs-4"><strong>📜 Almanac No: </strong> <span class="text-primary">{{ $almanac->serialno }}</span></p>
                                            <p class="mb-3 fs-4"><strong>📅 Financial Year: </strong> <span class="text-success">{{ $almanac->financialyear->year }}</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-3 fs-4"><strong>💰 Budget: </strong> <span class="text-danger fw-bold">KSH {{ number_format($almanac->budget, 2) }}</span></p>
                                            <p class="mb-3 fs-4"><strong>📌 Status: </strong>
                                                <span class="badge fs-5 {{ $almanac->status == 'Approved' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($almanac->status) }}
                                            </span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3"></div>

                                        <div class="col-md-6">
                                            @if ($remainingBudget > 0)
                                                <p class="mb-3 fs-4">
                                                    <strong>💰 Balance: </strong>
                                                    <span class="badge bg-success text-white">
                                                    KSH {{ number_format($remainingBudget, 2) }}
                                                </span>
                                                                                    </p>
                                            @else
                                                <style>
    .blink-zoom {
        animation: blinkZoom 2.5s infinite;
        display: inline-block;
    }

    @keyframes blinkZoom {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0;
            transform: scale(1.6);
        }
    }
</style>


                                                <p class="mb-3 fs-4">
                                                    <strong>💰 Balance: </strong>
                                                    <span class="badge bg-danger text-white blink-zoom">
                                            KSH {{ number_format($remainingBudget, 2) }}
                                             </span>
                                                </p>

                                                <p class="text-danger fw-bold blink-zoom">
                                                    Adjust your budget with the Almanac Budget.
                                                </p>
                                            @endif

                                        </div>
                                        @if($almanac->status === 'Rejected' && $almanac->latestReviewComment)
                                            <br>
                                            <span style="color: red; font-weight: bold;">
            {{ $almanac->latestReviewComment->comment }}
        </span>
                                        @endif
                                        <div class="col-md-3"></div>
                                    </div>

                                </div>

                                <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap dataTable no-footer" aria-describedby="scroll-horizontal-datatable_info" style="width: 2680px;">
{{--                                    <table id="selection-datatable" class="table dt-responsive nowrap w-100">--}}

                                    <thead class="table-dark text-white text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Meeting Name</th>
                                        <th>Meeting Date</th>
                                        <th style="width: 300px;">Purpose/Agenda</th>
                                        <th>Budget</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($allshcedules as $key => $almanc)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            <td class="fw-bold">{{ $almanc->meeting->name }}</td>
                                            <td class="text-center">{{ $almanc->meeting_date }}</td>

                                            <!-- Purpose Column: Ensures text wraps -->
                                            <td style="white-space: normal; word-wrap: break-word; max-width: 300px;">
                                                {{ $almanc->purpose }}
                                            </td>

                                            <!-- Bold and Green Budget -->
                                            <td class="fw-bold text-success text-center">
                                                KSH {{ number_format($almanc->budget, 2) }}
                                            </td>

                                            <td class="text-center">
                                                @if($almanac->status === 'Approved')
                                                    <span class="badge bg-success fs-6">
                                                ✅ Approved
                                            </span>
                                                @elseif($almanac->status === 'Awaiting Approval')
                                                    <span class="badge bg-warning text-dark fs-6">
                                                    ⏳ Awaiting Approval
                                                </span>
                                                @else
                                                    <a href="{{ route('edit.almanac', $almanc->id) }}" class="btn btn-primary rounded-pill">Edit</a>
                                                    <a href="javascript:void(0);" onclick="confirmDelete('{{ route('delete.almanac', $almanc->id) }}')" class="btn btn-danger rounded-pill">Delete</a>
                                                @endif
                                            </td>



                                            <script>
                                                function confirmDelete(url) {
                                                    if (confirm("Are you sure you want to delete this schedule? This action cannot be undone.")) {
                                                        window.location.href = url;
                                                    }
                                                }
                                            </script>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                @foreach ($allshcedules as $schedule)
                                    <h5>Schedule: {{ $schedule->meeting->name }} </h5>

                                    @if ($schedule->versions->count())
                                        <div class="table-responsive mb-5">
                                            <table class="table table-bordered">
                                                <thead class="table-dark text-white">
                                                <tr>
                                                    <th>Version</th>
                                                    <th>Meeting Date</th>
                                                    <th>Purpose</th>
                                                    <th>Budget</th>
{{--                                                    <th>Status</th>--}}
                                                    <th>Change Reason</th>
                                                    <th>Changed By</th>
                                                    <th>Changed At</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($schedule->versions as $version)
                                                    <tr>
                                                        <td>{{ $version->version_number }}</td>
                                                        <td>{{ $version->meeting_date }}</td>
                                                        <td>{{ $version->purpose }}</td>
                                                        <td>KSH {{ number_format($version->budget, 2) }}</td>
{{--                                                        <td>{{ ucfirst($version->status) }}</td>--}}
                                                        <td>{{ $version->change_reason ?? 'N/A' }}</td>
                                                        <td>{{ $version->created_by }}</td>
                                                        <td>{{ $version->created_at->format('Y-m-d H:i') }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No version history available for this schedule.</p>
                                    @endif
                                @endforeach


                            </div> <!-- end card body-->
                            <div class="mb-3">
                                <a href="{{ route('all.almanac.main') }}" class="btn btn-outline-secondary">
                                    ← Back
                                </a>
                            </div>
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- container -->
    </div> <!-- content -->


@endsection
