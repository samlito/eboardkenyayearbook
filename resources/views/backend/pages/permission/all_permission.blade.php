@extends('admin_dashboard')
@section('admin')
    <div class="content">
     <div class="container-fluid">
         <!--breadcrumb-->

         <!-- start page title -->
         <div class="row">
             <div class="col-12">
                 <div class="page-title-box">
                     <div class="page-title-right">
                         <ol class="breadcrumb m-0">
                             <li class="btn btn-info"><a href="{{route('add.permission')}}">Add Permission</a></li>

                         </ol>
                     </div>
                     <h4 class="page-title">Permissions List</h4>
                 </div>
             </div>
         </div>


         <hr/>
         <div class="row">
             <div class="col-12">
                 <div class="card">
                     <div class="card-body">
                         <div class="table-responsive">
{{--                             <table id="example" class="table table-striped table-bordered dataTable">--}}
                                 <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap dataTable no-footer" aria-describedby="scroll-horizontal-datatable_info" style="width: 2680px;">

                                 <thead>
                                 <tr>
                                     <th>Sl</th>
                                     <th>Permission Name </th>
                                     <th>Group Name </th>
                                     <th>Action</th>
                                 </tr>
                                 </thead>
                                 <tbody>
                                 @foreach($permissions as $key=> $item)
                                     <tr>
                                         <td>{{ $key+1 }}</td>
                                         <td>{{ $item->name }}</td>
                                         <td>{{ $item->group_name }}</td>
                                         <td>
                                             <a href="{{ route('edit.permission',$item->id) }}" class="btn btn-primary rounded-pill waves-effect waves-light">Edit</a>
                                             <a href="{{ route('delete.permission',$item->id) }}" class="btn btn-danger rounded-pill waves-effect waves-light" id="delete">Delete</a>

                                         </td>
                                     </tr>
                                 @endforeach
                                 </tbody>
                                 <tfoot>
                                 <tr>
                                     <th>Sl</th>
                                     <th>Permission Name </th>
                                     <th>Group Name </th>
                                     <th>Action</th>
                                 </tr>
                                 </tfoot>
                             </table>
                         </div>
                     </div>
                 </div>
             </div>
         </div>


     </div>
    </div>
@endsection

