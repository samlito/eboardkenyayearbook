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
        Schema::create('board_members', function (Blueprint $table) {
            $table->id();
            $table->integer('salutation');
            $table->string('names');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('idno');
            $table->string('designation');
            $table->string('place_of_residence');
            $table->string('upload_id')->nullable();
            $table->string('upload_cv')->nullable();
            $table->string('appointment_letter')->nullable();
            $table->string('gazettee_notice')->nullable();
            $table->string('acceptance_letter')->nullable();
            $table->date('appointment_date');
            $table->date('end_date')->nullable();
            $table->string('status')->nullable();
            $table->string('addedby')->nullable();

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
        Schema::dropIfExists('board_members');
    }
};
