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
        // 1. الأقسام الرئيسية (Sections)
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index('section_name_index');
            $table->string('slug')->unique()->nullable();
            $table->string('poster_url')->nullable();
            $table->integer('order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('type')->nullable(); // movie, series, book, move_series
            $table->timestamps();
        });

        // 2. الأقسام الفرعية (SubSections)
        Schema::create('sub_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->string('name')->index();
            $table->string('slug')->unique()->nullable();
            $table->string('type')->nullable();
            $table->integer('order')->default(0);
            $table->string('poster_url')->nullable();
            $table->timestamps();
        });

        // 3. الأنواع الفنية (Genres)
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index('genre_name_index');
            $table->timestamps();
        });

        // 4. المحتوى (Contents)
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index('content_title_index');
            $table->boolean('is_featured')->default(false);
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('language')->nullable();
            $table->year('release_year')->nullable();
            $table->integer('duration')->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->default(0);
            $table->string('poster_url')->nullable();
            $table->string('file_url')->nullable();
            $table->enum('access_level_required', ['free', 'basic', 'pro', 'premium'])->default('basic');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sub_section_id')->nullable()->constrained('sub_sections')->nullOnDelete();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });

        // 4. جدول الوسيط لربط المحتوى بالأنواع (ContentGenres)
        Schema::create('content_genres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->timestamps();
        });

        // 5. المواسم (Seasons) : المواسم الموجودة في المسلسل
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->string('season'); // الموسم الأول، الثاني، إلخ.
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('poster_url')->nullable();
            $table->timestamps();
        });

        // 6. الحلقات (Episodes): الحلقات الموجودة في الموسم
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->onDelete('cascade');
            $table->integer('episode_number');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('duration')->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->default(0);
            $table->string('poster_url')->nullable();
            $table->string('file_url')->nullable();
            $table->unsignedBigInteger('views_count')->default(0); // عدد المشاهدات
            $table->timestamps();
        });

        // 7. الخطط (Plans) : الخطط المتاحة للمستخدم
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('access_level', ['free', 'basic', 'pro', 'premium'])->default('basic');
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable()->unique();
            $table->string('currency', 3)->nullable()->default('usd');
            $table->string('interval', 20)->nullable();
            $table->unsignedSmallInteger('interval_count')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 8. المدفوعات (Payments)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans');
            $table->decimal('amount', 8, 2)->default(0); // المبلغ المدفوع
            $table->string('payment_method'); // طريقة الدفع مثل PayPal، بطاقة ائتمان، إلخ.
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('transaction_id')->unique();
            $table->timestamp('paid_at');
            $table->timestamps();
        });

        // 9. المفضلة (Favorites)
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->foreignId('episode_id')->nullable()->constrained('episodes')->nullOnDelete();
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();
        });

        // 10. التقييمات (Ratings)
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // من 1 لـ 5
            $table->text('review')->nullable(); // مراجعة نصية اختيارية
            $table->timestamp('rated_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف الجداول بترتيب معكوس للعلاقات
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('episodes');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('content_genres');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('sub_sections');
        Schema::dropIfExists('sections');
    }
};
