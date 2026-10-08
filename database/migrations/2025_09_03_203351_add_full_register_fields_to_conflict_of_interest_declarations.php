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
        Schema::table('conflict_of_interest_declarations', function (Blueprint $table) {
            $table->string('person_name')->nullable()->after('conflict_status');   // NAME OF PERSON / ISSUE
            $table->string('person_address')->nullable()->after('person_name');   // ADDRESS
            $table->string('interest_nature')->nullable()->after('person_address'); // NATURE OF INTEREST
            $table->decimal('interest_value', 15, 2)->nullable()->after('interest_nature'); // ESTIMATED VALUE (KSHS)
            $table->date('interest_ceased_on')->nullable()->after('interest_value'); // DATE CEASED
            $table->text('remarks')->nullable()->after('interest_ceased_on'); // REMARKS / SIGNATURE
            $table->string('otp_code')->nullable()->after('remarks'); // OTP
            $table->timestamp('otp_verified_at')->nullable()->after('otp_code'); // OTP VERIFIED
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conflict_of_interest_declarations', function (Blueprint $table) {
            $table->dropColumn([
                'person_name',
                'person_address',
                'interest_nature',
                'interest_value',
                'interest_ceased_on',
                'remarks',
                'otp_code',
                'otp_verified_at',
            ]);
        });
    }
};
