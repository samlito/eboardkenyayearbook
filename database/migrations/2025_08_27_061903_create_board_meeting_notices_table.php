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
        Schema::create('board_meeting_notices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id')->unique(); // One-to-one
            $table->text('notice_text');
            $table->json('agenda_order')->nullable(); // array of board_paper_ids in preferred order
            $table->timestamp('published_at')->nullable(); // when officially sent
            $table->timestamps();

            $table->foreign('meeting_id')->references('id')->on('board_meetings')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_meeting_notices');
    }
};
