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
        // For MySQL, we need to use raw SQL to change column size
        Schema::table('saved_articles', function (Blueprint $table) {
            // Change the 'type' column from CHAR or ENUM to VARCHAR(50)
            DB::statement('ALTER TABLE saved_articles MODIFY type VARCHAR(50)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_articles', function (Blueprint $table) {
            // You can revert if needed
            DB::statement('ALTER TABLE saved_articles MODIFY type VARCHAR(255)');
        });
    }
};