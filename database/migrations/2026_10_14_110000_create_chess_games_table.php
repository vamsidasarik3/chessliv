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
        Schema::create('chess_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('white')->nullable();
            $table->string('black')->nullable();
            $table->string('white_elo', 10)->nullable();
            $table->string('black_elo', 10)->nullable();
            $table->string('event')->nullable();
            $table->string('site')->nullable();
            $table->string('date', 20)->nullable();
            $table->string('round', 20)->nullable();
            $table->string('result', 15)->nullable();
            $table->string('eco', 10)->nullable();
            $table->text('starting_fen')->nullable();
            $table->mediumText('pgn')->nullable();
            $table->json('moves_san')->nullable();
            $table->string('source_url', 1000)->nullable();
            $table->timestamps();

            $table->index('article_id');
            $table->index(['white', 'black']);
            $table->index('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chess_games');
    }
};
