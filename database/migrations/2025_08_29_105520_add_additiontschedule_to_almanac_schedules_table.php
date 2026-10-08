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
            $table->boolean('addition_to_schedule')->default(0)->after('supporting_document');
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
            //
        });
    }
};
