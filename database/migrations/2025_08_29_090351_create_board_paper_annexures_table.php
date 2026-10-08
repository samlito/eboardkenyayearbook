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
    public function up()
    {
        Schema::create('board_paper_annexures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_paper_id')->constrained('board_papers')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file'); // Excel/PDF
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_paper_annexures');
    }
};
