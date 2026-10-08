<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index();
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->string('description', 255)->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['group', 'key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};
