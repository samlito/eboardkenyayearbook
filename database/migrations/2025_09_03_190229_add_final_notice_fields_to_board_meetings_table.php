<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('board_meetings', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->after('status');

            // Final (3-day) notice
            $table->timestamp('final_board_notice_sent_at')->nullable()->after('board_notified_at');
            $table->unsignedBigInteger('final_notice_sent_by')->nullable()->after('final_board_notice_sent_at');

            // Who sent the 10-day notice
            $table->unsignedBigInteger('board_notice_sent_by')->nullable()->after('board_notified_at');

            // Foreign keys to users
            $table->foreign('final_notice_sent_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('board_notice_sent_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('board_meetings', function (Blueprint $table) {
            $table->dropForeign(['final_notice_sent_by']);
            $table->dropForeign(['board_notice_sent_by']);

            $table->dropColumn([
                'reference_number',
                'final_board_notice_sent_at',
                'final_notice_sent_by',
                'board_notice_sent_by',
            ]);
        });
    }
};
