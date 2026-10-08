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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('staff_number')->nullable()->after('password');
            $table->enum('status', ['active', 'inactive', 'suspended', 'deleted','disabled'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable()->after('staff_number');
            $table->dateTime('last_login_at')->nullable()->after('updated_at');
            $table->string('login_ip')->nullable()->after('last_login_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
