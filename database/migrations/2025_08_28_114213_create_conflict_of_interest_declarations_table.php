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
        Schema::create('conflict_of_interest_declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->unsignedBigInteger('board_member_id');
            $table->enum('conflict_status', ['Yes', 'No']);
            $table->text('details')->nullable();
            $table->timestamps();

            // Shorter name for unique constraint
            $table->unique(['meeting_id', 'board_member_id'], 'coi_meeting_member_unique');

            $table->foreign('meeting_id')->references('id')->on('board_meetings')->onDelete('cascade');
            $table->foreign('board_member_id')->references('id')->on('board_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conflict_of_interest_declarations');
    }
};
