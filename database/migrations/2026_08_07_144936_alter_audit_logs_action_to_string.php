<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE `audit_logs` MODIFY `action` VARCHAR(50) NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `audit_logs` MODIFY `action` ENUM('INSERT','UPDATE','DELETE','LOGIN','LOGOUT') NOT NULL");
    }
};
