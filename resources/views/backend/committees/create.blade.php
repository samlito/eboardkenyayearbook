@extends('admin_dashboard')
@section('admin')
    <div class="card p-4">
        <h4>Create New Board Committee</h4>
        <form action="{{ route('committees.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Committee Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Description (Optional)</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            @include('backend.committees.partials.audit_toggle')
            <button class="btn btn-success">Save</button>
        </form>
    </div>
@endsection
