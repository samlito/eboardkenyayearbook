@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

            @if(session('success'))
                <script>
                    Swal.fire('Success', '{{ session('success') }}', 'success');
                </script>
            @endif

            @if(session('error'))
                <script>
                    Swal.fire('Error', '{{ session('error') }}', 'error');
                </script>
            @endif

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
                                    <th>Is Active</th>
                                    <th>Financial Year</th>
                                    <th>Budget</th>
                                    <th>Serial Number</th>
                                    <th>Status</th>
                                    <th>CEO Approval Date</th>
                                    <th>Add Schedule</th>
                                    <th>Actions</th>
                                    <th>Report</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($almanacs as $key => $almanc)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            @if((bool) $almanc->is_active)
                                                <span class="badge badge-soft-success rounded-pill">
      <i class="mdi mdi-check-circle-outline me-1"></i> Active
    </span>
                                            @else
                                                <span class="badge badge-soft-danger rounded-pill">
      <i class="mdi mdi-close-circle-outline me-1"></i> Inactive
    </span>
                                            @endif
                                        </td>


                                        <td>{{ $almanc->financialyear->year }} </td>
                                        <td>{{ $almanc->budget }} </td>
                                        <td>{{ $almanc->serialno }}</td>
{{--                                        <td>{{ $almanc->status }}</td>--}}
                                        <td>
                                            @php
                                                $status = $almanc->status;
                                                $badgeClass = match ($status) {
                                                    'Approved' => 'background: #28a745; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;',
                                                    'Draft' => 'background: #6c757d; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;',
                                                    'Awaiting Approval' => 'background: #ffc107; color: black; padding: 4px 8px; border-radius: 6px; font-weight: bold;',
                                                    'Rejected' => 'background: #dc3545; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;',
                                                    default => 'background: #17b84f; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;',
                                                };
                                            @endphp
                                            <span style="{{ $badgeClass }}">
                                       {{ $status }}
                                         </span>

                                            {{-- Show rejection comment if rejected --}}
                                            @if($status === 'Rejected' && $almanc->latestReviewComment)<br><span style="color: red; font-weight: bold;">
                                {{ $almanc->latestReviewComment->comment }}
                            </span>
                                            @endif
                                        </td>


                                        {{--                                        <td>--}}
{{--                                            <a href="{{ route('almanac.add', $almanc->id) }}" class="btn btn-sm btn-primary">--}}
{{--                                                Add Schedule--}}
{{--                                            </a>--}}
{{--                                        </td>--}}




                                        <td>
                                            {{ $almanc->ceo_approval_date ? \Carbon\Carbon::parse($almanc->ceo_approval_date)->format('d-m-Y') : 'Not indicated' }}
                                        </td>


                                        <td>
                                            @if($almanc->status === 'Pending'|| $almanc->status === 'Rejected')
                                                <a href="{{ route('almanac.add', $almanc->id) }}" class="btn btn-sm btn-primary">
                                                    Add Schedule
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    Add Schedule
                                                </button>
                                            @endif
                                        </td>

{{--                                        <td>--}}
{{--                                            <a href="{{ route('almanac.show.all', $almanc->id) }}" class="btn btn-sm btn-primary">--}}
{{--                                                View All Schedules--}}
{{--                                            </a>--}}
{{--                                        </td>--}}
                                        <td>
                                            <a href="{{ route('almanac.show.all', $almanc->id) }}" class="btn btn-sm btn-primary">
                                                View All Schedules
                                            </a>

                                            @if($almanc->status === 'Pending' || $almanc->status === 'Rejected')
                                                <button class="btn btn-sm btn-warning mt-1" data-bs-toggle="modal" data-bs-target="#submitModal{{ $almanc->id }}">
                                                    Submit for Approval
                                                </button>
                                            @endif
                                        </td>


{{--                                        </td>--}}
                                        <td>


                                            <a href="{{ route('almanac.download', $almanc->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="mdi mdi-file-pdf"></i> Download PDF
                                            </a>

                                            {{-- Show upload button only if status = Approved --}}
                                            @if($almanc->status === 'Approved')
                                                @if(!empty($almanc->signed_almanac) && Storage::disk('public')->exists($almanc->signed_almanac))
                                                    {{-- File already uploaded: show view/download link --}}
                                                    <a href="{{ asset('storage/' . $almanc->signed_almanac) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-file-pdf"></i> View Signed
                                                    </a>
                                                @else
                                                    {{-- No file uploaded yet: show upload form --}}
                                                    <form action="{{ route('almanac.upload', $almanc->id) }}"
                                                          method="POST" enctype="multipart/form-data" style="display:inline-block;">
                                                        @csrf
                                                        <label class="btn btn-sm btn-success mb-0">
                                                            <i class="mdi mdi-upload"></i> Upload Signed
                                                            <input type="file" name="signed_almanac"
                                                                   accept="application/pdf"
                                                                   onchange="this.form.submit()" hidden>
                                                        </label>
                                                    </form>
                                                @endif
                                            @endif



                                        </td>



                                        <!-- Submit Modal -->
                                        <div class="modal fade" id="submitModal{{ $almanc->id }}" tabindex="-1" aria-labelledby="submitModalLabel{{ $almanc->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form method="POST" action="{{ route('almanac.submit.approval', $almanc->id) }}">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning text-dark">
                                                            <h5 class="modal-title" id="submitModalLabel{{ $almanc->id }}">Confirm Submission</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to submit this almanac for CEO approval?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-warning">Yes, Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <!-- Modal -->
                            <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="pdfModalLabel">Almanac Report</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="pdfViewer" src="" width="100%" height="500px"></iframe>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <a id="downloadPdf" class="btn btn-primary" href="#" download>Download PDF</a>
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

    <script>
        $(document).ready(function() {
            $('.generate-report').click(function() {
                var almanacId = $(this).data('id');

                // Show a loading message
                Swal.fire({
                    title: 'Generating Report...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('almanac.generate.pdf') }}",
                    type: "POST",
                    data: {
                        almanac_id: almanacId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.status === 'success') {
                            $('#pdfModal iframe').attr('src', response.pdf_url);
                            $('#pdfModal').modal('show');
                        } else {
                            Swal.fire('Error', 'Failed to generate the PDF', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                });
            });
        });
    </script>
{{--<script>--}}
{{--    $.ajax({--}}
{{--        url: "{{ route('almanac.generate.pdf') }}",--}}
{{--        type: "POST",--}}
{{--        data: {--}}
{{--            almanac_id: almanacId,--}}
{{--            _token: "{{ csrf_token() }}"--}}
{{--        },--}}
{{--        success: function(response) {--}}
{{--            console.log('PDF Generated:', response);--}}
{{--            Swal.close();--}}
{{--            if (response.status === 'success') {--}}
{{--                $('#pdfModal iframe').attr('src', response.pdf_url);--}}
{{--                $('#pdfModal').modal('show');--}}
{{--            } else {--}}
{{--                Swal.fire('Error', 'Failed to generate the PDF', 'error');--}}
{{--            }--}}
{{--        },--}}
{{--        error: function(xhr, status, error) {--}}
{{--            console.error('Error:', error);--}}
{{--            console.error('Response:', xhr.responseText);--}}
{{--            Swal.fire('Error', 'Something went wrong', 'error');--}}
{{--        }--}}
{{--    });--}}

{{--</script>--}}
@endsection
