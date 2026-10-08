@extends('admin_dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('repository.category.show', $category) }}" class="btn btn-secondary btn-sm me-3">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">Add Document</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">
                        Upload a new document to <strong>{{ $category->name }}</strong>
                    </p>
                </div>
            </div>

            <div class="card" style="max-width: 720px;">
                <div class="card-body">
                    <form method="POST" action="{{ route('repository.document.store', $category) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" placeholder="e.g. Board Resolution - January 2026" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Brief description of the document contents">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip">
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV, ZIP</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Visibility <span class="text-danger">*</span></label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="visibility" value="all"
                                           id="visibility_all" {{ old('visibility', 'all') === 'all' ? 'checked' : '' }}
                                           onchange="toggleVisibilityOptions()">
                                    <label class="form-check-label" for="visibility_all">
                                        <strong>All Board Members</strong>
                                        <span class="text-muted d-block" style="font-size: 12px;">Everyone can access this document</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="visibility" value="committee"
                                           id="visibility_committee" {{ old('visibility') === 'committee' ? 'checked' : '' }}
                                           onchange="toggleVisibilityOptions()">
                                    <label class="form-check-label" for="visibility_committee">
                                        <strong>Specific Committees</strong>
                                        <span class="text-muted d-block" style="font-size: 12px;">Only members of selected committees can access</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="visibility" value="specific"
                                           id="visibility_specific" {{ old('visibility') === 'specific' ? 'checked' : '' }}
                                           onchange="toggleVisibilityOptions()">
                                    <label class="form-check-label" for="visibility_specific">
                                        <strong>Specific Members</strong>
                                        <span class="text-muted d-block" style="font-size: 12px;">Only individually selected members can access</span>
                                    </label>
                                </div>
                            </div>
                            @error('visibility')
                                <div class="text-danger mt-1" style="font-size: 12.5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Committee Selection -->
                        <div class="mb-3" id="committee-section" style="display: none;">
                            <label class="form-label">Select Committees <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 220px; overflow-y: auto;">
                                @forelse($committees as $committee)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="committee_ids[]"
                                               value="{{ $committee->id }}" id="committee_{{ $committee->id }}"
                                               {{ in_array($committee->id, old('committee_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="committee_{{ $committee->id }}">
                                            {{ $committee->name }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0" style="font-size: 12.5px;">No committees available.</p>
                                @endforelse
                            </div>
                            @error('committee_ids')
                                <div class="text-danger mt-1" style="font-size: 12.5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Member Selection -->
                        <div class="mb-3" id="member-section" style="display: none;">
                            <label class="form-label">Select Members <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 220px; overflow-y: auto;">
                                @forelse($members as $member)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="member_ids[]"
                                               value="{{ $member->id }}" id="member_{{ $member->id }}"
                                               {{ in_array($member->id, old('member_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="member_{{ $member->id }}">
                                            {{ $member->names }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0" style="font-size: 12.5px;">No members available.</p>
                                @endforelse
                            </div>
                            @error('member_ids')
                                <div class="text-danger mt-1" style="font-size: 12.5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check me-1"></i> Upload Document
                            </button>
                            <a href="{{ route('repository.category.show', $category) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleVisibilityOptions() {
            var visibility = document.querySelector('input[name="visibility"]:checked').value;
            document.getElementById('committee-section').style.display = (visibility === 'committee') ? 'block' : 'none';
            document.getElementById('member-section').style.display = (visibility === 'specific') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleVisibilityOptions();
        });
    </script>
@endsection
