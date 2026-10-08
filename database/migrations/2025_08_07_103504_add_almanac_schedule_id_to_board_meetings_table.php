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
            $table->unsignedBigInteger('almanac_schedule_id')->nullable()->after('board_committee_id');
            $table->foreign('almanac_schedule_id')
                ->references('id')
                ->on('almanac_schedules')
                ->onDelete('set null');
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
            $table->dropForeign(['almanac_schedule_id']);
            $table->dropColumn('almanac_schedule_id');
        });
    }
};
