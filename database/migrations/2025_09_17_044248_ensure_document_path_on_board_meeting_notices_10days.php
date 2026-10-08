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
    {// Add the column if it's missing (works across drivers)
        if (!Schema::hasColumn('board_meeting_notices_10days', 'document_path')) {
            Schema::table('board_meeting_notices_10days', function (Blueprint $table) {
                $table->string('document_path')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('board_meeting_notices_10days', 'document_path')) {
            Schema::table('board_meeting_notices_10days', function (Blueprint $table) {
                $table->dropColumn('document_path');
            });
        }
    }
};
