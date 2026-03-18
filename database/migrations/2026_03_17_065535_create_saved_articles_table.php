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
        Schema::create('saved_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('news_title');
            $table->string('news_url');
            $table->string('news_source');
            $table->text('news_description')->nullable();
            $table->string('news_image')->nullable();
            $table->timestamp('news_published')->nullable();
            $table->enum('type', ['save', 'favorite'])->default('save');
            $table->timestamps();
            
            $table->unique(['user_id', 'news_url']);
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_articles');
    }
};
