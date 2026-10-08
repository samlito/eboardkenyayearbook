<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_conflict_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();
            $table->foreignId('board_member_id')->constrained('board_members')->cascadeOnDelete();
            $table->boolean('has_conflict')->default(false);
            $table->text('conflict_details')->nullable();
            $table->string('person_name', 255)->nullable();
            $table->text('person_address')->nullable();
            $table->text('interest_nature')->nullable();
            $table->string('interest_value', 100)->nullable();
            $table->date('interest_ceased_on')->nullable();
            $table->text('remarks')->nullable();
            $table->string('otp_code', 10)->nullable();
            $table->timestamp('otp_sent_at')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->longText('signature')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['meeting_id', 'board_member_id'], 'twwda_cd_meeting_member_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_conflict_declarations');
    }
};
