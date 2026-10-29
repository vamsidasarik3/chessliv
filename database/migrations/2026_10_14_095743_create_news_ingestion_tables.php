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
        // 1. News Sources Table
        Schema::create('news_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('feed_url');
            $table->string('site_url')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('default_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('language', 10)->default('en');
            $table->boolean('is_active')->default(true);
            $table->integer('fetch_frequency_minutes')->default(60);
            $table->timestamp('last_fetched_at')->nullable();
            $table->text('last_error')->nullable();
            $table->unsignedInteger('items_fetched_count')->default(0);
            $table->timestamps();
        });

        // 2. Add ingestion fields to articles table
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('news_source_id')->nullable()->after('category_id')->constrained('news_sources')->nullOnDelete();
            $table->string('source_name')->nullable()->after('author_title');
            $table->text('original_url')->nullable()->after('content');
            $table->string('canonical_url', 500)->nullable()->index()->after('original_url');
            $table->string('content_hash', 64)->nullable()->index()->after('canonical_url');
            $table->string('language', 10)->default('en')->after('content_hash');
            $table->string('processing_status', 30)->default('processed')->index()->after('language');
            $table->timestamp('fetched_at')->nullable()->after('published_at');
        });

        // 3. Article Category Pivot Table (many-to-many relationship)
        Schema::create('article_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['article_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_category');

        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['news_source_id']);
            $table->dropColumn([
                'news_source_id',
                'source_name',
                'original_url',
                'canonical_url',
                'content_hash',
                'language',
                'processing_status',
                'fetched_at',
            ]);
        });

        Schema::dropIfExists('news_sources');
    }
};
