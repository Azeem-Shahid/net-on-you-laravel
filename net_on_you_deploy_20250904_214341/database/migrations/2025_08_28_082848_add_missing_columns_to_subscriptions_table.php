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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('subscription_type', 50)->default('monthly')->after('plan_name');
            $table->decimal('amount', 10, 2)->default(0.00)->after('subscription_type');
            $table->text('notes')->nullable()->after('amount');
            $table->enum('status', ['active', 'inactive', 'expired', 'cancelled'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['subscription_type', 'amount', 'notes']);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active')->change();
        });
    }
};
