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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('referrer_id')->nullable()->index();
            $table->string('name', 150);
            $table->string('email', 191)->unique();
            $table->string('password', 255);
            $table->string('wallet_address', 191)->nullable();
            $table->enum('role', ['user', 'admin'])->default('user')->index();
            $table->string('language', 10)->default('en');
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->index();
            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
