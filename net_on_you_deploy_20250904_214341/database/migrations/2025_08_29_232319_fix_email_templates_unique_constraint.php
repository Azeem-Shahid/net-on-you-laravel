<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            // Check if the unique constraint on name exists before dropping
            $indexes = DB::select("SHOW INDEX FROM email_templates WHERE Key_name = 'email_templates_name_unique'");
            if (!empty($indexes)) {
                $table->dropUnique(['name']);
            }
            
            // Check if the composite unique constraint already exists
            $compositeIndexes = DB::select("SHOW INDEX FROM email_templates WHERE Key_name = 'email_templates_name_language_unique'");
            if (empty($compositeIndexes)) {
                $table->unique(['name', 'language'], 'email_templates_name_language_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            // Check if the composite unique constraint exists before dropping
            $compositeIndexes = DB::select("SHOW INDEX FROM email_templates WHERE Key_name = 'email_templates_name_language_unique'");
            if (!empty($compositeIndexes)) {
                $table->dropUnique('email_templates_name_language_unique');
            }
            
            // Restore the unique constraint on name only
            $indexes = DB::select("SHOW INDEX FROM email_templates WHERE Key_name = 'email_templates_name_unique'");
            if (empty($indexes)) {
                $table->unique(['name']);
            }
        });
    }
};
