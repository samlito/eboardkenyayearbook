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
    public function up(): void
    {
        Schema::table('board_meetings', function (Blueprint $table) {
            $table->boolean('active')
                ->default(true)
                ->after('id'); // adjust position if needed
        });
    }

    public function down(): void
    {
        Schema::table('board_meeting_assignments', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
