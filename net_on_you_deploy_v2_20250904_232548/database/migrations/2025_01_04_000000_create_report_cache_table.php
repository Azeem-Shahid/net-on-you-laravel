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
        Schema::create('report_cache', function (Blueprint $table) {
            $table->id();
            $table->string('report_name', 100);
            $table->json('filters');
            $table->json('data_snapshot');
            $table->timestamp('generated_at');
            $table->unsignedBigInteger('created_by_admin_id');
            $table->timestamps();
            
            $table->index('created_by_admin_id');
            $table->index(['report_name', 'generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_cache');
    }
};
