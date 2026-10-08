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
        Schema::table('almanac_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('board_committee_id')->nullable()->after('meeting_id');

            $table->foreign('board_committee_id')
                ->references('id')->on('board_committees')
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
        Schema::table('almanac_schedules', function (Blueprint $table) {
            $table->dropForeign(['board_committee_id']);
            $table->dropColumn('board_committee_id');
        });
    }
};
