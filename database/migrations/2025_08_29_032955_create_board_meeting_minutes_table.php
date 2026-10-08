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
        Schema::create('board_meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('board_meetings')->onDelete('cascade');

            $table->string('minutes_title')->nullable();

            $table->json('present_members')->nullable();
            $table->json('absent_members')->nullable();
            $table->text('in_attendance')->nullable(); // Free text (e.g. "John Doe - Legal Advisor")

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
        Schema::dropIfExists('board_meeting_minutes');
    }
};
