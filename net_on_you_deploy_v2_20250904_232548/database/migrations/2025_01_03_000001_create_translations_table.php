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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('language_code', 10)->index();
            $table->string('key', 191)->index();
            $table->text('value');
            $table->string('module', 50)->nullable();
            $table->unsignedBigInteger('created_by_admin_id')->index();
            $table->unsignedBigInteger('updated_by_admin_id')->index();
            $table->timestamps();
            
            // Unique constraint per language and key
            $table->unique(['language_code', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
