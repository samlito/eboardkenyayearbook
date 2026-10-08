<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('mdi-folder-outline');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('repository_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('document_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->enum('visibility', ['all', 'committee', 'specific'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('repository_document_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('repository_documents')->onDelete('cascade');
            $table->foreignId('board_committee_id')->nullable()->constrained('board_committees')->onDelete('cascade');
            $table->foreignId('board_member_id')->nullable()->constrained('board_members')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('repository_document_access');
        Schema::dropIfExists('repository_documents');
        Schema::dropIfExists('document_categories');
    }
};
