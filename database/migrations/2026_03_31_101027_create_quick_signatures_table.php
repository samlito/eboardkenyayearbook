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
    public function up(): void
    {
        Schema::create('quick_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('document_title');
            $table->string('prepared_by');
            $table->text('notes')->nullable();
            $table->text('signature_data'); // base64 png from canvas
            $table->string('signer_name');
            $table->foreignId('signed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_signatures');
    }
};
