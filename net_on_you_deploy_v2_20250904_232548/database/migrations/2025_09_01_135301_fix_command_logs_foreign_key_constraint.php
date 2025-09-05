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
        Schema::table('command_logs', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['executed_by_admin_id']);
            
            // Add the correct foreign key constraint referencing admins table
            $table->foreign('executed_by_admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('command_logs', function (Blueprint $table) {
            // Drop the correct foreign key constraint
            $table->dropForeign(['executed_by_admin_id']);
            
            // Restore the original foreign key constraint referencing users table
            $table->foreign('executed_by_admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
