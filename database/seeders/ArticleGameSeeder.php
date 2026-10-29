<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ChessGame;
use App\Services\PgnParserService;

class ArticleGameSeeder extends Seeder
{
    public function run(): void
    {
        $parser = new PgnParserService();

        // 1. Gukesh D vs Alireza Firouzja (FIDE Candidates 2024, Round 10)
        $a1 = Article::where('slug', 'gukesh-d-shocks-world-elite-with-masterclass-candidates')->first();
        if ($a1) {
            $pgn1 = <<<PGN
[Event "FIDE Candidates 2024"]
[Site "Toronto, Canada"]
[Date "2024.04.14"]
[Round "10"]
[White "Gukesh D"]
[Black "Firouzja, Alireza"]
[Result "1-0"]
[WhiteElo "2743"]
[BlackElo "2760"]
[ECO "D38"]

1. d4 Nf6 2. c4 e6 3. Nf3 d5 4. Nc3 Bb4 5. cxd5 exd5 6. Bf4 Ne4 7. Rc1 Nc6 8. Nd2 g5 9. Be3 Nxc3 10. bxc3 Ba3 11. Rb1 f5 12. g3 O-O 13. Nf3 f4 14. Bc1 Bd6 15. Rb5 Be6 16. h4 fxg3 17. Bxg5 gxf2+ 18. Kxf2 Qe8 19. Rxb7 Kh8 20. Qd2 Na5 21. Rb1 Nc4 22. Qc1 Bf5 23. Rb3 Be4 24. Bg2 Qh5 25. Rh3 Rf7 26. Kg1 Rg8 27. Kh1 h6 28. Bxh6 Rf6 29. Bg5 Rfg6 30. Rb7 Bf5 31. Rxa7 Bxh3 32. Bxh3 Rxg5 33. Nxg5 Qxh4 34. Nf7+ Kg7 35. Qg5+ Qxg5 36. Nxg5 Kf6 37. Nf3 Rb8 38. Kg2 Rb2 39. Kf2 Rc2 40. a4 Rxc3 41. a5 Ra3 42. a6 Bf4 43. Bc8 Be3+ 44. Kg2 Bf4 45. Bb7 Ne3+ 46. Kf2 Ng4+ 47. Kg2 Ne3+ 48. Kf2 Ra1 49. Ne1 Nd1+ 50. Kf3 Bd6 51. Nd3 Nc3 52. Ra8 Ra3 53. a7 Ke7 54. Bc6 1-0
PGN;
            $parsed1 = $parser->parse($pgn1);

            $a1->update([
                'player_names' => 'Gukesh D, Alireza Firouzja, Ding Liren',
                'game_white' => 'Gukesh D',
                'game_black' => 'Firouzja, Alireza',
                'game_result' => '1-0',
                'game_event' => 'FIDE Candidates 2024',
                'game_date' => '2024.04.14',
                'pgn' => $pgn1,
            ]);

            ChessGame::updateOrCreate(
                ['article_id' => $a1->id],
                [
                    'white' => 'Gukesh D',
                    'black' => 'Firouzja, Alireza',
                    'white_elo' => '2743',
                    'black_elo' => '2760',
                    'event' => 'FIDE Candidates 2024',
                    'site' => 'Toronto, Canada',
                    'date' => '2024.04.14',
                    'round' => '10',
                    'result' => '1-0',
                    'eco' => 'D38',
                    'starting_fen' => ChessGame::STANDARD_STARTING_FEN,
                    'pgn' => $pgn1,
                    'moves_san' => $parsed1['moves_san'] ?? [],
                    'source_url' => $a1->original_url,
                ]
            );
        }

        // 2. Magnus Carlsen vs Fabiano Caruana
        $a2 = Article::where('slug', 'magnus-carlsen-unleashes-stunning-novelty-freestyle-chess')->first();
        if ($a2) {
            $pgn2 = <<<PGN
[Event "Freestyle Chess G.O.A.T. Challenge 2024"]
[Site "Weissenhaus, Germany"]
[Date "2024.02.12"]
[Round "Final"]
[White "Carlsen, Magnus"]
[Black "Caruana, Fabiano"]
[Result "1-0"]
[WhiteElo "2830"]
[BlackElo "2804"]
[ECO "D43"]

1. d4 d5 2. c4 c6 3. Nf3 Nf6 4. Nc3 e6 5. Bg5 Nbd7 6. e3 Qa5 7. Nd2 Bb4 8. Qc2 O-O 9. Be2 e5 10. Bxf6 Nxf6 11. dxe5 Ne4 12. Ndxe4 dxe4 13. O-O Bxc3 14. bxc3 Qxe5 15. Rad1 Be6 16. Rd4 f5 17. Rfd1 Rae8 18. g3 Qc7 19. c5 Kh8 20. Qa4 a6 21. Bc4 Bxc4 22. Qxc4 Re7 23. Rd6 h6 24. Qd4 Kh7 25. a4 Rff7 26. h4 Qa5 27. h5 Qc7 28. Kg2 Qc8 29. Rd8 Qe6 30. Qb4 Rd7 31. R1xd7 Rxd7 32. Rb8 Qf7 33. Qb3 Qxb3 34. axb3 g6 35. g4 fxg4 36. hxg6+ Kxg6 37. Kg3 h5 38. Kf4 Rf7+ 39. Kxe4 h4 40. Rg8+ Kh5 41. Rh8+ Kg5 42. Rg8+ Kh5 1-0
PGN;
            $parsed2 = $parser->parse($pgn2);

            $a2->update([
                'player_names' => 'Magnus Carlsen, Fabiano Caruana',
                'game_white' => 'Carlsen, Magnus',
                'game_black' => 'Caruana, Fabiano',
                'game_result' => '1-0',
                'game_event' => 'Freestyle Chess G.O.A.T. Challenge 2024',
                'game_date' => '2024.02.12',
                'pgn' => $pgn2,
            ]);

            ChessGame::updateOrCreate(
                ['article_id' => $a2->id],
                [
                    'white' => 'Carlsen, Magnus',
                    'black' => 'Caruana, Fabiano',
                    'white_elo' => '2830',
                    'black_elo' => '2804',
                    'event' => 'Freestyle Chess G.O.A.T. Challenge 2024',
                    'site' => 'Weissenhaus, Germany',
                    'date' => '2024.02.12',
                    'round' => 'Final',
                    'result' => '1-0',
                    'eco' => 'D43',
                    'starting_fen' => ChessGame::STANDARD_STARTING_FEN,
                    'pgn' => $pgn2,
                    'moves_san' => $parsed2['moves_san'] ?? [],
                    'source_url' => $a2->original_url,
                ]
            );
        }

        // 3. Hikaru Nakamura vs Jan-Krzysztof Duda
        $a3 = Article::where('slug', 'hikaru-nakamura-clinches-titled-tuesday-double')->first();
        if ($a3) {
            $pgn3 = <<<PGN
[Event "Titled Tuesday Arena 2024"]
[Site "Chess.com"]
[Date "2024.05.07"]
[Round "11"]
[White "Nakamura, Hikaru"]
[Black "Duda, Jan-Krzysztof"]
[Result "1-0"]
[WhiteElo "2875"]
[BlackElo "2748"]
[ECO "B90"]

1. e4 c5 2. Nf3 d6 3. d4 cxd4 4. Nxd4 Nf6 5. Nc3 a6 6. h3 e6 7. g4 b5 8. Bg2 Bb7 9. O-O Nfd7 10. Re1 Be7 11. a4 b4 12. Nd5 exd5 13. exd5 O-O 14. Nf5 Bf6 15. Nxd6 Qc7 16. Nxb7 Qxb7 17. d6 Nc6 18. Qd5 Rac8 19. g5 Bd8 20. Be3 Bb6 21. a5 Bxe3 22. Rxe3 Qa7 23. Rae1 Nd4 24. Re7 Nxc2 25. Rd1 b3 26. Qf5 Rcd8 27. Bd5 Kh8 28. Be4 g6 29. Qf4 Kg8 30. Rd3 Qc5 31. Bd5 Kh8 32. Bxb3 Nb4 33. Rc3 Qxa5 34. Qd4+ Kg8 35. h4 Qa1+ 36. Kg2 Qxb2 37. Bxf7+ Rxf7 38. Rxf7 Kxf7 39. Rf3+ 1-0
PGN;
            $parsed3 = $parser->parse($pgn3);

            $a3->update([
                'player_names' => 'Hikaru Nakamura, Jan-Krzysztof Duda',
                'game_white' => 'Nakamura, Hikaru',
                'game_black' => 'Duda, Jan-Krzysztof',
                'game_result' => '1-0',
                'game_event' => 'Titled Tuesday Arena 2024',
                'game_date' => '2024.05.07',
                'pgn' => $pgn3,
            ]);

            ChessGame::updateOrCreate(
                ['article_id' => $a3->id],
                [
                    'white' => 'Nakamura, Hikaru',
                    'black' => 'Duda, Jan-Krzysztof',
                    'white_elo' => '2875',
                    'black_elo' => '2748',
                    'event' => 'Titled Tuesday Arena 2024',
                    'site' => 'Chess.com',
                    'date' => '2024.05.07',
                    'round' => '11',
                    'result' => '1-0',
                    'eco' => 'B90',
                    'starting_fen' => ChessGame::STANDARD_STARTING_FEN,
                    'pgn' => $pgn3,
                    'moves_san' => $parsed3['moves_san'] ?? [],
                    'source_url' => $a3->original_url,
                ]
            );
        }

        // 4. Ding Liren vs Praggnanandhaa R (Tata Steel Masters 2024)
        $a4 = Article::where('slug', 'praggnanandhaa-and-vaishali-sibling-dynasty')->first();
        if ($a4) {
            $pgn4 = <<<PGN
[Event "Tata Steel Masters 2024"]
[Site "Wijk aan Zee, Netherlands"]
[Date "2024.01.16"]
[Round "4"]
[White "Ding, Liren"]
[Black "Praggnanandhaa, R"]
[Result "0-1"]
[WhiteElo "2780"]
[BlackElo "2743"]
[ECO "D38"]

1. d4 Nf6 2. Nf3 d5 3. c4 e6 4. Nc3 Bb4 5. cxd5 exd5 6. Bg5 Nbd7 7. e3 c5 8. Bd3 Qa5 9. Qc2 c4 10. Bf5 O-O 11. O-O Re8 12. Nd2 g6 13. Bh3 Bxc3 14. bxc3 Ne4 15. Nxe4 dxe4 16. Bf4 Nb6 17. Bxc8 Raxc8 18. Rab1 Nd5 19. Rxb7 Qxc3 20. Qxc3 Nxc3 21. Bh6 a5 22. a3 Rb8 23. Rc7 Rec8 24. Rxc8+ Rxc8 25. f3 f5 26. Kf2 Rb8 27. Rc1 Rb3 28. a4 Kf7 29. h4 Ke6 30. Bg7 Kd5 31. g4 exf3 32. gxf5 Ne4+ 33. Kxf3 gxf5 34. Rg1 Rb2 35. Rg2 c3 36. Rg1 c2 37. Rc1 Nd2+ 38. Ke2 Nb3 39. Rxc2 Rxc2+ 40. Kd3 Rc7 41. Be5 Rc4 42. h5 Rxa4 43. Kc3 Nc1 0-1
PGN;
            $parsed4 = $parser->parse($pgn4);

            $a4->update([
                'player_names' => 'Rameshbabu Praggnanandhaa, Vaishali Rameshbabu, Ding Liren',
                'game_white' => 'Ding, Liren',
                'game_black' => 'Praggnanandhaa, R',
                'game_result' => '0-1',
                'game_event' => 'Tata Steel Masters 2024',
                'game_date' => '2024.01.16',
                'pgn' => $pgn4,
            ]);

            ChessGame::updateOrCreate(
                ['article_id' => $a4->id],
                [
                    'white' => 'Ding, Liren',
                    'black' => 'Praggnanandhaa, R',
                    'white_elo' => '2780',
                    'black_elo' => '2743',
                    'event' => 'Tata Steel Masters 2024',
                    'site' => 'Wijk aan Zee, Netherlands',
                    'date' => '2024.01.16',
                    'round' => '4',
                    'result' => '0-1',
                    'eco' => 'D38',
                    'starting_fen' => ChessGame::STANDARD_STARTING_FEN,
                    'pgn' => $pgn4,
                    'moves_san' => $parsed4['moves_san'] ?? [],
                    'source_url' => $a4->original_url,
                ]
            );
        }
    }
}
