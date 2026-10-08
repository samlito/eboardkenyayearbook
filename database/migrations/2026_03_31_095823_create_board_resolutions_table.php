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
    public function up(): void
    {
        Schema::create('board_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_meeting_id')->constrained('board_meetings')->onDelete('cascade');
            $table->text('resolution');
            $table->foreignId('assigned_officer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'overdue'])->default('pending');
            $table->date('due_date')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_resolutions');
    }
};
