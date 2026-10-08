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
        Schema::table('board_members', function (Blueprint $table) {
            $table->integer('county_id');
            $table->string('postal_address');
            $table->string('photo')->nullable();
            $table->date('exit_date')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('experience_summary')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('director_type', ['Chairperson', 'Member'])->nullable();
            $table->enum('role_type', ['independent', 'representative'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('board_committees', function (Blueprint $table) {

        });
    }
};
