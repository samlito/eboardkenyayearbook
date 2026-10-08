<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardNoticeController;
use App\Http\Controllers\Board\BoardMemberMeetingController;
use App\Http\Controllers\Board\BoardDashboardController;
use App\Http\Controllers\Board\BoardPortalController;
use App\Http\Controllers\BoardMeetingController;
use App\Http\Controllers\Board\ActualBoardMeetingController;
use App\Http\Controllers\ConflictOfInterestController;
use App\Http\Controllers\Board\BoardMemberProfileController;
use App\Http\Controllers\Board\MeetingsController;
use App\Http\Controllers\Board\NoticesController;
use App\Http\Controllers\Board\PapersController;
use App\Http\Controllers\Board\CommitteesController;





Route::prefix('board/notices')->name('board.notices.')->group(function () {
    Route::get('/', [BoardNoticeController::class, 'index'])->name('index');
    Route::get('/{id}', [BoardNoticeController::class, 'show'])->name('show');



    Route::get('/10day/{notice}/preview', [BoardNoticeController::class, 'show10Day'])
        ->name('preview10day');

//
//    Route::get('/10day/{notice}/preview', [BoardNoticeController::class, 'preview10Day'])
//        ->name('board.notices.preview10day');

});




Route::prefix('board-member')->middleware(['auth'])->group(function () {
    Route::get('/my-meetings', [BoardMemberMeetingController::class, 'index'])->name('board.my.meetings');
    Route::get('/my-meetings/{meeting}', [BoardMemberMeetingController::class, 'show'])->name('board.my.meetings.show');
    Route::get('/active-meetings', [BoardMemberMeetingController::class, 'active'])->name('meetings.active');


});

Route::prefix('board')->middleware(['auth'])->name('board.')->group(function () {
    Route::get('/dashboard', [BoardDashboardController::class, 'index'])->name('dashboard');


    Route::get('/member-profile-show/{id}', [BoardMemberProfileController::class, 'show'])->name('profile.show');
//    Route::get('/member-notices', [BoardDashboardController::class, 'dashboard'])->name('notices.index');


    // Notices
    Route::get('/notices', [NoticesController::class, 'index'])->name('notices.index');
    Route::get('/notices/{meeting}', [NoticesController::class, 'show'])
        ->whereNumber('meeting')
        ->name('notices.show');
    // Meetings
    Route::get('/meetings', [MeetingsController::class, 'index'])->name('meetings.index');
    Route::get('/meetings/{meeting}', [MeetingsController::class, 'show'])
        ->whereNumber('meeting')
        ->name('meetings.show');


//    Route::get('/member-papers', [BoardDashboardController::class, 'dashboard'])->name('papers.index');
//
//    Route::get('/member-committees', [BoardDashboardController::class, 'dashboard'])->name('committees.index');

    // Board Papers
    Route::get('/papers', [PapersController::class, 'index'])->name('papers.index');

    // Committees
    Route::get('/committees', [CommitteesController::class, 'index'])->name('committees.index');



    // Profile (add to existing profile routes)
    Route::get('/profile/{member}/edit', [BoardMemberProfileController::class, 'edit'])
        ->whereNumber('member')
        ->name('profile.edit');

    Route::put('/profile/{member}', [BoardMemberProfileController::class, 'update'])
        ->whereNumber('member')
        ->name('profile.update');

    // In web.php
    Route::prefix('board')->middleware(['auth', 'role:Board Member'])->name('board.')->group(function () {

    });
});


Route::prefix('board')->middleware(['auth'])->group(function () {
//    Route::get('meetings', [ActualBoardMeetingController::class, 'active'])->name('board.my.meetings');
//    Route::get('meetings/{id}', [ActualBoardMeetingController::class, 'show'])->name('board.my.meetings.show');

    Route::get('conflict/{id}', [ActualBoardMeetingController::class, 'conflictForm'])->name('board.conflict.register');
    Route::post('conflict/{id}', [ActualBoardMeetingController::class, 'submitConflict'])->name('board.conflict.submit');

    Route::get('view-meeting/{id}', [ActualBoardMeetingController::class, 'viewMeeting'])->name('board.meetings.start');




    Route::post('/conflict-of-interest/{meeting}', [ConflictOfInterestController::class, 'store'])
        ->name('conflict.store');

//    Route::get('/conflict-of-interest/verify/{id}/{otp}', [ConflictOfInterestController::class, 'verify'])
//        ->name('conflict.verify');

    Route::post('/conflict-of-interest/verify/{id}', [ConflictOfInterestController::class, 'verify'])
        ->name('conflict.verify');

//
//    Route::get('/board/meetings/active', [ConflictOfInterestController::class, 'active'])
//        ->name('board.meetings.active');


    // Conflict of Interest Declarations
    Route::prefix('conflict')->group(function () {
        Route::post('/store/{meeting}', [ConflictOfInterestController::class, 'store'])->name('conflict.store');

        // Pending page after submission
        Route::get('/pending/{declaration}', [ConflictOfInterestController::class, 'pending'])->name('conflict.pending');

        // OTP verification form
        Route::get('/verify/{declaration}', [ConflictOfInterestController::class, 'verifyForm'])->name('conflict.verify.form');

        // OTP verification submit (AJAX or form POST)
        Route::post('/verify/{declaration}', [ConflictOfInterestController::class, 'verify'])->name('conflict.verify');

        Route::post('/conflict/resend/{declaration}', [ConflictOfInterestController::class, 'resendOtp'])->name('conflict.resend');

    });

});

// ══════════════════════════════════════════════════════════════════════════
//  V2 BOARD MEMBER PORTAL — Modern iPad-optimized experience
// ══════════════════════════════════════════════════════════════════════════
Route::prefix('board/portal')->middleware(['auth'])->name('board.portal.')->group(function () {
    Route::get('/', [BoardPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/calendar', [BoardPortalController::class, 'calendar'])->name('calendar');
    Route::get('/committees', [BoardPortalController::class, 'committees'])->name('committees');
    Route::get('/notices', [BoardPortalController::class, 'notices'])->name('notices');
    Route::get('/notices/{notice}', [BoardPortalController::class, 'noticeShow'])->name('notice.show');
    Route::get('/notices/{notice}/download', [BoardPortalController::class, 'noticeDownload'])->name('notice.download');
    Route::get('/papers', [BoardPortalController::class, 'papers'])->name('papers');
    Route::get('/papers/{paper}', [BoardPortalController::class, 'paperShow'])->name('paper.show');
    Route::post('/papers/{paper}/notes', [BoardPortalController::class, 'saveStickyNote'])->name('paper.note.store');
    Route::delete('/notes/{note}', [BoardPortalController::class, 'deleteStickyNote'])->name('note.destroy');
    Route::get('/active', [BoardPortalController::class, 'activeMeetings'])->name('active');
    Route::get('/meeting/{meeting}/live', [BoardPortalController::class, 'meetingLive'])->name('meeting.live');

    // Document Repository (Board Member View)
    Route::get('/repository', [BoardPortalController::class, 'repository'])->name('repository');
    Route::get('/repository/{category}', [BoardPortalController::class, 'repositoryCategory'])->name('repository.category');
    Route::get('/repository/download/{document}', [BoardPortalController::class, 'repositoryDownload'])->name('repository.download');
});
