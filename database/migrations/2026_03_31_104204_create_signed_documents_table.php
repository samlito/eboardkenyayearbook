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
        Schema::create('signed_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('original_pdf');       // path to uploaded PDF
            $table->string('signed_pdf')->nullable(); // path to final signed PDF
            $table->text('signature_data')->nullable(); // base64 signature image
            $table->integer('signature_page')->nullable();
            $table->float('signature_x')->nullable();
            $table->float('signature_y')->nullable();
            $table->enum('status', ['pending', 'signed'])->default('pending');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('signed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signed_documents');
    }
};
