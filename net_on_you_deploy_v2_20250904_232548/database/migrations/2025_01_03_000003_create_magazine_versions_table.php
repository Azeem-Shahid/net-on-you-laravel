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
        Schema::create('magazine_versions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('magazine_id')->index(); // No foreign key constraint
            $table->string('file_path', 500);
            $table->string('version', 50); // v1.0, v1.1, etc.
            $table->text('notes')->nullable(); // Change notes
            $table->bigInteger('uploaded_by_admin_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magazine_versions');
    }
};
