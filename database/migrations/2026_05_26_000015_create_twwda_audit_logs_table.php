<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 100);
            $table->string('module', 100)->nullable();
            $table->string('entity_type', 100)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->foreignId('meeting_id')->nullable()->constrained('twwda_meetings')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['entity_type', 'entity_id']);
            $table->index('module');
            $table->index('meeting_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_audit_logs');
    }
};
