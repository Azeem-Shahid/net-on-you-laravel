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
        Schema::create('payout_batches', function (Blueprint $table) {
            $table->id();
            $table->char('period', 7)->unique()->index(); // "YYYY-MM" for the commissions being paid
            $table->enum('status', ['open', 'processing', 'closed'])->default('open')->index();
            $table->decimal('total_amount', 18, 2);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by_admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_batches');
    }
};
