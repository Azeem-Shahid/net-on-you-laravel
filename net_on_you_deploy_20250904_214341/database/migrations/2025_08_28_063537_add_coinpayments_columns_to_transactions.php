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
        Schema::table('transactions', function (Blueprint $table) {
            // Add CoinPayments specific columns
            if (!Schema::hasColumn('transactions', 'txn_id')) {
                $table->string('txn_id')->nullable()->index()->after('id');
            }
            if (!Schema::hasColumn('transactions', 'target_currency')) {
                $table->string('target_currency')->nullable()->index()->after('currency');
            }
            if (!Schema::hasColumn('transactions', 'received_amount')) {
                $table->decimal('received_amount', 18, 8)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('transactions', 'confirmations')) {
                $table->unsignedInteger('confirmations')->default(0)->after('status');
            }
            if (!Schema::hasColumn('transactions', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Remove CoinPayments specific columns
            $table->dropColumn([
                'txn_id',
                'target_currency', 
                'received_amount',
                'confirmations',
                'processed_at'
            ]);
        });
    }
};
