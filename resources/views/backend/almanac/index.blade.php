@extends('admin_dashboard')
@section('admin')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
{{--                                <li class="btn btn-info"><a href="{{route('almanac.add')}}">Add New Almanac</a></li>--}}

                            </ol>
                        </div>
                        <h4 class="page-title">All Almanac List</h4>
                    </div>
                </div>
            </div>



            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">


                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap dataTable no-footer" aria-describedby="scroll-horizontal-datatable_info">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Financial Year</th>
                                    <th>Meeting Name</th>
                                    <th>Meeting Date</th>
                                    <th style="width: 300px;">Purpose/Agenda</th> <!-- Set width for better alignment -->
                                    <th>Budget</th>
                                    <th>Status</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($almanacs as $key => $almanc)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $almanc->financialyear->year }}</td>
                                        <td>{{ $almanc->meeting->name }}</td>
                                        <td>{{ $almanc->meeting_date }}</td>

                                        <!-- Purpose Column: Ensure text wraps -->
                                        <td style="white-space: normal; word-wrap: break-word; max-width: 300px;">
                                            {{ $almanc->purpose }}
                                        </td>

                                        <td>{{ $almanc->budget }}</td>
                                        <td>{{ $almanc->status }}</td>

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
