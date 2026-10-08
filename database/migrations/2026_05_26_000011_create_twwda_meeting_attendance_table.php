<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_meeting_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();
            $table->foreignId('board_member_id')->nullable()->constrained('board_members')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('attendee_name', 255)->nullable();
            $table->string('attendee_designation', 255)->nullable();
            $table->enum('attendance_type', ['board_member', 'staff', 'external'])->default('board_member');
            $table->timestamp('joined_at')->nullable();
            $table->boolean('is_present')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_meeting_attendance');
    }
};
