{{--@extends('admin_dashboard')--}}

{{--@section('admin')--}}
{{--    <div class="content">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="page-title-box">--}}
{{--                        <div class="page-title-right">--}}
{{--                            <a href="{{ route('all.almanac') }}" class="btn btn-primary">Back to Almanac List</a>--}}
{{--                        </div>--}}
{{--                        <h4 class="page-title">Create Almanac Schedule</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="row">--}}
{{--                <div class="col-lg-8 offset-lg-2">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            @if ($errors->any())--}}
{{--                                <div class="alert alert-danger">--}}
{{--                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>--}}
{{--                                    <ul class="mb-0">--}}
{{--                                        @foreach ($errors->all() as $error)--}}
{{--                                            <li>{{ $error }}</li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                </div>--}}
{{--                            @endif--}}

{{--                            <form action="{{route('almanac.store',$almanac->id)}}" method="POST">--}}
{{--                                @csrf--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="board_committee_id" class="form-label">Board Committee</label>--}}
{{--                                    <select class="form-select" name="board_committee_id" required>--}}
{{--                                        <option value="">Select Committee</option>--}}
{{--                                        @foreach($committees as $committee)--}}
{{--                                            <option value="{{ $committee->id }}">{{ $committee->name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}


{{--                                <div class="mb-3">--}}
{{--                                    <label for="meeting_id" class="form-label">Meeting</label>--}}
{{--                                    <select class="form-select" name="meeting_id" required>--}}
{{--                                        <option value="">Select Meeting</option>--}}
{{--                                        @foreach($meeting as $meet)--}}
{{--                                            <option value="{{ $meet->id }}">{{ $meet->name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}

{{--                                <div class="mb-3">--}}
{{--                                    <label for="meeting_date" class="form-label">Meeting Date</label>--}}
{{--                                    <input type="date" class="form-control" name="meeting_date" required>--}}
{{--                                </div>--}}

{{--                                <div class="mb-3">--}}
{{--                                    <label for="purpose" class="form-label">Purpose</label>--}}
{{--                                    <textarea class="form-control" name="purpose" rows="3" required></textarea>--}}
{{--                                </div>--}}

{{--                                <div class="mb-3">--}}
{{--                                    <label for="budget" class="form-label">Budget</label>--}}
{{--                                    <input type="number" step="0.01" class="form-control" name="budget" required>--}}
{{--                                </div>--}}




{{--                                <button type="submit" class="btn btn-success">Create Almanac Schedule</button>--}}
{{--                            </form>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}
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
                        <h4 class="page-title">Create Almanac Schedule</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('almanac.store', $almanac->id) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="board_committee_id" class="form-label">Board Committee</label>
                                    <select class="form-select" name="board_committee_id" required>
                                        <option value="">Select Committee</option>
                                        @foreach($committees as $committee)
                                            <option value="{{ $committee->id }}"
                                                {{ old('board_committee_id') == $committee->id ? 'selected' : '' }}>
                                                {{ $committee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="meeting_date" class="form-label">Meeting Date</label>
                                    <input type="date" class="form-control" name="meeting_date"
                                           value="{{ old('meeting_date') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose</label>
                                    <textarea class="form-control" name="purpose" rows="3" required>{{ old('purpose') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="budget" class="form-label">Budget</label>
                                    <input type="number" step="0.01" class="form-control" name="budget"
                                           value="{{ old('budget') }}" required>
                                </div>

                                <button type="submit" class="btn btn-success">Create Almanac Schedule</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
