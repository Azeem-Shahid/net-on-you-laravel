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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('earner_user_id')->index();
            $table->bigInteger('source_user_id')->index();
            $table->bigInteger('transaction_id')->index();
            $table->tinyInteger('level')->index();
            $table->decimal('amount', 18, 2);
            $table->char('month', 7)->index();
            $table->enum('eligibility', ['eligible', 'ineligible'])->default('eligible');
            $table->enum('payout_status', ['pending', 'paid', 'void'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};

