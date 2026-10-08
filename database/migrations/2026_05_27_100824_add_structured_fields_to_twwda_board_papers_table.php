<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('twwda_board_papers', function (Blueprint $table) {
            $table->string('committee_name', 255)->nullable()->after('title');
            $table->string('paper_type', 500)->nullable()->after('committee_name');
            $table->string('financial_year', 20)->nullable()->after('paper_type');
            $table->string('reporting_period', 255)->nullable()->after('financial_year');
            $table->string('author_department', 255)->nullable()->after('reporting_period');
        });
    }

    public function down(): void
    {
        Schema::table('twwda_board_papers', function (Blueprint $table) {
            $table->dropColumn([
                'committee_name',
                'paper_type',
                'financial_year',
                'reporting_period',
                'author_department',
            ]);
        });
    }
};
