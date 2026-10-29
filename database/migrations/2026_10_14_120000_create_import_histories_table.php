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
        Schema::create('import_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_source_id')->nullable()->constrained('news_sources')->nullOnDelete();
            $table->string('source_name');
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->decimal('duration_seconds', 8, 2)->nullable();
            $table->string('status', 30)->default('running'); // running, completed, failed, partial
            $table->unsignedInteger('articles_fetched')->default(0);
            $table->unsignedInteger('articles_imported')->default(0);
            $table->unsignedInteger('duplicates_skipped')->default(0);
            $table->unsignedInteger('errors_count')->default(0);
            $table->json('error_messages')->nullable();
            $table->string('trigger_type', 30)->default('manual'); // manual, scheduled, admin
            $table->timestamps();

            $table->index('started_at');
            $table->index('status');
            $table->index('news_source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_histories');
    }
};
