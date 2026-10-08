<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('board_committees', function (Blueprint $table) {
            $table->boolean('is_audit_committee')->default(false)->after('description');
        });
    }

    public function down()
    {
        Schema::table('board_committees', function (Blueprint $table) {
            $table->dropColumn('is_audit_committee');
        });
    }
};
