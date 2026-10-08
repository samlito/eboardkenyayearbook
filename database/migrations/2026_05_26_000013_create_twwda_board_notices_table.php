<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_board_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();
            $table->enum('notice_type', ['21_day', '7_day'])->default('21_day');
            $table->string('reference_number', 100)->nullable();
            $table->date('reference_date')->nullable();
            $table->text('subject')->nullable();
            $table->longText('body')->nullable();
            $table->json('addressees')->nullable();
            $table->json('agenda_items')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'sent'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->longText('ceo_signature')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('document_path', 500)->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_board_notices');
    }
};
