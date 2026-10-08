@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ route('resolutions.index') }}" class="btn btn-secondary btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Back to Resolutions
                            </a>
                        </div>
                        <h4 class="page-title">Record New Board Resolution</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('resolutions.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Select Board Meeting <span class="text-danger">*</span></label>
                                    <select name="board_meeting_id" class="form-select @error('board_meeting_id') is-invalid @enderror" required>
                                        <option value="">-- Select Meeting --</option>
                                        @foreach($meetings as $meeting)
                                            <option value="{{ $meeting->id }}" {{ old('board_meeting_id') == $meeting->id ? 'selected' : '' }}>
                                                {{ $meeting->title ?? $meeting->title }} — {{ $meeting->date ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('board_meeting_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Resolution <span class="text-danger">*</span></label>
                                    <textarea name="resolution" rows="4" class="form-control @error('resolution') is-invalid @enderror" placeholder="Describe the board resolution..." required>{{ old('resolution') }}</textarea>
                                    @error('resolution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assigned Officer <span class="text-danger">*</span></label>
                                    <select name="assigned_officer_id" class="form-select @error('assigned_officer_id') is-invalid @enderror" required>
                                        <option value="">-- Select Officer --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_officer_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_officer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Due Date</label>
                                    <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}">
                                    @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea name="remarks" rows="2" class="form-control" placeholder="Any additional remarks...">{{ old('remarks') }}</textarea>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Save Resolution
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
