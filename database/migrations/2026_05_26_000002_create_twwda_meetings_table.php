<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almanac_schedule_id')->nullable()->constrained('almanac_schedules')->nullOnDelete();
            $table->foreignId('board_committee_id')->nullable()->constrained('board_committees')->nullOnDelete();
            $table->string('title', 500);
            $table->date('meeting_date');
            $table->time('meeting_time')->nullable();
            $table->string('venue', 255)->nullable();
            $table->string('reference_number', 100)->nullable()->unique();
            $table->enum('status', [
                'draft', 'initiated', 'notice_created', 'notice_pending_approval',
                'notice_approved', 'notice_rejected', 'officers_notified',
                'papers_in_progress', 'papers_submitted', 'papers_level1_review',
                'papers_ceo_review', 'papers_approved', 'meeting_prepared',
                'meeting_active', 'meeting_completed'
            ])->default('draft');
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('initiated_at')->nullable();
            $table->tinyInteger('current_stage')->default(1);
            $table->boolean('is_started')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->foreignId('started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_meetings');
    }
};
