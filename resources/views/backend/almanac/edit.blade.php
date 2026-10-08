@extends('admin_dashboard')

@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Edit Almanac Schedule</h4>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('update.almanac', $almanac->id) }}" method="POST">
                                @csrf

                                <input type="hidden" name="id" value="{{ $almanac->id }}">
                                <div class="mb-3">
                                    <label class="form-label">Meeting</label>
                                    <select name="meeting_id" class="form-control" required>
                                        @foreach($meetings as $meeting)
                                            <option value="{{ $meeting->id }}" {{ $meeting->id == $almanac->board_committee_id ? 'selected' : '' }}>
                                                {{ $meeting->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Meeting Date</label>
                                    <input type="date" name="meeting_date" class="form-control" value="{{ $almanac->meeting_date }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Purpose/Agenda</label>
                                    <textarea name="purpose" class="form-control" rows="4" required>{{ $almanac->purpose }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Budget (KSH)</label>
                                    <input type="number" name="budget" class="form-control" value="{{ $almanac->budget }}" required>
                                </div>


                                    <div class="mb-3">
                                        <label for="change_reason" class="form-label">
                                            Reason for Change <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="change_reason" id="change_reason" class="form-control" rows="3" required>{{ old('change_reason') }}</textarea>
                                        @error('change_reason')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>





                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="Pending" {{ $almanac->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Approved" {{ $almanac->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Rejected" {{ $almanac->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">Update Schedule</button>
                                    <a href="{{ route('all.almanac.main') }}" class="btn btn-secondary">Cancel</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
