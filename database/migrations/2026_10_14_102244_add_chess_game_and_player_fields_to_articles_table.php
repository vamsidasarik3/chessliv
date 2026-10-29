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
        Schema::table('articles', function (Blueprint $table) {
            $table->text('pgn')->nullable()->after('content');
            $table->string('game_white')->nullable()->after('pgn');
            $table->string('game_black')->nullable()->after('game_white');
            $table->string('game_result', 10)->nullable()->after('game_black');
            $table->string('game_event')->nullable()->after('game_result');
            $table->string('game_date')->nullable()->after('game_event');
            $table->string('player_names')->nullable()->index()->after('author_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'pgn',
                'game_white',
                'game_black',
                'game_result',
                'game_event',
                'game_date',
                'player_names',
            ]);
        });
    }
};
