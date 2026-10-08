<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_internal_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();
            $table->string('reference_number', 100)->nullable();
            $table->date('reference_date')->nullable();
            $table->string('subject', 500);
            $table->longText('body')->nullable();
            $table->json('agenda_items')->nullable();
            $table->string('meeting_venue', 255)->nullable();
            $table->date('meeting_date')->nullable();
            $table->time('meeting_time')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'special_approved'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_special_approval')->default(false);
            $table->text('special_approval_reason')->nullable();
            $table->longText('ceo_signature')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_internal_notices');
    }
};
