<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twwda_meeting_agenda_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('twwda_meetings')->cascadeOnDelete();

            // Source tracking — where did this agenda item come from?
            $table->enum('source', ['notice', 'cs_added', 'ddia_added'])->default('notice');
            $table->text('original_notice_text')->nullable(); // text from the official board notice

            // Item details
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('item_number', 50)->nullable();  // e.g. "1.0", "2.0"
            $table->unsignedInteger('display_order')->default(0);

            // Status workflow
            $table->enum('status', [
                'active',               // from notice or already approved
                'pending_approval',     // CS-added items awaiting CEO approval
                'approved',             // CEO approved a new item
                'rejected',             // CEO rejected a new item
                'pulled_down',          // hidden from board members
                'pending_pulldown',     // CS pulldown awaiting CEO approval
            ])->default('active');

            // Board paper link — each agenda item can have one board paper
            $table->foreignId('board_paper_id')->nullable()->constrained('twwda_board_papers')->nullOnDelete();

            // CEO approval for new items (CS-added)
            $table->boolean('requires_ceo_approval')->default(false);

            // Pull-down fields
            $table->text('pulldown_justification')->nullable();
            $table->foreignId('pulldown_requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pulldown_requested_at')->nullable();
            $table->foreignId('pulldown_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pulldown_approved_at')->nullable();

            // Who added / who approved
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index(['meeting_id', 'status']);
            $table->index(['meeting_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twwda_meeting_agenda_items');
    }
};
