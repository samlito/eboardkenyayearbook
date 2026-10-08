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
        Schema::create('board_paper_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_paper_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['Pending', 'Approved', 'Needs Revision']);
            $table->text('ceo_comments')->nullable();
            $table->timestamp('acted_at')->useCurrent();
            $table->foreignId('acted_by')->constrained('users'); // CEO who took the action
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
        Schema::dropIfExists('board_paper_versions');
    }
};
