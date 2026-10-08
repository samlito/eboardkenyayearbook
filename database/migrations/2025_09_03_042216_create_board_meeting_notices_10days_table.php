<?php
//
//use Illuminate\Database\Migrations\Migration;
//use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Support\Facades\Schema;
//
//return new class extends Migration
//{
//    /**
//     * Run the migrations.
//     *
//     * @return void
//     */
//    public function up()
//    {
//        Schema::table('board_meeting_notices_10days', function (Blueprint $table) {
//            $table->string('document_path')->nullable()->after('notice_text');
//        });
//    }
//
//    /**
//     * Reverse the migrations.
//     *
//     * @return void
//     */
//    public function down()
//    {
//        Schema::table('board_meeting_notices_10days', function (Blueprint $table) {
////            $table->dropColumn('document_path');
//        });
//    }
//};


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('board_meeting_notices_10days')) {

            Schema::create('board_meeting_notices_10days', function (Blueprint $table) {
                $table->id();

                $table->foreignId('meeting_id')
                    ->constrained('board_meetings')
                    ->cascadeOnDelete();

                $table->text('notice_text');

                $table->json('agenda_order')->nullable();
                $table->string('reference_number')->nullable();
                $table->string('reference')->nullable();
                $table->date('submission_date')->nullable();
                $table->timestamp('published_at')->nullable();
//                $table->string('document_path')->nullable();

                $table->timestamps();
            });

        } else {

//            if (!Schema::hasColumn('board_meeting_notices_10days', 'document_path')) {
//                Schema::table('board_meeting_notices_10days', function (Blueprint $table) {
//                    $table->string('document_path')->nullable()->after('notice_text');
//                });
//            }

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        if (Schema::hasTable('board_meeting_notices_10days')
//            && Schema::hasColumn('board_meeting_notices_10days', 'document_path')) {
//
//            Schema::table('board_meeting_notices_10days', function (Blueprint $table) {
//                $table->dropColumn('document_path');
//            });
//        }
    }
};
