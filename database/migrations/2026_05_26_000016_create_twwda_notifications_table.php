<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('message')->nullable();
            $table->string('type', 100)->nullable();
            $table->string('link', 500)->nullable();
            $table->foreignId('meeting_id')->nullable()->constrained('twwda_meetings')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_notifications');
    }
};
