<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_board_paper_annexures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_paper_id')->constrained('twwda_board_papers')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->bigInteger('file_size')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_board_paper_annexures');
    }
};
