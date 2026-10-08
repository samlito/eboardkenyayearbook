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
            $table->boolean('is_started')->default(false);   // Has the meeting been started?
            $table->timestamp('started_at')->nullable();     // When was it started?
            $table->unsignedBigInteger('started_by')->nullable(); // Who started it

            $table->foreign('started_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
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
            //
        });
    }
};
