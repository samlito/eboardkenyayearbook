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
        Schema::create('minute_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_meeting_minutes_id')->constrained('board_meeting_minutes')->onDelete('cascade');

            $table->string('minute_number')->nullable(); // e.g. MIN/2025/01
            $table->string('agenda_title');
            $table->text('description')->nullable();
            $table->text('board_comments')->nullable();
            $table->text('board_resolution')->nullable();

            $table->foreignId('proposer_id')->nullable()->constrained('board_members')->onDelete('set null');
            $table->foreignId('seconder_id')->nullable()->constrained('board_members')->onDelete('set null');

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
        Schema::dropIfExists('minute_items');
    }
};
