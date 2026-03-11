<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تتبع المشاهدات لكل مستخدم/جلسة — لا تُحسب إلا مرة واحدة لكل محتوى أو حلقة.
     */
    public function up(): void
    {
        Schema::create('content_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id', 255)->nullable();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->foreignId('episode_id')->nullable()->constrained('episodes')->cascadeOnDelete(); // null = فيلم
            $table->timestamp('viewed_at')->useCurrent();

            $table->index(['user_id', 'content_id', 'episode_id']);
            $table->index(['session_id', 'content_id', 'episode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_views');
    }
};
