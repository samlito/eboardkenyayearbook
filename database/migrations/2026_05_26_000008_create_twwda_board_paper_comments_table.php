<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_board_paper_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_paper_id')->constrained('twwda_board_papers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->enum('action', ['rejected', 'resubmitted', 'comment', 'approved']);
            $table->enum('reviewer_role', ['director', 'ceo']);
            $table->integer('iteration')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_board_paper_comments');
    }
};
