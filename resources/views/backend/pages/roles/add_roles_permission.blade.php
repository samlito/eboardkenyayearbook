@extends('admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <style type="text/css">

        .form-check-label{
            text-transform: capitalize;
        }
    </style>

    <div class="page-content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->

            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Add Role In Permission</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Add Role In Permission</li>
                        </ol>
                    </nav>
                </div>

            </div>
            <!-- end page title -->

            <div class="row">


                <div class="col-lg-8 col-xl-12">
                    <div class="card">
                        <div class="card-body">





                            <!-- end timeline content-->

                            <div class="tab-pane" id="settings">
                                <form id="myForm" method="post" action="{{ route('role.permission.store') }}" enctype="multipart/form-data">
                                    @csrf


                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="firstname" class="form-label">All Roles </label>
                                                <select name="role_id" class="form-select" id="example-select">
                                                    <option selected disabled >Select Roles  </option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}"> {{ $role->name }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>

                                        <div class="form-check mb-2 form-check-primary">
                                            <input class="form-check-input" type="checkbox" value="" id="customckeck15"  >
                                            <label class="form-check-label" for="customckeck15">Check All</label>
                                        </div>

                                        <hr>

                                        @foreach($permission_groups as $group)
                                            <div class="row">
                                                <div class="col-3">

                                                    <div class="form-check mb-2 form-check-primary">
                                                        <input class="form-check-input" type="checkbox" value="" id="customckeck1"  >
                                                        <label class="form-check-label" for="customckeck1">{{ $group->group_name }}</label>
                                                    </div>

                                                </div>

                                                <div class="col-9">

                                                    @php
                                                        $permissions = App\Models\User::getpermissionByGroupName($group->group_name);
                                                    @endphp

                                                    @foreach($permissions as $permission)
                                                        <div class="form-check mb-2 form-check-primary">
                                                            <input class="form-check-input" type="checkbox" name="permission[]" value="{{ $permission->id }}" id="customckeck{{ $permission->id }}"  >
                                                            <label class="form-check-label" for="customckeck{{ $permission->id }}">{{ $permission->name }}</label>
                                                        </div>
                                                    @endforeach
                                                    <br>

                                                </div>

                                            </div> <!-- end row -->
                                        @endforeach

                                    </div> <!-- end row -->



                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i class="mdi mdi-content-save"></i> Save</button>
                                    </div>
                                </form>
                            </div>
                            <!-- end settings content-->


                        </div>
                    </div> <!-- end card-->

                </div> <!-- end col -->
            </div>
            <!-- end row-->

        </div> <!-- container -->

    </div> <!-- content -->


    <script type="text/javascript">
        $('#customckeck15').click(function(){
            if ($(this).is(':checked')) {
                $('input[type = checkbox]').prop('checked',true);
            }else{
                $('input[type = checkbox]').prop('checked',false);
            }

        });
    </script>




@endsection
