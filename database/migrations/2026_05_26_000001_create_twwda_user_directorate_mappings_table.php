<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_user_directorate_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('directorate_id')->constrained('directorates')->cascadeOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->enum('role_type', ['director', 'deputy_director']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'directorate_id'], 'twwda_udm_user_directorate_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_user_directorate_mappings');
    }
};
