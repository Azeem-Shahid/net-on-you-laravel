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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // unique template name
            $table->string('language', 10)->default('en'); // en, ur, fr, etc.
            $table->string('subject', 191);
            $table->text('body'); // support HTML/markdown
            $table->json('variables'); // list of allowed placeholders
            $table->unsignedBigInteger('created_by_admin_id')->index();
            $table->unsignedBigInteger('updated_by_admin_id')->index();
            $table->timestamps();
            
            // Composite unique constraint for name + language
            $table->unique(['name', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
