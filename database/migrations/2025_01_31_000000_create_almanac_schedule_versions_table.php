<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('almanac_schedule_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('almanac_schedule_id');
            $table->unsignedInteger('version_number')->default(1);
            $table->unsignedBigInteger('almanac_id')->nullable();
            $table->unsignedBigInteger('meeting_id')->nullable();
            $table->unsignedBigInteger('board_committee_id')->nullable();
            $table->date('meeting_date')->nullable();
            $table->text('purpose')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->string('status')->nullable();
            $table->text('change_reason')->nullable();
            $table->timestamps();

            $table->foreign('almanac_schedule_id')->references('id')->on('almanac_schedules')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('almanac_schedule_versions');
    }
};
