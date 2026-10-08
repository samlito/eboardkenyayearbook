@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="btn btn-info"><a href="{{route('main.almanac.add')}}">Add New Almanac</a></li>

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


                            {{--                            <table  class="table dt-responsive nowrap w-100">--}}
                            <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap dataTable no-footer" aria-describedby="scroll-horizontal-datatable_info" style="width: 2680px;">

                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Financial Year</th>
                                    <th>Budget</th>
                                    <th>Serial Number</th>
                                    <th>Status</th>
                                    <th>Add Schedule</th>
                                    <th>Actions</th>
                                    <th>Report</th>
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
                                            <a href="{{ route('almanac.add', $almanc->id) }}" class="btn btn-sm btn-primary">
                                                Add Schedule
                                            </a>
                                        </td>

                                        <td>
                                            <a href="{{ route('almanac.show.all', $almanc->id) }}" class="btn btn-sm btn-primary">
                                                View All Schedules
                                            </a>
                                        </td>


                                        </td>
{{--                                        <td>--}}
{{--                                            <button class="btn btn-sm btn-primary generate-report" data-id="{{ $almanc->id }}">--}}
{{--                                                View Report--}}
{{--                                            </button>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <button class="btn btn-sm btn-primary view-pdf" data-id="{{ $almanc->id }}">View Report</button>--}}
{{--                                        </td>--}}
                                        <td>
                                            <a href="{{ route('almanac.pdf', $almanc->id) }}" class="btn btn-sm btn-info" target="_blank">
                                                View Report
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <!-- Modal -->
{{--                            <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">--}}
{{--                                <div class="modal-dialog modal-lg">--}}
{{--                                    <div class="modal-content">--}}
{{--                                        <div class="modal-header">--}}
{{--                                            <h5 class="modal-title" id="pdfModalLabel">Almanac Report</h5>--}}
{{--                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                                                <span aria-hidden="true">&times;</span>--}}
{{--                                            </button>--}}
{{--                                        </div>--}}
{{--                                        <div class="modal-body">--}}
{{--                                            <iframe id="pdfViewer" src="" width="100%" height="500px"></iframe>--}}
{{--                                        </div>--}}
{{--                                        <div class="modal-footer">--}}
{{--                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
{{--                                            <a id="downloadPdf" class="btn btn-primary" href="#" download>Download PDF</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Almanac Report</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe src="" style="width: 100%; height: 500px;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
            </div>
            <!-- end row-->




        </div> <!-- container -->

    </div> <!-- content -->

{{--    <script>--}}
{{--        $(document).ready(function() {--}}
{{--            $('.generate-report').click(function() {--}}
{{--                var almanacId = $(this).data('id');--}}

{{--                // Show a loading message--}}
{{--                Swal.fire({--}}
{{--                    title: 'Generating Report...',--}}
{{--                    text: 'Please wait while we process your request.',--}}
{{--                    allowOutsideClick: false,--}}
{{--                    didOpen: () => {--}}
{{--                        Swal.showLoading();--}}
{{--                    }--}}
{{--                });--}}

{{--                $.ajax({--}}
{{--                    url: "{{ route('almanac.generate.pdf') }}",--}}
{{--                    type: "POST",--}}
{{--                    data: {--}}
{{--                        almanac_id: almanacId,--}}
{{--                        _token: "{{ csrf_token() }}"--}}
{{--                    },--}}
{{--                    success: function(response) {--}}
{{--                        Swal.close();--}}

{{--                        if (response.status === 'success') {--}}
{{--                            $('#pdfModal iframe').attr('src', response.pdf_url);--}}
{{--                            $('#pdfModal').modal('show');--}}
{{--                        } else {--}}
{{--                            Swal.fire('Error', 'Failed to generate the PDF', 'error');--}}
{{--                        }--}}
{{--                    },--}}
{{--                    error: function() {--}}
{{--                        Swal.fire('Error', 'Something went wrong', 'error');--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
    <script>
        $(document).ready(function () {
            $('.view-pdf').on('click', function () {
                let almanacId = $(this).data('id');
                let pdfUrl = "{{ route('almanac.generate.pdf', ':id') }}".replace(':id', almanacId);

                console.log("Fetching PDF from:", pdfUrl); // Debugging Step

                // Set the PDF in the iframe
                $('#pdfViewer').attr('src', pdfUrl);

                // Set download link
                $('#downloadPdf').attr('href', pdfUrl);

                // Show the modal
                $('#pdfModal').modal('show');
            });
        });
    </script>

@endsection
