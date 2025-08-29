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
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id')->index();
            $table->string('action', 191);
            $table->string('target_type', 50)->nullable();
            $table->bigInteger('target_id')->nullable()->index();
            $table->json('payload')->nullable();
            $table->string('ip_address', 45);
            $table->string('user_agent', 191);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
