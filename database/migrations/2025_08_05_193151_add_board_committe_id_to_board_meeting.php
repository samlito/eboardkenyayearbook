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
            $table->foreignId('board_committee_id')->nullable()->after('type')->constrained()->onDelete('set null');
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
            $table->dropForeign(['board_committee_id']);
            $table->dropColumn('board_committee_id');
        });
    }
};
