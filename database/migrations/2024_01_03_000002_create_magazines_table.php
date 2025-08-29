<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('magazines', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191); // Reduced for multi-language support
            $table->text('description')->nullable();
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->bigInteger('file_size');
            $table->string('mime_type', 100);
            $table->string('cover_image_path', 255)->nullable(); // Cover image for preview
            $table->string('category', 100)->nullable(); // Magazine category
            $table->string('language_code', 10)->default('en')->index(); // Language support
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->bigInteger('uploaded_by_admin_id')->index(); // Renamed for clarity
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magazines');
    }
};
