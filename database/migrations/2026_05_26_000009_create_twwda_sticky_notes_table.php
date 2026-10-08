<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_sticky_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_paper_id')->constrained('twwda_board_papers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('page_number')->default(1);
            $table->decimal('x_position', 8, 4)->default(0);
            $table->decimal('y_position', 8, 4)->default(0);
            $table->text('content');
            $table->string('color', 20)->default('yellow');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_sticky_notes');
    }
};
