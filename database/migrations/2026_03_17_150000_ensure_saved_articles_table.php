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
        // Check if table exists, if not create it
        if (!Schema::hasTable('saved_articles')) {
            Schema::create('saved_articles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('news_title');
                $table->text('news_description')->nullable();
                $table->string('news_source')->nullable();
                $table->text('news_url');
                $table->text('news_image')->nullable();
                $table->enum('type', ['article', 'summary', 'favorite', 'bookmark'])->default('article');
                $table->timestamps();
                
                // Add unique constraint to prevent duplicates
                $table->unique(['user_id', 'news_url']);
            });
        } else {
            // Table exists, add missing columns if needed
            Schema::table('saved_articles', function (Blueprint $table) {
                if (!Schema::hasColumn('saved_articles', 'news_image')) {
                    $table->text('news_image')->nullable()->after('news_url');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_articles');
    }
};