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
        Schema::table('board_notices', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->after('meeting_id');
            $table->date('reference_date')->nullable()->after('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('board_notices', function (Blueprint $table) {
            $table->dropColumn(['reference_number', 'reference_date']);
        });
    }
};
