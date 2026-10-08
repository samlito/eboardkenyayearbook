<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Twwda\MeetingWorkflowController;
use App\Http\Controllers\Twwda\InternalNoticeController;
use App\Http\Controllers\Twwda\OfficerAssignmentController;
use App\Http\Controllers\Twwda\BoardPaperWorkflowController;
use App\Http\Controllers\Twwda\DirectorReviewController;
use App\Http\Controllers\Twwda\CeoPaperReviewController;
use App\Http\Controllers\Twwda\MeetingPreparationController;
use App\Http\Controllers\Twwda\LiveMeetingController;
use App\Http\Controllers\Twwda\BoardNoticeWorkflowController;
use App\Http\Controllers\Twwda\CeoAlmanacApprovalController;
use App\Http\Controllers\Twwda\TwwdaDashboardController;

Route::prefix('workflow')->middleware(['auth', 'check.status', 'force.password.change'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────
    Route::get('/dashboard', [TwwdaDashboardController::class, 'index'])->name('workflow.dashboard');
    Route::get('/notifications', [TwwdaDashboardController::class, 'notifications'])->name('workflow.notifications');
    Route::post('/notifications/mark-read', [TwwdaDashboardController::class, 'markAllRead'])->name('workflow.notifications.markread');
    Route::post('/notifications/{id}/read', [TwwdaDashboardController::class, 'markRead'])->name('workflow.notifications.read');

    // ── Stage 1: Meeting Initiation ────────────────────────────────────────
    Route::get('/meetings/almanac', [MeetingWorkflowController::class, 'almanac'])->name('workflow.meetings.almanac');
    Route::post('/meetings/{schedule}/initiate', [MeetingWorkflowController::class, 'initiate'])->name('workflow.meetings.initiate');
    Route::get('/meetings/{meeting}', [MeetingWorkflowController::class, 'show'])->name('workflow.meetings.show');
    Route::get('/meetings/{meeting}/timeline', [MeetingWorkflowController::class, 'timeline'])->name('workflow.meetings.timeline');

    // ── Stage 2-3: Internal Notices ────────────────────────────────────────
    Route::get('/meetings/{meeting}/internal-notice', [InternalNoticeController::class, 'create'])->name('workflow.internal-notice.create');
    Route::post('/meetings/{meeting}/internal-notice', [InternalNoticeController::class, 'store'])->name('workflow.internal-notice.store');
    Route::put('/internal-notices/{notice}', [InternalNoticeController::class, 'update'])->name('workflow.internal-notice.update');
    Route::post('/internal-notices/{notice}/submit', [InternalNoticeController::class, 'submit'])->name('workflow.internal-notice.submit');
    Route::get('/internal-notices/{notice}/preview', [InternalNoticeController::class, 'preview'])->name('workflow.internal-notice.preview');

    // CEO Almanac Approval (same V1 tables — almanacs & almanac_schedules)
    Route::get('/ceo/almanac', [CeoAlmanacApprovalController::class, 'index'])->name('workflow.ceo.almanac');
    Route::get('/ceo/almanac/{almanac}', [CeoAlmanacApprovalController::class, 'show'])->name('workflow.ceo.almanac.show');
    Route::post('/ceo/almanac/{almanac}/decision', [CeoAlmanacApprovalController::class, 'decide'])->name('workflow.ceo.almanac.decision');

    // CEO Internal Notice Review
    Route::get('/ceo/internal-notices', [InternalNoticeController::class, 'ceoPending'])->name('workflow.ceo.internal-notices');
    Route::get('/ceo/internal-notices/{notice}', [InternalNoticeController::class, 'ceoReview'])->name('workflow.ceo.internal-notice.review');
    Route::post('/ceo/internal-notices/{notice}/decision', [InternalNoticeController::class, 'ceoDecision'])->name('workflow.ceo.internal-notice.decision');
    Route::post('/internal-notices/{notice}/special-approve', [InternalNoticeController::class, 'specialApprove'])->name('workflow.internal-notice.special-approve');
    Route::post('/internal-notices/{notice}/resubmit', [InternalNoticeController::class, 'resubmit'])->name('workflow.internal-notice.resubmit');

    // ── Stage 4: Officer Assignments ───────────────────────────────────────
    Route::get('/meetings/{meeting}/assign-officers', [OfficerAssignmentController::class, 'index'])->name('workflow.assignments.index');
    Route::post('/meetings/{meeting}/assign-officers', [OfficerAssignmentController::class, 'store'])->name('workflow.assignments.store');
    Route::post('/meetings/{meeting}/assign-officers/add', [OfficerAssignmentController::class, 'addMore'])->name('workflow.assignments.add');
    Route::get('/meetings/{meeting}/assignment-dashboard', [OfficerAssignmentController::class, 'dashboard'])->name('workflow.assignments.dashboard');
    Route::post('/assignments/{assignment}/mark-read', [OfficerAssignmentController::class, 'markRead'])->name('workflow.assignments.markread');

    // Officer-facing views (Division Heads see their own assignments)
    Route::get('/officer/my-assignments', [OfficerAssignmentController::class, 'myAssignments'])->name('workflow.officer.assignments');
    Route::get('/officer/assignments/{assignment}', [OfficerAssignmentController::class, 'showAssignment'])->name('workflow.officer.assignment.show');

    // ── Stage 5: Board Paper Preparation ───────────────────────────────────
    Route::get('/meetings/{meeting}/board-paper', [BoardPaperWorkflowController::class, 'create'])->name('workflow.papers.create');
    Route::post('/meetings/{meeting}/board-paper', [BoardPaperWorkflowController::class, 'store'])->name('workflow.papers.store');
    Route::get('/board-papers/{paper}', [BoardPaperWorkflowController::class, 'show'])->name('workflow.papers.show');
    Route::get('/board-papers/{paper}/edit', [BoardPaperWorkflowController::class, 'edit'])->name('workflow.papers.edit');
    Route::put('/board-papers/{paper}', [BoardPaperWorkflowController::class, 'update'])->name('workflow.papers.update');
    Route::post('/board-papers/{paper}/submit', [BoardPaperWorkflowController::class, 'submit'])->name('workflow.papers.submit');
    Route::post('/board-papers/{paper}/recall', [BoardPaperWorkflowController::class, 'recall'])->name('workflow.papers.recall');
    Route::post('/board-papers/{paper}/annexures', [BoardPaperWorkflowController::class, 'storeAnnexure'])->name('workflow.papers.annexure.store');
    Route::delete('/board-papers/{paper}/annexures/{annexure}', [BoardPaperWorkflowController::class, 'destroyAnnexure'])->name('workflow.papers.annexure.destroy');

    // ── Stage 6: Director Review ───────────────────────────────────────────
    Route::get('/director/board-papers', [DirectorReviewController::class, 'index'])->name('workflow.director.papers');
    Route::get('/director/board-papers/{paper}', [DirectorReviewController::class, 'show'])->name('workflow.director.paper.show');
    Route::post('/director/board-papers/{paper}/decision', [DirectorReviewController::class, 'decision'])->name('workflow.director.paper.decision');
    Route::post('/director/board-papers/{paper}/recall', [DirectorReviewController::class, 'recall'])->name('workflow.director.paper.recall');

    // ── Stage 7: CEO Paper Review ──────────────────────────────────────────
    Route::get('/ceo/meeting-papers', [CeoPaperReviewController::class, 'index'])->name('workflow.ceo.papers');
    Route::get('/ceo/meeting-papers/{meeting}', [CeoPaperReviewController::class, 'show'])->name('workflow.ceo.papers.show');
    Route::get('/ceo/meeting-papers/{paper}/review', [CeoPaperReviewController::class, 'review'])->name('workflow.ceo.paper.review');
    Route::post('/ceo/meeting-papers/{paper}/decision', [CeoPaperReviewController::class, 'decision'])->name('workflow.ceo.paper.decision');
    Route::post('/ceo/meeting-papers/{paper}/sticky-notes', [CeoPaperReviewController::class, 'addStickyNote'])->name('workflow.ceo.paper.sticky-note');
    Route::delete('/ceo/sticky-notes/{note}', [CeoPaperReviewController::class, 'removeStickyNote'])->name('workflow.ceo.sticky-note.destroy');
    Route::get('/board-papers/{paper}/pdf', [CeoPaperReviewController::class, 'downloadPdf'])->name('workflow.papers.pdf');

    // ── Stage 8: Meeting Preparation ───────────────────────────────────────
    Route::get('/meetings/{meeting}/prepare', [MeetingPreparationController::class, 'index'])->name('workflow.prepare.index');
    Route::post('/meetings/{meeting}/prepare/reorder', [MeetingPreparationController::class, 'reorder'])->name('workflow.prepare.reorder');
    Route::post('/meetings/{meeting}/prepare/add-item', [MeetingPreparationController::class, 'addItem'])->name('workflow.prepare.add-item');
    Route::post('/meetings/{meeting}/prepare/pulldown', [MeetingPreparationController::class, 'pulldown'])->name('workflow.prepare.pulldown');
    Route::post('/meetings/{meeting}/prepare/assign-paper', [MeetingPreparationController::class, 'assignPaper'])->name('workflow.prepare.assign-paper');
    Route::post('/meetings/{meeting}/prepare/remove-paper', [MeetingPreparationController::class, 'removePaper'])->name('workflow.prepare.remove-paper');
    Route::post('/meetings/{meeting}/prepare/remove-item', [MeetingPreparationController::class, 'removeItem'])->name('workflow.prepare.remove-item');
    Route::post('/meetings/{meeting}/prepare/finalize', [MeetingPreparationController::class, 'finalize'])->name('workflow.prepare.finalize');

    // CEO Agenda Item Review (new items + pulldown requests)
    Route::get('/ceo/agenda-items', [MeetingPreparationController::class, 'ceoAgendaPending'])->name('workflow.ceo.agenda-items');
    Route::get('/ceo/agenda-items/{item}', [MeetingPreparationController::class, 'ceoAgendaReview'])->name('workflow.ceo.agenda-item.review');
    Route::post('/ceo/agenda-items/{item}/decision', [MeetingPreparationController::class, 'ceoAgendaDecision'])->name('workflow.ceo.agenda-item.decision');

    // ── Stage 9: Live Meeting ──────────────────────────────────────────────
    Route::post('/meetings/{meeting}/start', [LiveMeetingController::class, 'start'])->name('workflow.meeting.start');
    Route::get('/meetings/{meeting}/live', [LiveMeetingController::class, 'index'])->name('workflow.meeting.live');
    Route::post('/meetings/{meeting}/attendance', [LiveMeetingController::class, 'addAttendance'])->name('workflow.meeting.attendance');
    Route::post('/meetings/{meeting}/attendance/toggle', [LiveMeetingController::class, 'toggleAttendance'])->name('workflow.meeting.attendance.toggle');
    Route::get('/meetings/{meeting}/conflict', [LiveMeetingController::class, 'conflictForm'])->name('workflow.meeting.conflict');
    Route::post('/meetings/{meeting}/conflict', [LiveMeetingController::class, 'submitConflict'])->name('workflow.meeting.conflict.submit');
    Route::post('/meetings/{meeting}/conflict/verify-otp', [LiveMeetingController::class, 'verifyOtp'])->name('workflow.meeting.conflict.verify');
    Route::post('/meetings/{meeting}/conflict/resend-otp', [LiveMeetingController::class, 'resendOtp'])->name('workflow.meeting.conflict.resend');
    Route::post('/meetings/{meeting}/conflict/sign', [LiveMeetingController::class, 'signDeclaration'])->name('workflow.meeting.conflict.sign');

    // ── Stage 9: Exports ──────────────────────────────────────────────
    Route::get('/meetings/{meeting}/export/attendance-pdf', [LiveMeetingController::class, 'exportAttendancePdf'])->name('workflow.meeting.export.attendance.pdf');
    Route::get('/meetings/{meeting}/export/attendance-csv', [LiveMeetingController::class, 'exportAttendanceCsv'])->name('workflow.meeting.export.attendance.csv');
    Route::get('/meetings/{meeting}/export/conflict-pdf', [LiveMeetingController::class, 'exportConflictPdf'])->name('workflow.meeting.export.conflict.pdf');
    Route::get('/meetings/{meeting}/export/conflict-csv', [LiveMeetingController::class, 'exportConflictCsv'])->name('workflow.meeting.export.conflict.csv');

    // ── Stage 10: Minutes ────────────────────────────────────────────
    Route::post('/meetings/{meeting}/minutes/save', [LiveMeetingController::class, 'saveMinutes'])->name('workflow.meeting.minutes.save');
    Route::get('/meetings/{meeting}/export/minutes-word', [LiveMeetingController::class, 'exportMinutesWord'])->name('workflow.meeting.export.minutes.word');

    // ── Board Notices (Official Board Member Notifications) ──────────────────
    Route::get('/board-notices', [BoardNoticeWorkflowController::class, 'index'])->name('workflow.board-notices.index');
    Route::get('/meetings/{meeting}/board-notice', [BoardNoticeWorkflowController::class, 'create'])->name('workflow.board-notice.create');
    Route::post('/meetings/{meeting}/board-notice', [BoardNoticeWorkflowController::class, 'store'])->name('workflow.board-notice.store');
    Route::put('/board-notices/{notice}', [BoardNoticeWorkflowController::class, 'update'])->name('workflow.board-notice.update');
    Route::post('/board-notices/{notice}/submit', [BoardNoticeWorkflowController::class, 'submit'])->name('workflow.board-notice.submit');
    Route::get('/board-notices/{notice}/preview', [BoardNoticeWorkflowController::class, 'preview'])->name('workflow.board-notice.preview');
    Route::post('/board-notices/{notice}/send', [BoardNoticeWorkflowController::class, 'send'])->name('workflow.board-notice.send');
    Route::post('/board-notices/{notice}/reminder', [BoardNoticeWorkflowController::class, 'reminder'])->name('workflow.board-notice.reminder');
    Route::post('/board-notices/{notice}/self-approve', [BoardNoticeWorkflowController::class, 'selfApprove'])->name('workflow.board-notice.self-approve');
    Route::post('/board-notices/{notice}/reschedule', [BoardNoticeWorkflowController::class, 'reschedule'])->name('workflow.board-notice.reschedule');
    Route::get('/board-notices/{notice}/history', [BoardNoticeWorkflowController::class, 'history'])->name('workflow.board-notice.history');

    // CEO Board Notice Review
    Route::get('/ceo/board-notices', [BoardNoticeWorkflowController::class, 'ceoPending'])->name('workflow.ceo.board-notices');
    Route::get('/ceo/board-notices/{notice}', [BoardNoticeWorkflowController::class, 'ceoReview'])->name('workflow.ceo.board-notice.review');
    Route::post('/ceo/board-notices/{notice}/decision', [BoardNoticeWorkflowController::class, 'ceoDecision'])->name('workflow.ceo.board-notice.decision');
});

// Old /twwda/... links in saved notifications and sent emails
Route::get('/twwda/{path?}', \App\Http\Controllers\Twwda\LegacyWorkflowRedirectController::class)
    ->where('path', '.*')
    ->middleware('auth')
    ->name('workflow.legacy.redirect');
