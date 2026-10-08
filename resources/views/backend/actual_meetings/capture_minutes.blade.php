@extends('admin_dashboard')

@section('admin')
    <div class="container mt-4">
        <h4 class="mb-4">📝 Capture Minutes for: <strong>{{ $meeting->title }}</strong></h4>
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <form action="{{ route('actual.meetings.minutes.store', $meeting->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Minutes Title</label>
                <input type="text" name="title" class="form-control" placeholder="Enter title of the minutes" required>
            </div>

            <div class="mb-3">
{{--                <label class="form-label fw-bold">Members Present</label>--}}
{{--                <select name="members_present[]" class="form-select" multiple>--}}
{{--                    @foreach($boardMembers as $member)--}}
{{--                        <option value="{{ $member->id }}">{{ $member->assignsalutation->title ?? '' }} {{ $member->names }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}

{{--                <select name="members_present[]" class="form-select" multiple required>--}}
{{--                    @foreach($boardMembers as $member)--}}
{{--                        <option value="{{ $member->id }}">--}}
{{--                            {{ $member->assignsalutation->title ?? '' }} {{ $member->names }}--}}
{{--                        </option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
                <div class="mb-3">
                    <label for="members_present" class="form-label">Members Present</label>
                    <select name="members_present[]" id="members_present" class="form-control select2" multiple>
                        @foreach($boardMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->assignsalutation->name ?? '' }} {{ $member->names }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="members_absent" class="form-label">Members Absent</label>
                    <select name="members_absent[]" id="members_absent" class="form-control select2" multiple>
                        @foreach($boardMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->assignsalutation->name ?? '' }} {{ $member->names }}</option>
                        @endforeach
                    </select>
                </div>


            </div>

{{--            <div class="mb-3">--}}
{{--                <label class="form-label fw-bold">Members Absent</label>--}}
{{--                <select name="members_absent[]" class="form-select" multiple>--}}
{{--                    @foreach($boardMembers as $member)--}}
{{--                        <option value="{{ $member->id }}">{{ $member->assignsalutation->title ?? '' }} {{ $member->names }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}

            <div class="mb-3">
                <label class="form-label fw-bold">In Attendance (Others)</label>
                <textarea name="members_in_attendance" class="form-control" rows="2" placeholder="List names of people in attendance"></textarea>
            </div>

            <hr>

            <h5 class="mb-3">Agenda & Minute Items</h5>
            <div id="minute-items-wrapper">
                <div class="minute-item border p-3 mb-3">
                    <div class="mb-3">
                        <label>Minute Number</label>
                        <input type="text" name="minute_items[0][minute_number]" class="form-control" required>

                    </div>

                    <div class="mb-2">
                        <label>Agenda Item</label>
                        <input type="text" name="minute_items[0][agenda_item]" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Description</label>
                        <textarea name="minute_items[0][description]" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-2">
                        <label>Board Comments & Recommendations</label>
                        <textarea name="minute_items[0][comments]" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-2">
                        <label>Board Resolution</label>
                        <textarea name="minute_items[0][resolution]" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label>Proposed By</label>
                            <input type="text" name="minute_items[0][proposer]" class="form-control">
                        </div>
                        <div class="col">
                            <label>Seconded By</label>
                            <input type="text" name="minute_items[0][seconder]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-secondary mb-3" id="add-minute-item">➕ Add Minute Item</button>

            <div class="text-end">
                <button type="submit" class="btn btn-success">💾 Save Minutes</button>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = 1;

        document.getElementById('add-minute-item').addEventListener('click', function () {
            const wrapper = document.getElementById('minute-items-wrapper');
            const newItem = document.createElement('div');
            newItem.classList.add('minute-item', 'border', 'p-3', 'mb-3');
            newItem.innerHTML = `
             <div class="mb-3">
                <label for="minute_number" class="form-label">Minute Number</label>
                <input type="text" class="form-control" name="minute_items[${itemIndex}][minute_number]" required>
            </div>
            <div class="mb-2">
                <label>Agenda Item</label>
                <input type="text" name="minute_items[${itemIndex}][agenda_item]" class="form-control" required>
            </div>

            <div class="mb-2">
                <label>Description</label>
                <textarea name="minute_items[${itemIndex}][description]" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-2">
                <label>Board Comments & Recommendations</label>
                <textarea name="minute_items[${itemIndex}][comments]" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-2">
                <label>Board Resolution</label>
                <textarea name="minute_items[${itemIndex}][resolution]" class="form-control" rows="2"></textarea>
            </div>

            <div class="row">
                <div class="col">
                    <label>Proposed By</label>
                    <input type="text" name="minute_items[${itemIndex}][proposer]" class="form-control">
                </div>
                <div class="col">
                    <label>Seconded By</label>
                    <input type="text" name="minute_items[${itemIndex}][seconder]" class="form-control">
                </div>
            </div>
        `;
            wrapper.appendChild(newItem);
            itemIndex++;
        });
    </script>

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "Select board members",
                allowClear: true
            });
        });
    </script>
    <script>
        $('.select2').select2({
            tags: false,
            allowClear: true,
            placeholder: 'Select members',
        });

    </script>
@endsection
