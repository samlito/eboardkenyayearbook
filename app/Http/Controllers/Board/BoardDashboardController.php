<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Services\BoardMeetingService;
use Illuminate\Http\Request;
use App\Models\BoardMember;
use App\Models\BoardMeeting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Services\BoardMemberService;
use App\Http\Services\BoardNoticeService;
class BoardDashboardController extends Controller
{
    //
    public function index()
    {
        $userId = Auth::id();

        // Prefer 1:1 link, fall back by email if legacy rows exist
        $member = BoardMember::with(['committees', 'county', 'assignsalutation'])
            ->where('user_id', $userId)
            ->orWhere('email', Auth::user()->email)
            ->firstOrFail();

        // Services you shared
        $upcomingMeetings = BoardMeetingService::upcomingMeetingsForLoggedInMember();
        $notices = BoardNoticeService::fetchForLoggedInMember();

        // Aggregate quick stats
        $totalUpcomingPapers = $upcomingMeetings->pluck('boardPapers')->flatten()->count();
        $nextMeeting = $upcomingMeetings->first(); // meetings are ordered by date in the service

        // Simple profile completeness scoring (tweak as you wish)
        $checklist = [
            'photo' => !!$member->photo,
            'upload_cv' => !!$member->upload_cv,
            'appointment_letter' => !!$member->appointment_letter,
            'gazettee_notice' => !!$member->gazettee_notice,
            'acceptance_letter' => !!$member->acceptance_letter,
            'qualifications' => !!$member->qualifications,
            'experience_summary' => !!$member->experience_summary,
            'postal_address' => !!$member->postal_address,
            'county_id' => !!$member->county_id,
            'date_of_birth' => !!$member->date_of_birth,
        ];
        $score = (int) (collect($checklist)->filter()->count() / count($checklist) * 100);

        // Day-of-year & current date
        $today = Carbon::now();
        $dayOfYear = $today->dayOfYear;

        // Quick actions (customize route names to your app)
        $routes = [
            'profile' => route('board.profile.show', $member->id),
            'notices' => route('board.notices.index'),
            'meetings' => route('board.meetings.index'),
            'papers' => route('board.papers.index'),
            'committees' => route('board.committees.index'),
        ];

        return view('board_index', compact(
            'member',
            'upcomingMeetings',
            'notices',
            'totalUpcomingPapers',
            'nextMeeting',
            'score',
            'checklist',
            'today',
            'dayOfYear',
            'routes'
        ));
    }

}
