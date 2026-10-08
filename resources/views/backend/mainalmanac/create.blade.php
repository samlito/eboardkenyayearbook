@extends('admin_dashboard')

@section('admin')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ route('all.almanac') }}" class="btn btn-primary">Back to Almanac List</a>
                        </div>
                        <h4 class="page-title">Create Almanac </h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{route('main.almanac.store')}}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="fy_id" class="form-label">Financial Year</label>
                                    <select class="form-select" name="fy_id" required>
                                        <option value="">Select Financial Year</option>
                                        @foreach($financialyear as $year)
                                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                                        @endforeach
                                    </select>
                                </div>



                                <div class="mb-3">
                                    <label for="budget" class="form-label">Budget</label>
                                    <input type="number" step="0.01" class="form-control" name="budget" required>
                                </div>




                                <button type="submit" class="btn btn-success">Create Almanac </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
