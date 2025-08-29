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
        Schema::create('payout_batch_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('batch_id')->index();
            $table->bigInteger('earner_user_id')->index();
            $table->json('commission_ids'); // array of commission IDs included
            $table->decimal('amount', 18, 2);
            $table->enum('status', ['queued', 'sent', 'failed', 'paid'])->default('queued')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_batch_items');
    }
};
