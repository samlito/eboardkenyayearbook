<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_officer_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('director_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('agenda_item_number', 50)->nullable();
            $table->string('agenda_item_title', 500)->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('email_read_at')->nullable();
            $table->timestamp('system_read_at')->nullable();
            $table->enum('status', [
                'assigned', 'pending', 'notified', 'read', 'paper_submitted',
                'paper_approved', 'paper_rejected'
            ])->default('assigned');
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_officer_assignments');
    }
};
