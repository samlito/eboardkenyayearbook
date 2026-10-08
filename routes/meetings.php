<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Meetings\ActualMeetingController;



Route::prefix('actual-meetings')->name('actual.meetings.')->controller(ActualMeetingController::class)->group(function () {
    Route::get('/','index')->name('index');
    Route::get('/{meeting}/prepare-notice',  'prepareNotice')->name('prepareNotice');
    Route::post('/{meeting}/publish-notice', 'publishNotice')->name('publishNotice');


    Route::get('/{id}/add-notice','createNotice')->name('notice.create');
    Route::post('/{id}/add-notice',  'storeNotice')->name('notice.store');

    Route::get('{id}/notice/edit','edit')->name('notice.edit');
    Route::get('{id}/notice/preview', 'preview')->name('notice.preview');
    Route::post('{id}/notice/send',  'send')->name('notice.send');

//    Route::get('{meeting}/minutes/capture',  'captureMinutes')->name('minutes.capture');
    Route::get('{meeting}/minutes/capture',  'create')->name('minutes.capture');

    Route::post('{meeting}/minutes/save',  'storeMinutes')->name('minutes.store');

    Route::get('{id}/minutes/preview',  'previewMinutes')->name('minutes.preview');

    Route::get('/{meeting}/conflict-of-interest-pdf', 'downloadConflictRegister')
        ->name('conflict.download');


    Route::get('meetings/{id}/attendance-register', 'downloadAttendanceRegister')->name('attendance.download');




    Route::post('/{id}/start',  'startMeeting')->name('start');

});


