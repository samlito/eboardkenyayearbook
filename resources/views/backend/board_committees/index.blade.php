@extends('admin_dashboard')
@section('admin')
    <div class="container-fluid mt-4">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Assign Members to: {{ $committee->name }} ({{ ucfirst($committee->type) }})</h5>
                <a href="{{ route('committees.index') }}" class="btn btn-secondary btn-sm">← Back to Committees</a>
            </div>

            <form action="{{ route('committees.members.store', $committee->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Board Member Name</th>
                                <th>Email</th>
                                <th>Assign</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($boardMembers as $index => $member)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                            {{ in_array($member->id, $assignedMemberIds) ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No active board members found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">Save Assignments</button>
                </div>
            </form>
        </div>

    </div>
@endsection
