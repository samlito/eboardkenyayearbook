    <?php
    use App\Http\Controllers\ProfileController;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AdminController;
    use App\Http\Controllers\Backend\RoleController;
    use App\Http\Controllers\Backend\UsersController;
    use App\Http\Controllers\Backend\BoardMemberController;
    use App\Http\Controllers\LoggedInController;
    use App\Http\Controllers\UserGuideController;
    use App\Http\Controllers\Backend\VerificationController;
    use App\Http\Controllers\Backend\AlmanacController;
    use App\Http\Controllers\Backend\AlmanacApprovalController;
    use App\Http\Controllers\BoardMeetingController;
    use App\Http\Controllers\BoardNoticeController;
    use App\Http\Controllers\DivisionHeadController;
    use App\Http\Controllers\CEOBoardReviewController;
    use App\Http\Controllers\BoardPaperController;
    use App\Http\Controllers\Admin\BoardCommitteeController;
    use App\Http\Controllers\Admin\BoardCommitteeMemberController;
    use App\Http\Controllers\Backend\MeetingManagementController;
    use App\Http\Controllers\DivisionController;
    use App\Http\Controllers\DirectorateController;
    USE App\Http\Controllers\Security\OtpVerificationController;
    use App\Http\Controllers\Security\AuditLogController;
    use App\Http\Controllers\SetupWizardController;
    use App\Http\Controllers\Admin\AdminSettingsController;
    use App\Http\Controllers\Backend\FinancialYearController;


    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
