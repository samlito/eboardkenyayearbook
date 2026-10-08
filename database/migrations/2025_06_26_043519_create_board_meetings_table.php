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
        Schema::create('board_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date');
            $table->string('venue');
            $table->string('type'); // e.g. Board, Committee
            $table->string('status')->default('Scheduled'); // Scheduled, Initiated, Completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_meetings');
    }
};
