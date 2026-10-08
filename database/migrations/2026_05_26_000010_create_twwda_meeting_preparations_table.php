<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_meeting_preparations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();
            $table->foreignId('prepared_by')->constrained('users')->cascadeOnDelete();
            $table->json('paper_order')->nullable();
            $table->json('additional_agenda_items')->nullable();
            $table->json('removed_papers')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_meeting_preparations');
    }
};
