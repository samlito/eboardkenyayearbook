<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->foreignId('directorate_id')->nullable()->after('name')
                ->constrained('directorates')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['directorate_id']);
            $table->dropColumn('directorate_id');
        });
    }
};
