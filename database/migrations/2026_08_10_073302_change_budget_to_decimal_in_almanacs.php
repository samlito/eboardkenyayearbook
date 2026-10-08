<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE almanacs MODIFY budget DECIMAL(15,2) DEFAULT 0');
        DB::statement('ALTER TABLE almanac_schedules MODIFY budget DECIMAL(15,2) DEFAULT 0');
    }

    public function down()
    {
        DB::statement('ALTER TABLE almanacs MODIFY budget FLOAT DEFAULT 0');
        DB::statement('ALTER TABLE almanac_schedules MODIFY budget FLOAT DEFAULT 0');
    }
};
