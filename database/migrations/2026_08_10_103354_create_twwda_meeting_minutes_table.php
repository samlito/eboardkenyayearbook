<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('twwda_meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();
            $table->string('minutes_title')->nullable();
            $table->json('present_members')->nullable();
            $table->json('absent_members')->nullable();
            $table->text('in_attendance')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('twwda_minute_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_minutes_id')->constrained('twwda_meeting_minutes')->cascadeOnDelete();
            $table->foreignId('agenda_item_id')->nullable()->constrained('twwda_meeting_agenda_items')->nullOnDelete();
            $table->string('minute_number')->nullable();
            $table->string('agenda_title');
            $table->text('discussion')->nullable();
            $table->text('comments')->nullable();
            $table->text('resolution')->nullable();
            $table->string('proposer')->nullable();
            $table->string('seconder')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('twwda_minute_items');
        Schema::dropIfExists('twwda_meeting_minutes');
    }
};
