<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand the status ENUM to include all values used by the application.
     *
     * Original: draft, submitted, director_review, director_approved, director_rejected, ceo_review, ceo_approved, ceo_rejected
     * Added:    rejected_by_director, rejected_by_ceo, removed_from_agenda, under_review, approved
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE twwda_board_papers MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'director_review',
            'director_approved',
            'director_rejected',
            'rejected_by_director',
            'ceo_review',
            'under_review',
            'ceo_approved',
            'ceo_rejected',
            'rejected_by_ceo',
            'removed_from_agenda',
            'approved'
        ) DEFAULT 'draft'");
    }

    /**
     * Revert to original ENUM values.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE twwda_board_papers MODIFY COLUMN status ENUM(
            'draft', 'submitted', 'director_review', 'director_approved',
            'director_rejected', 'ceo_review', 'ceo_approved', 'ceo_rejected'
        ) DEFAULT 'draft'");
    }
};