//    Route::get('/', function () {
//        return view('welcome');
//    });
//    Route::get('/dashboard', function () {
//        return view('index');
//    })->middleware(['auth', 'verified'])->name('dashboard');

    // Setup Wizard Routes (exempt from setup check)
    Route::prefix('setup')->withoutMiddleware([\App\Http\Middleware\EnsureSetupComplete::class])->group(function () {
        Route::get('/', [SetupWizardController::class, 'index'])->name('setup.index');
        Route::get('/step/{step}', [SetupWizardController::class, 'show'])->name('setup.show');
        Route::post('/step/{step}', [SetupWizardController::class, 'process'])->name('setup.process');
        Route::get('/complete', [SetupWizardController::class, 'complete'])->name('setup.complete');
    });

    Route::get('/', function () {
        $heroSlides = setting('landing_hero_slides', []) ?: [];
        $features = setting('landing_features', []) ?: [];
        $boardMembers = setting('landing_board_members', []) ?: [];
        return view('landing.index', compact('heroSlides', 'features', 'boardMembers'));
    });
    //    Route::get('/dashboard', function () {
    //        return view('index');
    //    })->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/dashboard', [LoggedInController::class, 'index'])
        ->middleware(['auth', 'verified', 'check.status'])  // Correct syntax for middleware
        ->name('dashboard');

    Route::get('/verify-code', [VerificationController::class, 'showVerificationForm'])->name('verify.code');
    //Route::post('/verify-code', [App\Http\Controllers\Auth\VerificationController::class, 'verifyCode']);
    Route::post('/verify-code', [VerificationController::class, 'verifyCode'])->middleware('throttle:5,1');
    Route::post('/resend-code', [VerificationController::class, 'resendCode'])->name('resend.code');
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    require __DIR__.'/auth.php';

    Route::get('/admin/logout', [AdminController::class, 'AdminDestroy'])->name('admin.logout');

    Route::get('/logout', [AdminController::class, 'AdminLogoutPage'])->name('admin.logout.page');

    Route::get('/account-restricted', function () {
        return view('auth.restricted');
    })->name('account.restricted');

    Route::get('/otp/verify', [OtpVerificationController::class, 'showVerifyForm'])->name('otp.verify.form');
    Route::post('/otp/verify', [OtpVerificationController::class, 'verify'])->name('otp.verify');
    Route::get('/otp/resend', [OtpVerificationController::class, 'resend'])->name('otp.resend');

    Route::middleware(['auth','check.status','force.password.change'])->group(function(){

        Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');

        Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');


        Route::get('/change/password', [AdminController::class, 'ChangePassword'])->name('change.password');

        Route::post('/update/password', [AdminController::class, 'UpdatePassword'])->name('update.password');

        Route::get('/user-guide', [UserGuideController::class, 'index'])->name('user.guide');
        Route::get('/user-guide/pdf', [UserGuideController::class, 'pdf'])->name('user.guide.pdf');


        Route::controller(RoleController::class)->group(function() {
            Route::get('/all/permission','AllPermission')->name('all.permission');
            Route::get('/add/permission','AddPermission')->name('add.permission');
            Route::post('/store/permission','StorePermission')->name('permission.store');
            Route::get('/edit/permission/{id}','EditPermission')->name('edit.permission');

            Route::post('/update/permission','UpdatePermission')->name('permission.update');
            Route::get('/delete/permission/{id}','DeletePermission')->name('delete.permission');

            Route::get('/all/roles','AllRoles')->name('all.roles');
            Route::get('/add/roles','AddRoles')->name('add.roles');
            Route::post('/store/roles','StoreRoles')->name('roles.store');
            Route::get('/edit/roles/{id}','EditRoles')->name('edit.roles');
            Route::post('/update/roles','UpdateRoles')->name('roles.update');
            Route::get('/delete/roles/{id}','DeleteRoles')->name('delete.roles');



            Route::get('/add/roles/permission','AddRolesPermission')->name('add.roles.permission');
            Route::post('/role/permission/store','StoreRolesPermission')->name('role.permission.store');

            Route::get('/all/roles/permission','AllRolesPermission')->name('all.roles.permission');
            Route::get('/admin/edit/roles/{id}','AdminEditRoles')->name('admin.edit.roles');
            Route::post('/role/permission/update/{id}','RolePermissionUpdate')->name('role.permission.update');
            Route::get('/admin/delete/roles/{id}','AdminDeleteRoles')->name('admin.delete.roles');

        });

        Route::controller(BoardMemberController::class)->group(function() {
            Route::get('/all/boardmembers','index')->name('all.boardmembers');
            Route::get('/add/boardmembers','create')->name('boardmembers.add');
            Route::post('/store/boardmembers','store')->name('boardmembers.store');
            Route::get('/show/boardmembers/{id}', 'show')->name('boardmembers.show'); // View details of a board member
            Route::get('/edit/boardmembers/{id}', 'edit')->name('boardmembers.edit'); // Show edit form
            Route::put('/update/boardmembers/{id}', 'update')->name('boardmembers.update'); // Update board member details
            Route::delete('/delete/boardmembers/{id}', 'destroy')->name('boardmembers.delete'); // Delete a board member
            Route::post('/toggle-status/boardmembers/{id}', 'toggleStatus')->name('boardmembers.toggleStatus');
            Route::post('/create-user/boardmembers/{id}', 'createUser')->name('boardmembers.createUser');
        });

        Route::controller(AlmanacController::class)->group(function() {
            Route::get('/all/almanac/main','almanacindex')->name('all.almanac.main');
            Route::post('/almanac/{id}/upload', 'uploadSigned')->name('almanac.upload');
            Route::get('/all/almanac/rejected','almanacrejectedindex')->name('all.almanac.rejected');

            Route::get('/add/almanac/main','almaniccreate')->name('main.almanac.add');
            Route::post('/store/almanac/main','almanacstore')->name('main.almanac.store');
            Route::get('/show/almanac/{id}','show')->name('almanac.show.all');
            Route::get('/all/almanac','index')->name('all.almanac');
            Route::get('/add/almanac/{id}','create')->name('almanac.add');
            Route::get('/add/toapprovedalmanac/almanac/{id}','createtoapproved')->name('almanac.add.toapprovedalmanac');

            Route::post('/store/almanac/{id}','store')->name('almanac.store');
            Route::post('/store/almanac/ap/{id}','storeAp')->name('almanac.store.approved');

            Route::get('/edit/almanac/{id}', 'edit')->name('edit.almanac');
            Route::get('/delete/almanac/{id}', 'destroy')->name('delete.almanac');
            Route::post('/update/almanac/{id}', 'update')->name('update.almanac');
            Route::post('/almanac/generate-pdf', 'generatePdf')->name('almanac.generate.pdf');
            Route::get('/almanac/{id}/pdf',  'generatePdf2')->name('almanac.pdf');

            Route::post('/almanac/{id}/submit', 'submitForApproval')->name('almanac.submit.approval');

            Route::get('/almanac/{id}/download',  'downloadPdf')->name('almanac.download');

        });

        Route::controller(AlmanacApprovalController::class)->group(function() {
            Route::get('/all/almanac/pendingceoapproval','pendingceoapproval')->name('all.almanac.pendingceoapproval');
            Route::get('/show/pending/almanac/{id}','showpending')->name('almanac.show.pending');
            Route::post('/almanac/{id}/approval',  'approve')->name('almanac.approval');
            Route::get('/show/approved/almanac/{id}','showapproved')->name('almanac.show.approved');
            Route::get('/all/almanac/ceoapproved','ceoapproved')->name('all.almanac.ceoapproved');

        });






        Route::prefix('users')->controller(UsersController::class)
            ->middleware('role:Superadmin|Corporation Secretary')
            ->group(function() {
            Route::get('/index','index')->name('users.index');
            Route::get('/add','create')->name('users.add');
            Route::post('/store','store')->name('users.store');
            Route::get('/{user}/edit', 'edit')->name('users.edit');
            Route::put('/{user}', 'update')->name('users.update');
            Route::post('/change-status/{id}',  'changeStatus');
            Route::post('/change-status', 'changeStatus')->name('users.changeStatus');
            Route::post('/{id}/unlock', 'unlock')->name('users.unlock');
        });


        Route::prefix('board-meetings')->controller(BoardMeetingController::class)->group(function() {
            Route::get('/all', 'index')->name('board.meetings.all');
            Route::get('/add',  'create')->name('board.meetings.add');
            Route::post('/store',  'store')->name('board.meetings.store');
            Route::get('/initiate/{id}', 'initiate')->name('board.meetings.initiate');



            Route::get('{id}/notice/preview', 'preview')->name('10day.notice.preview');
            Route::post('{id}/notice/send',  'send')->name('10day.notice.send');


            Route::get('/board-papers', 'indexBoardPapers')->name('cs.boardmeetings.index');
            Route::get('/my-meetings/{meeting}', 'show')->name('cs.my.meetings.show');


        });

        Route::get('/board-meetings/{id}/notice', [BoardMeetingController::class, 'viewNotice'])->name('board.meetings.view.notice');
        Route::get('/board-meetings/{id}/notify-heads', [BoardMeetingController::class, 'notifyDivisionHeads'])->name('board.meetings.notify.heads');
        Route::post('/board-meetings/{id}/send-notification', [BoardMeetingController::class, 'sendDivisionNotifications'])->name('board.meetings.send.heads');
        Route::get('/board-meetings/{id}/notify-ceo', [BoardMeetingController::class, 'notifyCeo'])->name('board.meetings.notify.ceo');


        Route::post('/board-notice/store', [BoardNoticeController::class, 'store'])->name('board.notice.store');
        Route::post('/board-meetings/{id}/notify-board', [BoardMeetingController::class, 'notifyBoard'])
            ->name('board.meetings.notify.board');


                Route::get('/board-meetings/10-day-notice/{id}', [BoardMeetingController::class, 'send10DayNotice'])->name('board.meetings.notify.board10');
        Route::post('/board-meetings/{id}/add-notice-10',  [BoardMeetingController::class,'storeNotice'])->name('10day.notice.store');
        Route::get('/board-meetings/almanac', [BoardMeetingController::class, 'selectSchedule'])->name('board_meetings.select_schedule');


        Route::post('/board-meetings/initiate/{id}', [BoardMeetingController::class, 'storeFromSchedule'])->name('board_meetings.initiate');

        Route::prefix('ceo')->group(function () {
            Route::get('/notices/pending', [BoardNoticeController::class, 'pending'])->name('ceo.notices.pending');
            Route::get('/notice/review/{id}', [BoardNoticeController::class, 'review'])->name('ceo.notice.review');
            Route::post('/notice/decision/{id}', [BoardNoticeController::class, 'decision'])->name('ceo.notice.decision');
        });


        Route::prefix('division-head')->middleware(['auth'])->group(function () {
            Route::get('/my-meetings', [DivisionHeadController::class, 'index'])->name('division.head.meetings');
            Route::get('/my-meetings-audit', [DivisionHeadController::class, 'indexAudit'])->name('division.head.meetings.audit');

            Route::get('/submit-paper/{meeting_id}', [DivisionHeadController::class, 'create'])->name('division.head.submit');
            Route::post('/submit-paper/{meeting_id}', [DivisionHeadController::class, 'store'])->name('division.head.store');

             Route::get('/manager/revisions', [BoardPaperController::class, 'revisions'])->name('manager.revisions');
             Route::post('/manager/revisions/{id}/resubmit', [BoardPaperController::class, 'resubmitRevision'])->name('manager.revisions.resubmit');

            Route::get('/meetings/{meeting}/submissions', [DivisionHeadController::class, 'submissions'])
                ->name('division.head.submissions');

            Route::get('/meetings/{meeting}/submissions/audit', [DivisionHeadController::class, 'submissionsAudit'])
                ->name('division.head.submissions.audit');

        });



        // routes/web.php

// Paper edit/update
        Route::get('/papers/{paper}/edit', [DivisionHeadController::class, 'editPaper'])
            ->name('division.head.paper.edit');

        Route::patch('/papers/{paper}', [DivisionHeadController::class, 'updatePaper'])
            ->name('division.head.paper.update');

// Annexures: create, update, delete
        Route::post('/papers/{paper}/annexures', [DivisionHeadController::class, 'storeAnnexure'])
            ->name('division.head.annexure.store');

        Route::patch('/annexures/{annexure}', [DivisionHeadController::class, 'updateAnnexure'])
            ->name('division.head.annexure.update');

        Route::delete('/annexures/{annexure}', [DivisionHeadController::class, 'destroyAnnexure'])
            ->name('division.head.annexure.destroy');


        Route::prefix('ceo')->middleware(['auth'])->group(function () {
            Route::get('/board-papers', [CEOBoardReviewController::class, 'index'])->name('ceo.board.papers.index');
            Route::get('/board-papers/{meeting_id}', [CEOBoardReviewController::class, 'show'])->name('ceo.board.papers.show');
//            Route::post('/board-paper/{paper_id}/decision', [CEOBoardReviewController::class, 'decision'])->name('ceo.board.paper.decision');
//            Route::post('/ceo/board-paper/decision/{id}', [CEOBoardReviewController::class, 'decision'])->name('ceo.board.paper.decision');
            Route::post('/board-paper/decision/{id}', [CEOBoardReviewController::class, 'decision'])->name('ceo.board.paper.decision');
            Route::post('/ceo/board/send-notice/{id}', [CEOBoardReviewController::class, 'sendNotice'])->name('ceo.send.notice');
        });



        Route::prefix('admin/committees')->group(function () {
            Route::get('/', [BoardCommitteeController::class, 'index'])->name('committees.index');
            Route::get('/create', [BoardCommitteeController::class, 'create'])->name('committees.create');
            Route::post('/store', [BoardCommitteeController::class, 'store'])->name('committees.store');
            Route::get('/{id}/edit', [BoardCommitteeController::class, 'edit'])->name('committees.edit');
            Route::post('/{id}/update', [BoardCommitteeController::class, 'update'])->name('committees.update');
        });
        Route::prefix('board-committees')->middleware(['auth'])->group(function () {
            Route::get('{committee}/members', [BoardCommitteeMemberController::class, 'index'])->name('committees.members.index');
            Route::post('{committee}/members', [BoardCommitteeMemberController::class, 'store'])->name('committees.members.store');
        });





        Route::prefix('meetings/manage')->middleware(['auth'])->group(function () {
            Route::get('/', [MeetingManagementController::class, 'index'])->name('meetings.manage.index');
            Route::get('/{id}/publish', [MeetingManagementController::class, 'publishForm'])->name('meetings.manage.publish');
            Route::post('/{id}/publish', [MeetingManagementController::class, 'publish'])->name('meetings.manage.publish.save');
            Route::post('/{id}/remind', [MeetingManagementController::class, 'sendReminder'])->name('meetings.manage.remind');
        });

        // Directorates CRUD
        Route::prefix('directorates')->controller(DirectorateController::class)->group(function () {
            Route::get('/', 'index')->name('directorates.index');
            Route::get('/create', 'create')->name('directorates.create');
            Route::post('/store', 'store')->name('directorates.store');
            Route::get('/{directorate}/edit', 'edit')->name('directorates.edit');
            Route::put('/{directorate}', 'update')->name('directorates.update');
            Route::delete('/{directorate}', 'destroy')->name('directorates.destroy');
            Route::post('/{directorate}/assign-director', 'assignDirector')->name('directorates.assign-director');
            Route::delete('/{directorate}/remove-director', 'removeDirector')->name('directorates.remove-director');
        });

        // Divisions CRUD
        Route::prefix('divisions')->controller(DivisionController::class)->group(function () {
            Route::get('/', 'index')->name('divisions.index');
            Route::get('/create',  'create')->name('divisions.create');
            Route::post('/store',  'store')->name('divisions.store');
            Route::get('/{division}/edit',  'edit')->name('divisions.edit');
            Route::put('/{division}', 'update')->name('divisions.update');
            Route::delete('/{division}',  'destroy')->name('divisions.destroy');
        });



        Route::prefix('audit-logs')->middleware(['auth'])->group(function () {
            // Main index with filters
            Route::get('/', [AuditLogController::class, 'index'])->name('audit.logs.index');

            // Export options (optional if you add later)
            Route::get('/export/pdf', [AuditLogController::class, 'exportPdf'])->name('audit.logs.export.pdf');
        });

        // Financial Years (Superadmin only)
        Route::prefix('admin/financial-years')->group(function () {
            Route::get('/', [FinancialYearController::class, 'index'])->name('financial-years.index');
            Route::post('/store', [FinancialYearController::class, 'store'])->name('financial-years.store');
            Route::post('/{id}/toggle', [FinancialYearController::class, 'toggleStatus'])->name('financial-years.toggle');
            Route::delete('/{id}', [FinancialYearController::class, 'destroy'])->name('financial-years.destroy');
        });

        // Document Repository (Corporation Secretary / Superadmin)
        Route::prefix('repository')->middleware('role:Superadmin|Corporation Secretary')
            ->controller(\App\Http\Controllers\DocumentRepositoryController::class)
            ->group(function () {
                Route::get('/', 'index')->name('repository.index');
                Route::get('/categories/create', 'createCategory')->name('repository.category.create');
                Route::post('/categories', 'storeCategory')->name('repository.category.store');
                Route::get('/categories/{category}/edit', 'editCategory')->name('repository.category.edit');
                Route::put('/categories/{category}', 'updateCategory')->name('repository.category.update');
                Route::delete('/categories/{category}', 'destroyCategory')->name('repository.category.destroy');
                Route::get('/categories/{category}', 'showCategory')->name('repository.category.show');
                Route::get('/categories/{category}/documents/create', 'createDocument')->name('repository.document.create');
                Route::post('/categories/{category}/documents', 'storeDocument')->name('repository.document.store');
                Route::get('/categories/{category}/documents/{document}/edit', 'editDocument')->name('repository.document.edit');
                Route::put('/categories/{category}/documents/{document}', 'updateDocument')->name('repository.document.update');
                Route::delete('/categories/{category}/documents/{document}', 'destroyDocument')->name('repository.document.destroy');
                Route::get('/documents/{document}/download', 'downloadDocument')->name('repository.document.download');
            });

        // System Settings (Superadmin only)
        Route::prefix('admin/settings')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
            Route::post('/update', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
            Route::post('/branding', [AdminSettingsController::class, 'updateBranding'])->name('admin.settings.branding');
            Route::post('/landing-page', [AdminSettingsController::class, 'updateLandingPage'])->name('admin.settings.landing');
            Route::post('/test-email', [AdminSettingsController::class, 'testEmail'])->name('admin.settings.test-email');
        });

    }); // End User Middleware

    require __DIR__.'/board.php';
    require __DIR__.'/meetings.php';
    require __DIR__.'/resolutions.php';
    require __DIR__.'/twwda.php';
