<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE twwda_officer_assignments MODIFY COLUMN status ENUM('assigned', 'pending', 'notified', 'read', 'paper_submitted', 'paper_approved', 'paper_rejected') DEFAULT 'assigned'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE twwda_officer_assignments MODIFY COLUMN status ENUM('pending', 'notified', 'read', 'paper_submitted', 'paper_approved', 'paper_rejected') DEFAULT 'pending'");
    }
};
