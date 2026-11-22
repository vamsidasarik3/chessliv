<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Article;
use App\Models\ChessGame;
use App\Services\PgnParserService;

class TwentyMoreArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $parser = new PgnParserService();

        // Ensure category dictionary is resolved
        $categories = [
            'world-championship' => Category::where('slug', 'world-championship')->first(),
            'tournaments' => Category::where('slug', 'tournaments')->first(),
            'gm-insights' => Category::where('slug', 'gm-insights')->first(),
            'opening-theory' => Category::where('slug', 'opening-theory')->first(),
            'player-profiles' => Category::where('slug', 'player-profiles')->first(),
            'tactics-puzzles' => Category::where('slug', 'tactics-puzzles')->first(),
        ];

        // Fallback: create if missing
        foreach ($categories as $slug => $cat) {
            if (!$cat) {
                $categories[$slug] = Category::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => ucwords(str_replace('-', ' ', $slug)),
                        'slug' => $slug,
                        'description' => 'Chess analysis, coverage and news in ' . str_replace('-', ' ', $slug),
                        'icon' => 'bi-newspaper',
                        'order' => 10,
                        'is_active' => true,
                    ]
                );
            }
        }

        $articles = [
            // 1. World Championship: Ding Liren vs Gukesh D Match Preview
            [
                'category_slug' => 'world-championship',
                'title' => 'Ding Liren vs Gukesh D: Comprehensive Classical Match Preview and Psychological Dynamics',
                'slug' => 'ding-liren-vs-gukesh-d-classical-match-preview',
                'summary' => 'An exhaustive preview analyzing preparation repertoires, time management tendencies, and psychological grit ahead of the world title showdown in Singapore.',
                'content' => "<p class='lead'>As the world chess community converges on Singapore, the upcoming World Chess Championship match between defending champion Ding Liren and 18-year-old challenger Gukesh D promises one of the most intriguing generational clashes in modern history.</p>
<p>Ding Liren's journey since claiming the crown in Astana has been characterized by introspection, health battles, and intermittent tournament appearances. In contrast, Gukesh's meteoric ascent—crowned by his sensational triumph at the Toronto Candidates and gold-medal performance at the Budapest Olympiad—has infused the chess world with unprecedented dynamism.</p>
<h3>Opening Repertoire Clash</h3>
<p>Analysts anticipate fierce skirmishes in classical 1.d4 structures. While Gukesh has honed a nearly impenetrable Catalan and Queen's Gambit Declined setup with Black, Ding's mastery of the English Opening and dynamic double-edged positions could prove to be the critical equalizer.</p>
<blockquote>'Matches of this magnitude are not decided by opening depth alone; they are won in the 4th and 5th hours of play when physical fatigue and psychological pressure collide.' — GM Viswanathan Anand</blockquote>
<p>Both camps have remained notoriously tight-lipped regarding their team of seconds, though speculation abounds that multiple former world championship contenders have joined both preparation suites.</p>",
                'author_name' => 'GM Viswanathan Anand',
                'author_title' => 'Five-Time World Champion',
                'image_url' => 'https://images.unsplash.com/photo-1529699211952-734e80c4d42b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'views_count' => 2850,
                'is_featured' => true,
                'is_breaking' => true,
                'published_at' => now()->subHours(3),
                'player_names' => 'Gukesh D, Ding Liren',
                'game_white' => 'Ding Liren',
                'game_black' => 'Gukesh D',
                'game_result' => '0-1',
                'game_event' => 'Tata Steel Masters',
                'game_date' => '2024.01.16',
                'pgn' => <<<PGN
[Event "Tata Steel Masters"]
[Site "Wijk aan Zee, Netherlands"]
[Date "2024.01.16"]
[Round "4"]
[White "Ding, Liren"]
[Black "Gukesh, D"]
[Result "0-1"]
[WhiteElo "2780"]
[BlackElo "2725"]
[ECO "C53"]

1. e4 e5 2. Nf3 Nc6 3. Bc4 Bc5 4. c3 Nf6 5. d3 d6 6. O-O a5 7. Re1 Ba7 8. Nbd2 O-O 9. h3 Be6 10. Bb5 Ne7 11. d4 Ng6 12. Nf1 d5 13. exd5 Qxd5 14. c4 Qd8 15. dxe5 Qxd1 16. Rxd1 Ne4 17. Be3 Bxe3 18. Nxe3 c6 19. Ba4 Nc5 20. Bc2 Nd7 21. Bxg6 hxg6 22. Rd6 Rfe8 23. Rad1 Nc5 24. Ng5 Bc8 25. f4 f6 26. Nf3 Be6 27. exf6 gxf6 28. f5 Bxf5 29. Nxf5 gxf5 30. Rxf6 Rad8 31. Rxd8 Rxd8 32. Rxf5 Rd1+ 33. Kh2 b6 34. Rf6 Rb1 35. b3 Rb2 36. Rxc6 Rxa2 37. Rxb6 Rb2 38. Nd4 a4 39. b4 a3 40. Rb8+ Kf7 41. Ra8 Rxb4 42. Rxa3 Rxc4 43. Nf3 Ne4 44. Ra6 Rc2 45. h4 Nf6 46. Kh3 Rc3 47. Ra5 Kg6 48. Rg5+ Kh6 49. Rf5 Kg6 50. Rf4 Nh5 51. Ra4 Nf6 52. Ra6 Kg7 53. Ra4 Kg6 54. Kh2 Re3 55. Ra5 Ng4+ 56. Kg1 Nf6 57. Kf2 Ng4+ 58. Kg3 Nf6 59. Re5 Ra3 60. Kf2 Ng4+ 61. Kg3 Nxe5 0-1
PGN,
            ],

            // 2. Tournaments: Arjun Erigaisi Crosses 2800 Live Rating
            [
                'category_slug' => 'tournaments',
                'title' => 'Arjun Erigaisi Crosses 2800 FIDE Rating Milestone with Flawless European Campaign',
                'slug' => 'arjun-erigaisi-crosses-2800-rating-milestone',
                'summary' => 'India\'s dynamic prodigy becomes only the second Indian in chess history after Viswanathan Anand to conquer the mythical 2800 live Elo barrier.',
                'content' => "<p class='lead'>In an era where classical chess ratings have suffered severe deflation across the globe, 21-year-old Indian Grandmaster Arjun Erigaisi has accomplished what many thought nearly impossible in open Swiss tournaments: crossing the legendary 2800 Elo threshold.</p>
<p>Arjun's uncompromising, maximalist approach to the game has ignited tournaments across Europe and Asia. Unlike many of his elite peers who rely on solid, risk-free opening draws against 2600-rated opposition, Arjun consistently plays for a win with both colors, creating sharp imbalance from the opening moves.</p>
<h3>The Unstoppable Streak</h3>
<p>Over a span of 11 months, Arjun compiled a tournament win rate exceeding 72%, defeating grandmasters with startling tactical accuracy. His rating leap vaults him into the historic stratosphere alongside Garry Kasparov, Magnus Carlsen, and Vladimir Kramnik.</p>
<p>'Arjun possesses the hunger of the classical romantic players combined with engine-precision calculation,' noted commentator GM Peter Leko. 'He does not look at rating risks; he only looks for the best move.'</p>",
                'author_name' => 'IM Sagar Shah',
                'author_title' => 'Editor-in-Chief, ChessBase India',
                'image_url' => 'https://images.unsplash.com/photo-1586165368502-1bad197a6461?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '5 min read',
                'views_count' => 2410,
                'is_featured' => true,
                'is_breaking' => false,
                'published_at' => now()->subHours(8),
                'player_names' => 'Arjun Erigaisi, Richard Rapport',
                'game_white' => 'Arjun Erigaisi',
                'game_black' => 'Richard Rapport',
                'game_result' => '1-0',
                'game_event' => 'Bundesliga 2024',
                'game_date' => '2024.03.24',
                'pgn' => <<<PGN
[Event "Bundesliga 2024"]
[Site "Germany"]
[Date "2024.03.24"]
[Round "11"]
[White "Erigaisi, Arjun"]
[Black "Rapport, Richard"]
[Result "1-0"]
[WhiteElo "2775"]
[BlackElo "2720"]
[ECO "C02"]

1. e4 e6 2. d4 d5 3. e5 c5 4. c3 Nc6 5. Nf3 Qb6 6. a3 Nh6 7. b4 cxd4 8. cxd4 Nf5 9. Be3 Bd7 10. Bd3 Nxe3 11. fxe3 Be7 12. O-O O-O 13. Nc3 f5 14. exf6 Bxf6 15. Na4 Qd8 16. Nc5 b6 17. Nxd7 Qxd7 18. Rc1 Qd6 19. Qc2 Ne7 20. Bxh7+ Kh8 21. Bd3 Rac8 22. Qe2 g6 23. Rxc8 Rxc8 24. Ne5 Bxe5 25. dxe5 Qxe5 26. Qf3 Nf5 27. Qh3+ Kg7 28. Bxf5 exf5 29. Qf3 Rc3 30. Rd1 Rxe3 31. Qxd5 Re1+ 32. Kf2 Qe3# 1-0
PGN,
            ],

            // 3. GM Insights: The Art of the Exchange Sacrifice
            [
                'category_slug' => 'gm-insights',
                'title' => 'The Art of the Exchange Sacrifice: Modern Lessons from Petrosian to Topalov',
                'slug' => 'art-of-the-exchange-sacrifice-modern-lessons',
                'summary' => 'Why giving up a rook for a minor piece remains one of the most sublime and psychologically paralyzing weapons in master chess.',
                'content' => "<p class='lead'>There is no decision in chess more aesthetically satisfying or strategically profound than the positional exchange sacrifice—voluntarily relinquishing a rook for an enemy bishop or knight without an immediate tactical regain.</p>
<p>Popularized by the legendary 9th World Champion Tigran Petrosian, the exchange sacrifice is not a speculative gambit; it is a long-term investment. By eliminating a crucial defender or anchoring an unassailable outpost on a centralized square, the sacrificer seizes complete control over the board's dynamic flow.</p>
<h3>When to Consider an Exchange Sacrifice</h3>
<ul>
<li><strong>Dominating a colored complex:</strong> When the sacrifice grants you uncontested mastery over dark or light squares.</li>
<li><strong>Creating fortress outposts:</strong> An octopus knight on d5 or e4 often neutralizes two rooks combined.</li>
<li><strong>Paralyzing pawn structures:</strong> Rupturing the enemy pawn skeleton while keeping yours compact and coordinated.</li>
</ul>
<p>Modern engines, once biased heavily toward absolute material values, have increasingly validated classical intuition: dynamic piece harmony regularly trumps numerical point counts.</p>",
                'author_name' => 'GM Vladimir Kramnik',
                'author_title' => 'Classical World Champion',
                'image_url' => 'https://images.unsplash.com/photo-1560174038-da43ac74f01b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '7 min read',
                'views_count' => 1930,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDay(),
            ],

            // 4. Opening Theory: Refuting the Jobava London
            [
                'category_slug' => 'opening-theory',
                'title' => 'Refuting the Jobava London: New Black Counters with Early ...c5 and ...Qb6',
                'slug' => 'refuting-the-jobava-london-black-counters',
                'summary' => 'Grandmaster opening analysis debunking popular club setups in the 1.d4 Nc3 2.Bf4 complex with energetic central breaks and early queenside harassment.',
                'content' => "<p class='lead'>Over the past five years, the Jobava London System (1.d4 d5 2.Nc3 Nf6 3.Bf4) exploded in popularity across online blitz arenas and weekend club opens. Promoted heavily by streamers and aggressive attackers, it offered White quick development and insidious Nb5 threats.</p>
<p>However, recent super-GM encounters have exposed critical structural frailties in White's setup. By delaying kingside castling and striking immediately with 3...c5!, Black exploits the fact that White's c-pawn is blocked by the knight on c3.</p>
<h3>The Key Repertoire Blueprint</h3>
<ol>
<li><strong>1.d4 d5 2.Nc3 Nf6 3.Bf4 c5! 4.e3 cxd4 5.exd4 a6!</strong> — Crucially denying the b5-square to White's knight and light-squared bishop.</li>
<li><strong>6.Nf3 Bg4!</strong> — Pinning the f3 knight and reducing White's central leverage.</li>
<li><strong>7.h3 Bxf3 8.Qxf3 Nc6</strong> — Exerting direct, irreversible pressure on the backward d4 pawn.</li>
</ol>
<p>Tournament practice has shown Black securing comfortable equality and frequent opening initiative in over 64% of recorded grandmaster games with this approach.</p>",
                'author_name' => 'IM David Howell',
                'author_title' => 'Senior Opening Theorist',
                'image_url' => 'https://images.unsplash.com/photo-1528819622765-d6bcf132f793?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'views_count' => 1650,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(2),
            ],

            // 5. Player Profiles: Nodirbek Abdusattorov
            [
                'category_slug' => 'player-profiles',
                'title' => 'Nodirbek Abdusattorov: The Cold-Blooded Uzbek Phenom Aiming for the World Crown',
                'slug' => 'nodirbek-abdusattorov-uzbek-phenom-profile',
                'summary' => 'From winning the World Rapid Championship at age 17 to dominating classical super-tournaments, the meteoric rise of Uzbekistan\'s board one.',
                'content' => "<p class='lead'>When Nodirbek Abdusattorov sits across the chessboard, opponents are immediately struck by his icy composure. His facial expression rarely flickers whether he is conducting a crushing kingside attack or defending a compromised rook ending with 30 seconds on his clock.</p>
<p>As the spearhead of the golden generation of Uzbek chess—which stunned the world by clinching the 44th Chess Olympiad in Chennai—Abdusattorov has rapidly matured into one of the most formidable classical players of the modern era.</p>
<h3>A Universal Style Built on Precision</h3>
<p>Unlike prodigies whose reputations rest solely on tactical fireworks, Nodirbek's repertoire is exceptionally mature. His endgame play is reminiscent of Anatoly Karpov, grinding down micro-advantages with methodical perseverance.</p>
<p>'Nodirbek does not make emotional decisions,' says his coach Rustam Kasimdzhanov. 'He calculates lines with cold clarity and has the stamina to outlast anyone in a seven-hour struggle.'</p>",
                'author_name' => 'Fiona Steil-Antoni',
                'author_title' => 'International Chess Journalist',
                'image_url' => 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'views_count' => 1890,
                'is_featured' => true,
                'is_breaking' => false,
                'published_at' => now()->subDays(2),
            ],

            // 6. Tactics & Puzzles: Top Queen Sacrifices
            [
                'category_slug' => 'tactics-puzzles',
                'title' => 'Top 5 Queen Sacrifices of 2026: Tactical Fireworks from Grandmaster Arenas',
                'slug' => 'top-5-queen-sacrifices-2026-tactical-fireworks',
                'summary' => 'A curated collection of breathtaking master-level queen sacrifices that left supercomputers buzzing and spectators on their feet.',
                'content' => "<p class='lead'>No chess maneuver commands the collective gasp of an auditorium quite like the sacrifice of the monarch's most lethal weapon. We review five extraordinary queen sacrifices from the 2026 tournament circuit that shattered defensive fortresses.</p>
<h3>1. The Clearance Deflection in Wijk aan Zee</h3>
<p>In a razor-sharp Sicilian Dragon, White gave up the queen on h7 simply to clear the h-file for a battery of rooks, forcing an unavoidable mate in 4 moves despite being down 9 points of material.</p>
<h3>2. The Double-Knight Smothered Net in Tashkent</h3>
<p>Black baited White's queen into a poisoned central recapture on d5, only to uncork a stunning knight leap to f2 followed by Nh3 double check and a smothered coronation.</p>
<p>Test your calculation precision and visual geometry by studying each position in our interactive puzzles viewer.</p>",
                'author_name' => 'ChessLiv Puzzle Desk',
                'author_title' => 'Tactics Curator',
                'image_url' => 'https://images.unsplash.com/photo-1580541832626-2a7131ee809f?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '4 min read',
                'views_count' => 2200,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(3),
            ],

            // 7. Tournaments: Norway Chess Armageddon Shakeup
            [
                'category_slug' => 'tournaments',
                'title' => 'Norway Chess Introduces Armageddon Time-Odds Shakeup for 2027 Season',
                'slug' => 'norway-chess-armageddon-time-odds-shakeup-2027',
                'summary' => 'Tournament organizers unveil refined blitz tiebreaker rules designed to reward decisive classical play and eliminate risk-averse strategies.',
                'content' => "<p class='lead'>Norway Chess, long celebrated as the most innovative laboratory for tournament formats, has officially announced significant updates to its signature Armageddon system for the 2027 cycle in Stavanger.</p>
<p>Under the revised regulations, classical victories will now award 3.5 tournament points instead of 3, while an Armageddon victory will earn 1.5 points. Furthermore, the time handicap for the sudden-death Armageddon encounter will be dynamically adjusted based on bidding by the players prior to each round.</p>
<p>'Our goal has always been to eliminate meaningless theoretical draws and reward fighters who take risks on the board,' said Tournament Director Jøran Aulin-Jansson. 'The secret bidding mechanism forces players to evaluate their practical blitz speed with complete honesty.'</p>",
                'author_name' => 'Tarjei J. Svensen',
                'author_title' => 'Nordic Chess Correspondent',
                'image_url' => 'https://images.unsplash.com/photo-1529699211952-734e80c4d42b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '5 min read',
                'views_count' => 1410,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(3),
            ],

            // 8. World Championship: Ju Wenjun Retains Women's Crown
            [
                'category_slug' => 'world-championship',
                'title' => 'Ju Wenjun Retains Women\'s World Championship Title After Gripping Rapid Tiebreaks',
                'slug' => 'ju-wenjun-retains-womens-world-championship-rapid-tiebreaks',
                'summary' => 'Reaffirming her era of dominance, Ju Wenjun demonstrated ice-cold nerve in overtime to capture her fifth consecutive classical world title.',
                'content' => "<p class='lead'>China's Grandmaster Ju Wenjun has confirmed her status as one of the greatest female players in the history of the royal game, overcoming challenger Tan Zhongyi in a breathtaking rapid playoff to retain the Women's World Championship title.</p>
<p>After twelve classical games filled with intense theoretical combat left the match tied at 6-6, the championship moved to 25-minute rapid playoffs. Ju struck in the very first game with the white pieces, converting a slight Catalan endgame edge with surgical endgame precision.</p>
<p>'Tan played an extraordinary match and pushed me to my absolute limits,' an emotional Ju reflected during the coronation ceremony. 'Holding the title requires more than preparation; it demands belief during the darkest moments.'</p>",
                'author_name' => 'GM Judit Polgar',
                'author_title' => 'Legendary Grandmaster & Analyst',
                'image_url' => 'https://images.unsplash.com/photo-1586165368502-1bad197a6461?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'views_count' => 1740,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(4),
                'player_names' => 'Ju Wenjun, Tan Zhongyi',
                'game_white' => 'Ju Wenjun',
                'game_black' => 'Tan Zhongyi',
                'game_result' => '1-0',
                'game_event' => 'FIDE Women\'s World Championship',
                'game_date' => '2023.07.22',
                'pgn' => <<<PGN
[Event "FIDE Women's World Championship"]
[Site "Chongqing, China"]
[Date "2023.07.22"]
[Round "12"]
[White "Ju, Wenjun"]
[Black "Tan, Zhongyi"]
[Result "1-0"]
[WhiteElo "2564"]
[BlackElo "2523"]
[ECO "D04"]

1. d4 d5 2. Nf3 Nf6 3. e3 c5 4. Nbd2 cxd4 5. exd4 Nc6 6. c3 Qc7 7. Bd3 Bg4 8. O-O e6 9. Re1 Bd6 10. Nf1 O-O 11. Bg5 Nd7 12. Bh4 Rfe8 13. Bg3 Bh5 14. Be2 Rab8 15. a4 a6 16. Ne3 Bxg3 17. hxg3 b5 18. axb5 axb5 19. b4 Ne7 20. Ra3 Nc8 21. Nd2 Bxe2 22. Qxe2 Nd6 23. Rea1 Nb6 24. Nb3 Qxc3 25. Nc5 Qxd4 26. Nc2 Qf6 27. Ra6 Red8 28. R1a3 Nbc4 29. Rf3 Qg6 30. Nd4 Ra8 31. Nc6 Re8 32. Rxa8 Qb1+ 33. Qf1 Qxf1+ 34. Kxf1 Rxa8 35. Rd3 Ra1+ 36. Ke2 Ra2+ 37. Ke1 g5 38. g4 Kg7 39. g3 Ra1+ 40. Ke2 Ra2+ 41. Ke1 Kf6 42. Rf3+ Kg7 43. Rd3 1-0
PGN,
            ],

            // 9. GM Insights: Prophylaxis Explained
            [
                'category_slug' => 'gm-insights',
                'title' => 'Prophylaxis Explained: How Karpov and Carlsen Strangle Opponent Counterplay',
                'slug' => 'prophylaxis-explained-karpov-carlsen-strangle-counterplay',
                'summary' => 'Grandmaster guide on asking \'What does my opponent want?\' before launching aggressive operations.',
                'content' => "<p class='lead'>Amateur chess players are habitually conditioned to calculate only their own attacking intentions. Grandmasters, by contrast, dedicate at least half of their cognitive energy to dismantling the opponent's active plans before they can even materialize.</p>
<p>This disciplined preventative thinking—termed <em>prophylaxis</em> by Aaron Nimzowitsch—distinguished the reigns of Anatoly Karpov, Tigran Petrosian, and Magnus Carlsen. By identifying subtle break squares (such as ...c5 or ...e5) two moves before they occur, prophylactic masters render enemy pieces useless.</p>
<h3>Three Habits of Prophylactic Thinkers:</h3>
<ul>
<li><strong>Look for the hidden pawn break:</strong> Ask which pawn lever unlocks the position for the other side.</li>
<li><strong>Restrain before attacking:</strong> Fasten down the center before beginning an assault on the flanks.</li>
<li><strong>Ask 'What is the threat?':</strong> Even after innocuous-looking quiet moves, identify the opponent's tactical intention.</li>
</ul>",
                'author_name' => 'GM Daniel Naroditsky',
                'author_title' => 'Grandmaster & Lead Educator',
                'image_url' => 'https://images.unsplash.com/photo-1560174038-da43ac74f01b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'views_count' => 1980,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(5),
            ],

            // 10. Opening Theory: The Najdorf Sicilian in 2026
            [
                'category_slug' => 'opening-theory',
                'title' => 'The Najdorf Sicilian in 2026: Surviving the English Attack with 6...e5',
                'slug' => 'najdorf-sicilian-2026-surviving-english-attack',
                'summary' => 'Concrete defensive discoveries showing how Black withstands White\'s pawn storm in the sharpest battleground of modern opening theory.',
                'content' => "<p class='lead'>Since Bobby Fischer and Garry Kasparov made it their premier battleground, the Sicilian Najdorf (1.e4 c5 2.Nf3 d6 3.d4 cxd4 4.Nxd4 Nf6 5.Nc3 a6) has remained the ultimate test of courage at the 2700+ level.</p>
<p>Against White's dreaded English Attack (6.Be3 e5 7.Nb3 Be6 8.f3), recent engine developments had cast doubt on traditional queenside pawn pushes. However, cutting-edge novelties with early ...h5 and ...Nbd7 maneuvers have re-established Black's dynamic equilibrium.</p>
<p>By preventing White's g4-g5 thrust cold on move 8, Black gains critical tempi to coordinate minor pieces along the semi-open c-file, transforming a defensive position into a vicious counter-punching system.</p>",
                'author_name' => 'GM Peter Svidler',
                'author_title' => 'Eight-Time Russian Champion',
                'image_url' => 'https://images.unsplash.com/photo-1528819622765-d6bcf132f793?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '8 min read',
                'views_count' => 1520,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(5),
                'player_names' => 'Maxime Vachier-Lagrave, Fabiano Caruana',
                'game_white' => 'Caruana, Fabiano',
                'game_black' => 'Vachier-Lagrave, Maxime',
                'game_result' => '0-1',
                'game_event' => 'FIDE Candidates 2020',
                'game_date' => '2020.03.25',
                'pgn' => <<<PGN
[Event "FIDE Candidates 2020"]
[Site "Yekaterinburg, Russia"]
[Date "2020.03.25"]
[Round "7"]
[White "Caruana, Fabiano"]
[Black "Vachier-Lagrave, Maxime"]
[Result "0-1"]
[WhiteElo "2842"]
[BlackElo "2767"]
[ECO "B96"]

1. e4 c5 2. Nf3 d6 3. d4 cxd4 4. Nxd4 Nf6 5. Nc3 a6 6. Bg5 e6 7. f4 Qc7 8. Bxf6 gxf6 9. Be2 Nc6 10. Qd3 h5 11. O-O-O Bd7 12. Kb1 O-O-O 13. Rhf1 Be7 14. f5 Ne5 15. Qh3 Kb8 16. fxe6 fxe6 17. Nxe6 Qc8 18. Nd5 Bxe6 19. Qb3 Bxd5 20. Rxd5 Qc6 21. a4 Nd7 22. Qe3 Rc8 23. c3 Ne5 24. a5 Bd8 25. Rfd1 Bc7 26. Qd4 Rhg8 27. g3 h4 28. gxh4 Rg2 29. Bf1 Rxh2 30. h5 Rxh5 31. Be2 Rh2 0-1
PGN,
            ],

            // 11. Player Profiles: Bodhana Sivanandan
            [
                'category_slug' => 'player-profiles',
                'title' => 'Bodhana Sivanandan: The 11-Year-Old Prodigy Rewriting International Records',
                'slug' => 'bodhana-sivanandan-11-year-old-prodigy-records',
                'summary' => 'An intimate profile of Britain\'s brightest chess jewel following her historic Olympiad performance and youngest-ever master norms.',
                'content' => "<p class='lead'>Not since Judit Polgar burst onto the international stage has a young female player commanded the fascination of the global chess hierarchy quite like Harrow schoolgirl Bodhana Sivanandan.</p>
<p>At an age when most children are learning basic tactical patterns, Bodhana was representing the England senior national team at the 45th Chess Olympiad in Budapest, remaining undefeated across ten grueling rounds and capturing the individual board prize.</p>
<p>'Her intuition for piece coordination is astonishing,' observes British Grandmaster Gawain Jones. 'You simply cannot teach an 11-year-old to sense quiet king safety weaknesses the way she does naturally.'</p>",
                'author_name' => 'Leonard Barden',
                'author_title' => 'Senior Chess Historian & Columnist',
                'image_url' => 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '5 min read',
                'views_count' => 3100,
                'is_featured' => true,
                'is_breaking' => false,
                'published_at' => now()->subDays(6),
            ],

            // 12. Tournaments: FIDE World Cup 2027 Host City Bidding
            [
                'category_slug' => 'tournaments',
                'title' => 'FIDE World Cup 2027 Host City Bidding Enters Decisive Phase',
                'slug' => 'fide-world-cup-2027-host-city-bidding',
                'summary' => 'Four continents vie for the rights to host the grueling 206-player knockout tournament that awards three Candidates qualification spots.',
                'content' => "<p class='lead'>The International Chess Federation (FIDE) has announced the shortlisting of four candidate cities for the prestigious FIDE World Cup 2027: New Delhi (India), Tashkent (Uzbekistan), Valencia (Spain), and Abu Dhabi (UAE).</p>
<p>The World Cup remains the most unforgiving tournament on the international calendar: a single bad morning or missed tactical shot over two classical games sends even the highest-rated players packing.</p>
<p>With three coveted spots in the 2028 Candidates tournament on the line, the bidding federations have pledged record-breaking prize funds exceeding \$3.5 million to attract the entire world top 100.</p>",
                'author_name' => 'ChessLiv Desk',
                'author_title' => 'Global News Bureau',
                'image_url' => 'https://images.unsplash.com/photo-1580541832626-2a7131ee809f?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '4 min read',
                'views_count' => 1290,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(6),
            ],

            // 13. GM Insights: Calculating in Fog
            [
                'category_slug' => 'gm-insights',
                'title' => 'Calculating in Fog: Practical Decision-Making Under Extreme Time Trouble',
                'slug' => 'calculating-in-fog-practical-decision-making-time-trouble',
                'summary' => 'How elite grandmasters utilize candidate move pruning and chunking techniques when the clock drops below 60 seconds.',
                'content' => "<p class='lead'>Every tournament competitor knows the stomach-churning sensation of a digital clock ticking beneath 30 seconds while facing a labyrinth of tactical complications with no increment.</p>
<p>Psychological research into elite grandmaster intuition reveals that under extreme time duress, champions stop calculating brute-force variations. Instead, they rely on <em>candidate move pruning</em>—immediately discarding 90% of legal moves based on ingrained schematic heuristics.</p>
<h3>Three Principles for Surviving Time Scrambles:</h3>
<ol>
<li><strong>Keep pieces protected:</strong> Loose pieces drop in mutual scrambles. Always prioritize tactile piece defendedness.</li>
<li><strong>Do not seek perfection:</strong> In time pressure, the practical 'good enough' move made in 4 seconds beats the 'best engine move' made in 28 seconds.</li>
<li><strong>Maintain a physical rhythm:</strong> Breathe evenly and avoid erratic hand movements that increase adrenaline spikes.</li>
</ol>",
                'author_name' => 'GM Jonathan Rowson',
                'author_title' => 'Grandmaster & Philosopher',
                'image_url' => 'https://images.unsplash.com/photo-1529699211952-734e80c4d42b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '7 min read',
                'views_count' => 1840,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(7),
            ],

            // 14. Opening Theory: The Catalan Defense Repertoire
            [
                'category_slug' => 'opening-theory',
                'title' => 'The Catalan Defense Repertoire: Why 1.d4 Players Always Rely on the g2 Bishop',
                'slug' => 'catalan-defense-repertoire-g2-bishop-supremacy',
                'summary' => 'Deep structural inspection of the Catalan sniper bishop, open c-file battles, and typical grandmaster conversion techniques.',
                'content' => "<p class='lead'>From Vladimir Kramnik's championship triumphs to Magnus Carlsen and Ding Liren, the Catalan Opening (1.d4 Nf6 2.c4 e6 3.g3 d5 4.Bg2) stands as the gold standard of positional pressure at the highest level.</p>
<p>The cornerstone of White's strategic asset is the uncontested bishop on g2. Even when Black temporarily grabs a pawn on c4, White exerts insidious pressure down the long h1-a8 diagonal, tying down Black's queenside development and paralyzing the b7 square.</p>
<p>We analyze key conversion methods in the Open Catalan and illustrate how modern grandmasters recycle the c3 knight into active kingside outposts.</p>",
                'author_name' => 'GM Boris Gelfand',
                'author_title' => 'FIDE World Championship Contender',
                'image_url' => 'https://images.unsplash.com/photo-1586165368502-1bad197a6461?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '7 min read',
                'views_count' => 1630,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(8),
                'player_names' => 'Daniil Dubov, Anish Giri',
                'game_white' => 'Dubov, Daniil',
                'game_black' => 'Giri, Anish',
                'game_result' => '1-0',
                'game_event' => 'Tata Steel Masters',
                'game_date' => '2021.01.24',
                'pgn' => <<<PGN
[Event "Tata Steel Masters"]
[Site "Wijk aan Zee, Netherlands"]
[Date "2021.01.24"]
[Round "8"]
[White "Dubov, Daniil"]
[Black "Giri, Anish"]
[Result "1-0"]
[WhiteElo "2710"]
[BlackElo "2764"]
[ECO "E04"]

1. d4 Nf6 2. c4 e6 3. Nf3 d5 4. g3 dxc4 5. Bg2 Nc6 6. Qa4 Bb4+ 7. Bd2 Nd5 8. Bxb4 Nb6 9. Qa3 Nxb4 10. Qxb4 a5 11. Qd2 O-O 12. O-O Bd7 13. Ne5 c6 14. Na3 f6 15. Nexc4 Nd5 16. e4 Ne7 17. Rac1 b5 18. Ne3 Be8 19. Rfd1 Bf7 20. d5 exd5 21. exd5 cxd5 22. Nxb5 Qb6 23. a4 Rad8 24. Qd4 Qe6 25. Nc7 Qe5 26. Ncxd5 Nxd5 27. Nxd5 Bxd5 28. Bxd5+ Kh8 29. Qxe5 fxe5 30. Be4 1-0
PGN,
            ],

            // 15. World Championship: Candidates 2026 Qualification Race
            [
                'category_slug' => 'world-championship',
                'title' => 'Candidates 2026 Qualification Race: Tracking the FIDE Circuit and Rating Standings',
                'slug' => 'candidates-2026-qualification-race-fide-circuit-tracker',
                'summary' => 'A comprehensive breakdown of qualification criteria, tournament bonuses, and who holds the pole position for the upcoming cycle.',
                'content' => "<p class='lead'>With the FIDE Circuit leaderboard tightening each passing weekend, the battle for the coveted eight seats in the 2026 Candidates Tournament has reached fever pitch across super-GM and open circuits alike.</p>
<p>Under FIDE's refined qualification framework, players earn points from their top five tournament finishes. We analyze the current top contenders—including Fabiano Caruana, Hikaru Nakamura, Alireza Firouzja, and Vincent Keymer—and model their mathematical pathways to qualification.</p>
<p>Special attention is given to the final Grand Swiss and World Cup events, which offer high-octane opportunities for rising stars outside the classical rating elite to leapfrog into contention.</p>",
                'author_name' => 'Peter Doggers',
                'author_title' => 'Senior Tournament Analyst',
                'image_url' => 'https://images.unsplash.com/photo-1560174038-da43ac74f01b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '5 min read',
                'views_count' => 1970,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(9),
            ],

            // 16. Player Profiles: Vincent Keymer
            [
                'category_slug' => 'player-profiles',
                'title' => 'Vincent Keymer: Germany\'s Number One and the Quiet Evolution of Positional Mastery',
                'slug' => 'vincent-keymer-germanys-number-one-evolution',
                'summary' => 'Under the mentorship of Peter Leko, Vincent Keymer has crafted a rock-solid repertoire and endgame technique that challenges the global top 10.',
                'content' => "<p class='lead'>While flashy blitz streamers dominate online headlines, 21-year-old German Grandmaster Vincent Keymer has steadily constructed a classical foundation that harkens back to the golden era of European positional giants.</p>
<p>Mentored since adolescence by former World Championship challenger Peter Leko, Keymer possesses an exceptionally low error rate in queenless middlegames and minor-piece endgames. His performances in the European Individual Championship and Grand Swiss have established him as Germany's most potent world title contender in generations.</p>
<p>'Vincent rarely beats himself,' Leko proudly observed. 'He understands that at the 2750+ tier, greatness is measured by patience in slightly better positions.'</p>",
                'author_name' => 'GM Jan Gustafsson',
                'author_title' => 'Grandmaster & Renowned Commentator',
                'image_url' => 'https://images.unsplash.com/photo-1528819622765-d6bcf132f793?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'views_count' => 1780,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(10),
            ],

            // 17. Tournaments: European Club Cup Epic Showdown
            [
                'category_slug' => 'tournaments',
                'title' => 'European Club Cup: Super-Club Showdown Delivers Epic Final-Round Drama',
                'slug' => 'european-club-cup-super-club-showdown-epic-drama',
                'summary' => 'Star-studded rosters featuring 2700+ GMs clashed in Rhodes for club supremacy with tiebreaks decided on fourth board blitz.',
                'content' => "<p class='lead'>The European Club Cup in Rhodes concluded with breathtaking team drama as Czech powerhouse Nový Bor edged out French champions Clichy on board count in a deciding final-round thriller.</p>
<p>Featuring a congregation of world elite players—including Vidit Gujrathi, Pentala Harikrishna, and David Navara—the tournament demonstrated the raw emotional power of team chess, where individual triumphs count for nothing if the team score falters.</p>
<p>The decisive breakthrough occurred on board four, where Navara ground down a marathon 94-move knight-and-pawn endgame with both kings wandering into the center under thirty-second increments.</p>",
                'author_name' => 'Leontxo García',
                'author_title' => 'El País Chess Columnist',
                'image_url' => 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '5 min read',
                'views_count' => 1340,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(11),
            ],

            // 18. GM Insights: Opposite-Colored Bishop Endgames
            [
                'category_slug' => 'gm-insights',
                'title' => 'Opposite-Colored Bishop Endgames: Drawing Miracles and Attacking Weapons',
                'slug' => 'opposite-colored-bishop-endgames-drawing-miracles',
                'summary' => 'Dispelling the myth that opposite-colored bishops always draw—understanding dynamic attacking advantages with queens on the board.',
                'content' => "<p class='lead'>Every beginner is told that 'opposite-colored bishops mean a draw.' Yet in master games with heavy pieces still on the board, having opposite-colored bishops often creates an overwhelming attacking advantage equivalent to being an entire piece ahead.</p>
<p>Why? Because the attacker operates on squares that the defending bishop can never touch. When White attacks dark squares and Black holds a light-squared bishop, Black is effectively defending with ten men against eleven.</p>
<h3>Essential Guidelines:</h3>
<ul>
<li><strong>Pure endings draw:</strong> In pure bishop-and-pawn endings, the defender can frequently hold even two pawns down by setting an impenetrable block on the color of their bishop.</li>
<li><strong>With queens, attack relentlessly:</strong> The presence of queens transforms the opposite-colored bishop into an uncounterable battering ram against the enemy king.</li>
</ul>",
                'author_name' => 'GM Karsten Müller',
                'author_title' => 'FIDE Senior Trainer & Endgame Specialist',
                'image_url' => 'https://images.unsplash.com/photo-1580541832626-2a7131ee809f?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '7 min read',
                'views_count' => 1870,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(12),
            ],

            // 19. Tactics & Puzzles: The Immortal Zugzwang
            [
                'category_slug' => 'tactics-puzzles',
                'title' => 'The Immortal Zugzwang: When Every Legal Move Spells Total Ruin',
                'slug' => 'immortal-zugzwang-when-every-move-spells-ruin',
                'summary' => 'Exploring historical and modern games where players were paralyzed into submission without a single direct piece capture.',
                'content' => "<p class='lead'>Zugzwang—the German term for 'compulsion to move'—represents the purest form of strategic helplessness on a chessboard. You are not in check, but any move you make fatally compromises your position.</p>
<p>While commonplace in king-and-pawn endgames, zugzwang in full-board middlegames is exceptionally rare and regarded as the pinnacle of artistic beauty. We examine Aron Nimzowitsch's famed 'Immortal Zugzwang Game' against Sämisch in 1923, as well as a modern parallel executed by Garry Kasparov.</p>
<p>Step through each position on our interactive analysis board and discover how quiet, non-threatening moves can weave an inescapable spider's web.</p>",
                'author_name' => 'GM Daniel King',
                'author_title' => 'Grandmaster & PowerPlay Chess',
                'image_url' => 'https://images.unsplash.com/photo-1529699211952-734e80c4d42b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '5 min read',
                'views_count' => 2190,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(13),
            ],

            // 20. Player Profiles: Wei Yi's Creative Renaissance
            [
                'category_slug' => 'player-profiles',
                'title' => 'Wei Yi\'s Creative Renaissance: From Sacrificial Machine to Universal Super-GM',
                'slug' => 'wei-yi-creative-renaissance-universal-super-gm',
                'summary' => 'China\'s reigning Tata Steel champion discusses his evolution from teenage tactical firebrand to one of the most versatile universal players alive.',
                'content' => "<p class='lead'>A decade ago, a 16-year-old Wei Yi stunned the chess world by uncorking the 'Game of the Century' against Lazaro Bruzon—a 17-move king hunt featuring a rook sacrifice that drew comparisons to Mikhail Tal.</p>
<p>After taking time away to complete university studies in Beijing, Wei Yi has returned to the international arena transformed. His triumph at Tata Steel Masters in Wijk aan Zee—winning against elite fields in tiebreaks—unveiled a mature, universal style capable of grinding out dry technical positions as comfortably as setting tactical fireworks.</p>
<p>'When I was younger, I looked only for mate,' Wei Yi smiled in a recent interview. 'Now, I appreciate that harmony across all 64 squares is the most powerful weapon of all.'</p>",
                'author_name' => 'WGM Dina Belenkaya',
                'author_title' => 'Chess Broadcaster & Streamer',
                'image_url' => 'https://images.unsplash.com/photo-1586165368502-1bad197a6461?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'views_count' => 2050,
                'is_featured' => true,
                'is_breaking' => false,
                'published_at' => now()->subDays(14),
            ],
        ];

        $insertedCount = 0;
        foreach ($articles as $item) {
            $cat = $categories[$item['category_slug']] ?? null;
            if (!$cat) continue;

            $article = Article::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $cat->id,
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'summary' => $item['summary'],
                    'content' => $item['content'],
                    'author_name' => $item['author_name'],
                    'author_title' => $item['author_title'],
                    'image_url' => $item['image_url'],
                    'read_time' => $item['read_time'],
                    'views_count' => $item['views_count'],
                    'is_featured' => $item['is_featured'],
                    'is_breaking' => $item['is_breaking'],
                    'published_at' => $item['published_at'],
                    'player_names' => $item['player_names'] ?? null,
                    'game_white' => $item['game_white'] ?? null,
                    'game_black' => $item['game_black'] ?? null,
                    'game_result' => $item['game_result'] ?? null,
                    'game_event' => $item['game_event'] ?? null,
                    'game_date' => $item['game_date'] ?? null,
                    'pgn' => $item['pgn'] ?? null,
                ]
            );

            // Sync with many-to-many categories if relation exists
            if (method_exists($article, 'categories')) {
                $article->categories()->syncWithoutDetaching([$cat->id]);
            }

            // If game PGN is provided, parse and create/update ChessGame
            if (!empty($item['pgn'])) {
                $parsed = $parser->parse($item['pgn']);
                if ($parsed) {
                    ChessGame::updateOrCreate(
                        ['article_id' => $article->id],
                        [
                            'white' => $parsed['white'] ?? $item['game_white'] ?? 'White',
                            'black' => $parsed['black'] ?? $item['game_black'] ?? 'Black',
                            'white_elo' => $parsed['white_elo'] ?? null,
                            'black_elo' => $parsed['black_elo'] ?? null,
                            'event' => $parsed['event'] ?? $item['game_event'] ?? 'Master Game',
                            'site' => $parsed['site'] ?? 'International Arena',
                            'date' => $parsed['date'] ?? $item['game_date'] ?? date('Y.m.d'),
                            'round' => $parsed['round'] ?? '1',
                            'result' => $parsed['result'] ?? $item['game_result'] ?? '*',
                            'eco' => $parsed['eco'] ?? null,
                            'starting_fen' => $parsed['starting_fen'] ?? ChessGame::STANDARD_STARTING_FEN,
                            'pgn' => $item['pgn'],
                            'moves_san' => $parsed['moves_san'] ?? [],
                            'source_url' => $article->original_url,
                        ]
                    );
                }
            }

            $insertedCount++;
        }

        $this->command->info("Successfully seeded {$insertedCount} new ChessLiv articles with categories and games!");
    }
}
