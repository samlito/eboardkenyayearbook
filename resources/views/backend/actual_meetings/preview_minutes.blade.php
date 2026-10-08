@extends('admin_dashboard')

@section('admin')
    <div class="container my-4">

        {{-- LETTERHEAD --}}
        <div class="d-flex align-items-center mb-4">
            <img src="{{ asset(setting('logo_letterhead') ?: setting('logo_main', 'images/banner.png')) }}" alt="{{ setting('institution_acronym', 'ORG') }} Logo" style="height: 80px;">
            <div class="ms-3">
                <h4 class="mb-0">{{ strtoupper(setting('institution_name', 'eBoard System')) }}</h4>
                <small>Board Meeting Minutes</small>
            </div>
        </div>

        {{-- MINUTES TITLE --}}
        <h5 class="text-center mb-4"><strong>{{ $meeting->minutes->minutes_title ?? 'Board Meeting Minutes' }}</strong></h5>

        {{-- MEMBERS PRESENT --}}
        <div class="mb-3">
            <strong>Members Present:</strong>
            <ul>
{{--                @foreach($meeting->minutes->membersPresent() as $member)--}}
{{--                    <li>{{ $member->assignsalutation->name ?? '' }} {{ $member->name }}</li>--}}
{{--                @endforeach--}}
            </ul>
        </div>

        {{-- MEMBERS ABSENT --}}
        <div class="mb-3">
            <strong>Members Absent:</strong>
            <ul>
{{--                @foreach($meeting->minutes->membersAbsent() as $member)--}}
{{--                    <li>{{ $member->assignsalutation->name ?? '' }} {{ $member->name }}</li>--}}
{{--                @endforeach--}}
            </ul>
        </div>

        {{-- IN ATTENDANCE --}}
        <div class="mb-4">
            <strong>In Attendance:</strong>
            <p>{{ $meeting->minutes->in_attendance ?? 'N/A' }}</p>
        </div>

        {{-- MINUTES TABLE --}}
        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead class="table-light">
                <tr>
                    <th style="width: 20%">Minute No.</th>
                    <th>Agenda</th>
                </tr>
                </thead>
                <tbody>
                @foreach($meeting->minutes->minuteItems as $item)
                    <tr>
                        <td><strong>{{ $item->minute_number }}</strong></td>
                        <td>
                            <strong>Agenda Item:</strong> {{ $item->agenda_title }}<br>
                            <strong>Description:</strong> {{ $item->description }}<br>
                            <strong>Comments & Recommendations:</strong> {{ $item->comments }}<br>
                            <strong>Resolution:</strong> {{ $item->resolution }}<br>
                            <strong>Proposed by:</strong> {{ $item->proposer }}<br>
                            <strong>Seconded by:</strong> {{ $item->seconder }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- SIGNATURE SECTION --}}
        <div class="row mt-5">
            <div class="col-md-6">
                <p><strong>Signed by</strong></p>
                <p>…………………………………………. <br>
                    Date ………………………<br>
                    ENG. PHILIP GICHUKI, MBS<br>
                    {{ strtoupper(setting('signatory_title_ceo', 'Chief Executive Officer')) }} & SECRETARY TO THE BOARD</p>
            </div>
            <div class="col-md-6">
                <p><strong>Signed by</strong></p>
                <p>…………………………………………. <br>
                    Date ………………………<br>
                    MR. TEOBALD MUKUNDI<br>
                    {{ strtoupper(setting('signatory_title_chairperson', 'Chairperson')) }}</p>
            </div>
        </div>

    </div>
@endsection
