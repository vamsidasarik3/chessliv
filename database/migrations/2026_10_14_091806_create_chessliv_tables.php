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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('bi-newspaper');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->longText('content');
            $table->string('author_name')->default('ChessLiv Desk');
            $table->string('author_title')->default('Senior Chess Correspondent');
            $table->string('image_url')->nullable();
            $table->string('read_time')->default('4 min read');
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_breaking')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('dates');
            $table->string('format');
            $table->string('current_leader');
            $table->string('status')->default('ongoing');
            $table->timestamps();
        });

        Schema::create('puzzles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fen');
            $table->string('turn')->default('white');
            $table->string('solution');
            $table->unsignedInteger('rating')->default(1800);
            $table->string('hint')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('puzzles');
        Schema::dropIfExists('tournaments');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('categories');
    }
};
