@extends('admin_dashboard')
@section('admin')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h4 class="page-title">All Almanac List</h4>

                        <div class="page-title-right">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                ← Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">


                            {{--                            <table  class="table dt-responsive nowrap w-100">--}}
                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap dataTable no-footer" aria-describedby="scroll-horizontal-datatable_info" style="width: 2680px;">

                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Financial Year</th>
                                    <th>Budget</th>
                                    <th>Serial Number</th>
                                    <th>Status</th>
                                    <th>Actions</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($almanacs as $key => $almanc)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>

                                        <td>{{ $almanc->financialyear->year }} </td>
                                        <td>{{ $almanc->budget }} </td>
                                        <td>{{ $almanc->serialno }}</td>
                                        <td>{{ $almanc->status }}</td>


                                        <td>
                                            <a href="{{ route('almanac.show.pending', $almanc->id) }}" class="btn btn-sm btn-primary">
                                                View All Schedules
                                            </a>
                                        </td>


                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
            </div>
            <!-- end row-->




        </div> <!-- container -->

    </div> <!-- content -->


@endsection
