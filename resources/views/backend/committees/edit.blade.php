@extends('admin_dashboard')
@section('admin')
    <div class="card p-4">
        <h4>Edit Committee</h4>
        <form action="{{ route('committees.update', $committee->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Committee Name</label>
                <input type="text" name="name" class="form-control" value="{{ $committee->name }}" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ $committee->description }}</textarea>
            </div>
            @include('backend.committees.partials.audit_toggle')
            <button class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
