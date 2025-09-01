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
        Schema::create('scheduled_commands', function (Blueprint $table) {
            $table->id();
            $table->string('command', 191);
            $table->string('frequency', 50)->default('manual'); // manual, daily, weekly, monthly
            $table->timestamp('next_run_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['command', 'status']);
            $table->index('next_run_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_commands');
    }
};
