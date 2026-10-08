<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('twwda_board_notices', function (Blueprint $table) {
            // Signatory details (CEO for CS notices, DDIA for audit notices)
            $table->string('signatory_name', 255)->nullable()->after('ceo_signature');
            $table->string('signatory_title', 255)->nullable()->after('signatory_name');

            // Closing and CC
            $table->string('closing_text', 500)->nullable()->after('agenda_items');
            $table->json('cc')->nullable()->after('closing_text');

            // Reminder tracking
            $table->timestamp('reminder_sent_at')->nullable()->after('sent_by');
            $table->unsignedInteger('reminder_count')->default(0)->after('reminder_sent_at');

            // Rescheduled notice chain
            $table->unsignedBigInteger('parent_notice_id')->nullable()->after('version');
            $table->foreign('parent_notice_id')->references('id')->on('twwda_board_notices')->nullOnDelete();

            // Workflow routing: false = DDIA self-approves (audit committee)
            $table->boolean('requires_ceo_approval')->default(true)->after('parent_notice_id');
        });

        // Add notice_date only if it doesn't exist
        if (!Schema::hasColumn('twwda_board_notices', 'notice_date')) {
            Schema::table('twwda_board_notices', function (Blueprint $table) {
                $table->date('notice_date')->nullable()->after('reference_date');
            });
        }
    }

    public function down(): void
    {
        Schema::table('twwda_board_notices', function (Blueprint $table) {
            $table->dropForeign(['parent_notice_id']);
            $table->dropColumn([
                'signatory_name', 'signatory_title', 'closing_text', 'cc',
                'reminder_sent_at', 'reminder_count', 'parent_notice_id',
                'requires_ceo_approval',
            ]);
        });
        if (Schema::hasColumn('twwda_board_notices', 'notice_date')) {
            Schema::table('twwda_board_notices', function (Blueprint $table) {
                $table->dropColumn('notice_date');
            });
        }
    }
};
