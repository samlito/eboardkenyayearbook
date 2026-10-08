<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('board_paper_versions', function (Blueprint $table) {
            $table->string('file_path')->after('acted_by');
            $table->string('supporting_docs_path')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('board_paper_versions', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'supporting_docs_path']);
        });
    }
};
