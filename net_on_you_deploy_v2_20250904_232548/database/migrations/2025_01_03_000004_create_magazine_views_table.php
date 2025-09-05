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
        Schema::create('magazine_views', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('magazine_id')->index(); // No foreign key constraint
            $table->bigInteger('user_id')->index(); // No foreign key constraint
            $table->enum('action', ['viewed', 'downloaded']);
            $table->string('ip_address', 45)->nullable();
            $table->string('device', 50)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magazine_views');
    }
};
