<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('county', function (Blueprint $table) {
            $table->id();
            $table->integer('county_code')->unique();
            $table->string('county_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('county');
    }
};
