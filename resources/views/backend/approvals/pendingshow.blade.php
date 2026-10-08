@extends('admin_dashboard')
@section('admin')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <div class="content">
        <div class="container-fluid">

            <!-- Start Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title">All Almanac List</h4>
                        <div class="page-title-right">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                ← Back
                            </a>

                        </div>
                    </div>
                </div>
            </div>
            <!-- End Page Title -->


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
                                            <p class="mb-3 fs-4"><strong>📜 Almanac No: </strong>
                                                <span class="text-primary">{{ $almanac->serialno }}</span>
                                            </p>
                                            <p class="mb-3 fs-4"><strong>📅 Financial Year: </strong>
                                                <span class="text-success">{{ $almanac->financialyear->year }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-3 fs-4"><strong>💰 Budget: </strong>
                                                <span class="text-danger fw-bold">KSH {{ number_format($almanac->budget, 2) }}</span>
                                            </p>
                                            <p class="mb-3 fs-4"><strong>📌 Status: </strong>
                                                <span class="badge fs-5 {{ $almanac->status == 'Approved' ? 'bg-success' : 'bg-warning' }}">
                    {{ ucfirst($almanac->status) }}
                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6 text-center">
                                            @if ($remainingBudget > 0)
                                                <p class="mb-3 fs-4">
                                                    <strong>💰 Balance: </strong>
                                                    <span class="badge bg-success text-white">
                        KSH {{ number_format($remainingBudget, 2) }}
                    </span>
                                                </p>
                                            @else
                                                <p class="mb-3 fs-4">
                                                    <strong>💰 Balance: </strong>
                                                    <span class="badge bg-danger text-white">
                        KSH {{ number_format($remainingBudget, 2) }}
                    </span>
                                                </p>
                                                <p class="text-danger fw-bold text-center">
                                                    Adjust your budget with the Almanac Budget.
                                                </p>
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <p class="mb-3 fs-4"><strong> Comments: </strong></p>
                                            <div class="p-3 border border-primary rounded">
                                                @if ($rejectedcomment->isNotEmpty())
                                                    @foreach ($rejectedcomment as $index => $comment)
                                                        <p class="mb-2 {{ $loop->first ? 'text-danger fw-bold' : 'text-dark' }}">
                                                            {{ $index + 1 }}. {{ $comment }} <!-- Numbering comments -->
                                                        </p>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No comments available.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{--                                <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap dataTable no-footer" aria-describedby="scroll-horizontal-datatable_info" style="width: 2680px;">--}}
                                <table id="selection-datatable" class="table dt-responsive nowrap w-100">

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

                                            <td>
                                                <a href="{{ route('edit.almanac', $almanc->id) }}" class="btn btn-primary rounded-pill">Edit</a>
                                                <a href="javascript:void(0);" onclick="confirmDelete('{{ route('delete.almanac', $almanc->id) }}')" class="btn btn-danger rounded-pill">Delete</a>
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
                                <!-- Approval Form -->
                                <div class="form-control">
                                    <h2 style="color: red">Approval Decision</h2>
                                    <form id="myForm" method="post" action="{{route('almanac.approval', $almanac->id) }}"  >
                                        @csrf


                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="inputLastName2" class="form-label" style="font-size:large">Committee Decision</label>
                                                <div class="form-group">
                                                    <select name="status"  id="decision" required="" class="form-control">
                                                        <option value="" selected="" >Select Decision</option>
                                                        <option value="Approved" >Approve</option>
                                                        <option value="Rejected">Reject</option>

                                                    </select>
                                                    <x-input-error :messages="$errors->get('status')" style="color: red" class="mt-2" />

                                                </div>
                                            </div>
                                            <br>
                                            <div class="col-md-12" style="display:none" id="hidediv">
                                                <label for="inputLastName2" class="form-label"style="font-size:large">Review Comments</label>
                                                <div class="form-group">
                                                    <textarea name="comment" class="form-control" type="text"></textarea>
                                                    <x-input-error :messages="$errors->get('comment')" style="color: red" class="mt-2" />

                                                </div>
                                            </div>


                                        </div>

                                        <p></p>
                                        <div class="col-m-6 form-control" id="buttonsdiv">
                                            <input type="submit" class="btn btn-primary " value="Save Changes" />
                                        </div>

                                    </form>

                                </div>

                                <script type="text/javascript">
                                    $(function () {

                                        $('#decision').on('change', function() {


                                            if ($('#decision').val()=='Rejected')
                                            {
                                                $("#hidediv").show();



                                            }
                                            else
                                            {

                                                $("#hidediv").hide();
                                                $("#comment").val('');


                                            }



                                        });
                                    });

                                    $(document).ready(function (){
                                        $('#myForm').validate({
                                            rules: {

                                                status: {
                                                    required : true,
                                                },
                                                comment: {
                                                    required : function(element){
                                                        return Number($('#decision').val()=='Rejected');
                                                    },
                                                },

                                            },
                                            messages :{
                                                comment: {
                                                    required : 'Required',
                                                },
                                                status: {
                                                    required : 'Required',
                                                },

                                            },
                                            errorElement : 'span',
                                            errorPlacement: function (error,element) {
                                                error.addClass('invalid-feedback');
                                                element.closest('.form-group').append(error);
                                            },
                                            highlight : function(element, errorClass, validClass){
                                                $(element).addClass('is-invalid');
                                            },
                                            unhighlight : function(element, errorClass, validClass){
                                                $(element).removeClass('is-invalid');
                                            },
                                        });
                                    });



                                </script>

                                <!-- Approval Form -->

{{--                                <div class="card border-primary shadow-lg mt-4">--}}
{{--                                    <div class="card-header bg-primary text-white text-center fw-bold">--}}
{{--                                        Approve or Reject Almanac--}}
{{--                                    </div>--}}
{{--                                    <div class="card-body bg-light p-4">--}}
{{--                                        <form action="{{ route('almanac.approval', $almanac->id) }}" method="POST">--}}
{{--                                            @csrf--}}
{{--                                            <div class="mb-3">--}}
{{--                                                <label for="status" class="form-label fw-bold">Select Action</label>--}}
{{--                                                <select class="form-select" name="status" id="status" required onchange="toggleRejectionComment()" class="form-control">--}}
{{--                                                    <option value="" disabled selected>-- Select an Action --</option>--}}
{{--                                                    <option value="Approved">Approve</option>--}}
{{--                                                    <option value="Rejected">Reject</option>--}}
{{--                                                </select>--}}
{{--                                            </div>--}}

{{--                                            <div class="mb-3 d-none" id="rejection-comment">--}}
{{--                                                <label for="comment" class="form-label fw-bold">Rejection Comment</label>--}}
{{--                                                <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>--}}
{{--                                            </div>--}}

{{--                                            <div class="text-center">--}}
{{--                                                <button type="submit" class="btn btn-success">Submit</button>--}}
{{--                                            </div>--}}
{{--                                        </form>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <script>--}}
{{--                                    function toggleRejectionComment() {--}}
{{--                                        const status = document.getElementById('status').value;--}}
{{--                                        const commentField = document.getElementById('rejection-comment');--}}
{{--                                        if (status === 'Rejected') {--}}
{{--                                            commentField.classList.remove('d-none');--}}
{{--                                        } else {--}}
{{--                                            commentField.classList.add('d-none');--}}
{{--                                        }--}}
{{--                                    }--}}
{{--                                </script>--}}
                            </div> <!-- end card body-->
                        </div> <!-- end card -->
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- container -->
        </div> <!-- content -->


@endsection
