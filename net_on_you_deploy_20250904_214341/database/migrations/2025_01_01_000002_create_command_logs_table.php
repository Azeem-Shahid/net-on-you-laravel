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
        Schema::create('command_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command', 191);
            $table->longText('output')->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->unsignedBigInteger('executed_by_admin_id')->nullable();
            $table->timestamp('executed_at');
            $table->text('error_message')->nullable();
            $table->integer('execution_time_ms')->nullable(); // Execution time in milliseconds
            
            $table->timestamps();
            
            $table->index(['command', 'status']);
            $table->index('executed_at');
            $table->index('executed_by_admin_id');
            
            $table->foreign('executed_by_admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('command_logs');
    }
};
