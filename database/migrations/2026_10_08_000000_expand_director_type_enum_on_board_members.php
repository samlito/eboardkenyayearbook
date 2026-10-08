<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE board_members MODIFY director_type ENUM('Executive', 'Non-Executive', 'Chairperson', 'Member') NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('board_members')
            ->whereIn('director_type', ['Executive', 'Non-Executive'])
            ->update(['director_type' => null]);

        DB::statement("ALTER TABLE board_members MODIFY director_type ENUM('Chairperson', 'Member') NULL");
    }
};
