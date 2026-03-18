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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('logged_at')->index();
            $table->string('level', 20)->index();  // INFO, SUCCESS, WARNING, ERROR, DEBUG
            $table->string('channel', 50)->index(); // api, auth, news, groq, db, scheduler, etc.
            $table->text('message');
            $table->json('context')->nullable();     // Context data (user_id, duration_ms, etc.)
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            
            // Composite index for common queries
            $table->index(['logged_at', 'level']);
            $table->index(['logged_at', 'channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};