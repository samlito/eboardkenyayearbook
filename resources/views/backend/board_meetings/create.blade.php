@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <h4 class="page-title">Add New Meeting</h4>
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('board.meetings.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Venue</label>
                            <input type="text" name="venue" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="Board">Board</option>
                                <option value="Committee">Committee</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Board Committee</label>
                            <select name="board_committee_id" class="form-control" required>
                                <option value="">-- Select Committee --</option>
                                @foreach($board_committees as $committee)
                                    <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Save Meeting</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
