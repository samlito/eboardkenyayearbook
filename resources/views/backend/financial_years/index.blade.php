@extends('admin_dashboard')

@section('admin')
<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Financial Years</h4>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Add New Financial Year --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header" style="background: {{ setting('primary_color', '#1a1a1a') }}; color: #fff;">
                        <h5 class="card-title mb-0"><i class="mdi mdi-calendar-plus me-1"></i> Add Financial Year</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('financial-years.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Starting Year</label>
                                <select class="form-select" name="start_year" required>
                                    <option value="">-- Select Year --</option>
                                    @for($y = date('Y') - 2; $y <= date('Y') + 5; $y++)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                            {{ $y }}/{{ $y + 1 }}
                                        </option>
                                    @endfor
                                </select>
                                <small class="text-muted">e.g. selecting {{ date('Y') }} creates FY {{ date('Y') }}/{{ date('Y') + 1 }}</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="set_active" id="setActive" value="1">
                                    <label class="form-check-label" for="setActive">Set as Active</label>
                                </div>
                            </div>

                            <button type="submit" class="btn w-100" style="background: {{ setting('primary_color', '#1a1a1a') }}; color: #fff;">
                                <i class="mdi mdi-plus-circle me-1"></i> Add Financial Year
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="card border-0" style="background: #f8f8f8;">
                    <div class="card-body">
                        <h6 class="mb-2"><i class="mdi mdi-information-outline me-1" style="color: {{ setting('primary_color', '#1a1a1a') }};"></i> About Financial Years</h6>
                        <ul class="mb-0 ps-3" style="font-size: 13px; color: #555;">
                            <li>Financial years run from <strong>July to June</strong> (e.g. FY 2026/2027 = Jul 2026 - Jun 2027).</li>
                            <li>Only <strong>active</strong> financial years appear in the almanac dropdown.</li>
                            <li>You can have multiple active years if needed.</li>
                            <li>Years with linked almanacs cannot be deleted.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Financial Years List --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">All Financial Years</h5>
                        <span class="badge bg-primary rounded-pill">{{ $financialYears->count() }} total</span>
                    </div>
                    <div class="card-body">
                        @if($financialYears->isEmpty())
                            <div class="text-center py-5">
                                <i class="mdi mdi-calendar-blank-outline" style="font-size: 48px; color: #ccc;"></i>
                                <p class="text-muted mt-2 mb-0">No financial years have been added yet.</p>
                                <p class="text-muted">Use the form on the left to add your first financial year.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Financial Year</th>
                                            <th>Status</th>
                                            <th>Almanacs</th>
                                            <th>Created</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($financialYears as $index => $fy)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="fw-semibold" style="font-size: 15px;">
                                                        <i class="mdi mdi-calendar-range me-1"></i>FY {{ $fy->year }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($fy->status === 'active')
                                                        <span class="badge bg-success-subtle text-success px-2 py-1" style="font-size: 12px;">
                                                            <i class="mdi mdi-check-circle me-1"></i>Active
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 12px;">
                                                            <i class="mdi mdi-minus-circle me-1"></i>Inactive
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $fy->almanacs_count ?? $fy->almanacs->count() }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $fy->created_at->format('d M Y') }}</small>
                                                </td>
                                                <td class="text-end">
                                                    <form action="{{ route('financial-years.toggle', $fy->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @if($fy->status === 'active')
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Deactivate"
                                                                onclick="return confirm('Deactivate FY {{ $fy->year }}? It will no longer appear in the almanac dropdown.')">
                                                                <i class="mdi mdi-pause-circle-outline"></i>
                                                            </button>
                                                        @else
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Activate">
                                                                <i class="mdi mdi-play-circle-outline"></i>
                                                            </button>
                                                        @endif
                                                    </form>

                                                    <form action="{{ route('financial-years.destroy', $fy->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                            onclick="return confirm('Delete FY {{ $fy->year }}? This cannot be undone.')">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
