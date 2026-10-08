<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_board_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('twwda_officer_assignments')->nullOnDelete();
            $table->foreignId('prepared_by')->constrained('users')->cascadeOnDelete();
            $table->string('agenda_item_number', 50)->nullable();
            $table->string('paper_number', 100)->nullable();
            $table->string('title', 500);
            $table->longText('content')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->enum('status', [
                'draft', 'submitted', 'director_review', 'director_approved',
                'director_rejected', 'ceo_review', 'ceo_approved', 'ceo_rejected'
            ])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('director_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('director_approved_at')->nullable();
            $table->timestamp('ceo_approved_at')->nullable();
            $table->longText('ceo_signature')->nullable();
            $table->integer('display_order')->nullable();
            $table->boolean('is_auto_approved')->default(false);
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_board_papers');
    }
};
