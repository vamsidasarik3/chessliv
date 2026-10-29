<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Article;
use App\Models\Tournament;
use App\Models\Puzzle;
use Illuminate\Support\Str;

class ChesslivSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesData = [
            [
                'name' => 'Tournaments & Events',
                'slug' => 'tournaments',
                'description' => 'Real-time coverage, pairings, round recaps, and live standings from premier world events.',
                'icon' => 'bi-trophy',
                'order' => 1,
            ],
            [
                'name' => 'World Championship',
                'slug' => 'world-championship',
                'description' => 'Latest updates, candidate qualifiers, game reviews, and match drama for the crown.',
                'icon' => 'bi-award',
                'order' => 2,
            ],
            [
                'name' => 'Grandmaster Insights',
                'slug' => 'gm-insights',
                'description' => 'Deep tactical breakdowns, psychological insights, and grandmaster commentary.',
                'icon' => 'bi-lightbulb',
                'order' => 3,
            ],
            [
                'name' => 'Opening Theory',
                'slug' => 'opening-theory',
                'description' => 'Modern novelties, opening surprises, refutations, and repertoire recommendations.',
                'icon' => 'bi-book',
                'order' => 4,
            ],
            [
                'name' => 'Player Profiles',
                'slug' => 'player-profiles',
                'description' => 'Interviews, rating milestones, rising prodigies, and career retrospectives.',
                'icon' => 'bi-person-badge',
                'order' => 5,
            ],
            [
                'name' => 'Tactics & Puzzles',
                'slug' => 'tactics-puzzles',
                'description' => 'Test your calculation skills with daily puzzles from master games.',
                'icon' => 'bi-puzzle',
                'order' => 6,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['slug']] = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
        }

        $articlesData = [
            [
                'category_slug' => 'world-championship',
                'title' => 'Gukesh D Shocks World Elite with Masterclass in Candidates Defense',
                'slug' => 'gukesh-d-shocks-world-elite-with-masterclass-candidates',
                'summary' => 'In a breathtaking demonstration of defensive resilience and endgame precision, reigning world champion Gukesh D navigated treacherous complications to secure a pivotal victory.',
                'content' => "<p class='lead'>In a breathtaking demonstration of defensive resilience and endgame precision, reigning world champion Gukesh D navigated treacherous complications in round 9 to deliver an unforgettable tactical masterclass.</p>
<p>Facing aggressive preparation against his Catalan setup, Gukesh calmly gave up an exchange on move 23 to secure impenetrable dark-square blockade. Commentators around the globe marveled at the computational exactness with which the Indian prodigy neutralized white's initiative before converting a passed d-pawn into an overwhelming queen promotion threat.</p>
<h4>Key Turning Points</h4>
<ul>
<li><strong>Move 21. ... Nh5!</strong> - A paradoxical knight retreat disrupting coordinate lines.</li>
<li><strong>Move 29. ... e4!</strong> - Sacrificing pawn structure to trap the white bishop on an irrelevant diagonal.</li>
<li><strong>Move 41. ... Kd5!</strong> - King activation precisely upon reaching the first time control.</li>
</ul>
<p>'I felt that after the time control, the pawn structure was fixed in my favor,' Gukesh stated during the post-game press conference. 'It was just a matter of calculating the king paths without giving counterplay.'</p>
<p>With this landmark victory, Gukesh cements his position at the summit of the championship standings, earning praise from former world champions including Garry Kasparov and Viswanathan Anand.</p>",
                'author_name' => 'GM Vladimir Kramnik',
                'author_title' => 'Senior Strategic Analyst',
                'image_url' => 'https://images.unsplash.com/photo-1529699211952-734e80c4d42b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '5 min read',
                'views_count' => 1420,
                'is_featured' => true,
                'is_breaking' => true,
                'published_at' => now()->subHours(2),
            ],
            [
                'category_slug' => 'tournaments',
                'title' => 'Magnus Carlsen Unleashes Stunning Novelty in Freestyle Chess Grand Slam',
                'slug' => 'magnus-carlsen-unleashes-stunning-novelty-freestyle-chess',
                'summary' => 'Magnus Carlsen demonstrated why Chess960 is heralded as the future of pure chess instinct, catching his opponent off guard on move 4 with an unorthodox queen sacrifice.',
                'content' => "<p class='lead'>World number one Magnus Carlsen set the Freestyle Chess Grand Slam alight today with a game that chess historians will dissect for decades to come.</p>
<p>In a starting position with rooks and knights inverted, Carlsen dispensed with orthodox opening principles, advancing wing pawns on both flanks before springing an audacious piece sacrifice on the fourth move.</p>
<p>Opponents who rely on home engine preparation find themselves completely disarmed against Carlsen's intuitive mastery of unbalanced board positions. Carlsen spent less than 3 minutes on his first 15 moves, building dynamic compensation that culminated in a devastating double-rook pin along the semi-open c-file.</p>
<p>'Freestyle chess brings back the joy of discovering the board from scratch,' Carlsen reflected afterwards with a grin. 'When there is no database to guide you, every single move is honest.'</p>",
                'author_name' => 'Aruna Ramesh',
                'author_title' => 'Tournament Reporter',
                'image_url' => 'https://images.unsplash.com/photo-1586165368502-1bad197a6461?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '4 min read',
                'views_count' => 980,
                'is_featured' => true,
                'is_breaking' => false,
                'published_at' => now()->subHours(6),
            ],
            [
                'category_slug' => 'opening-theory',
                'title' => 'The Renaissance of the French Defense: Why Super GMs Are Re-evaluating 3...c5',
                'slug' => 'renaissance-french-defense-super-gms-re-evaluating',
                'summary' => 'Long criticized for a cramped light-squared bishop, the French Defense has seen an explosive revival at the 2750+ rating level thanks to fresh engine discoveries in the Winawer.',
                'content' => "<p class='lead'>For years, the French Defense was dismissed by the ultra-elite as too passive against 1.e4 compared to the Sicilian or Berlin. That orthodoxy has been shattered in 2026.</p>
<p>Deep neural engine runs by top seconds have unearthed dynamic counter-attacking resources in the Winawer Poisoned Pawn variation and the Advance structure. Rather than nursing passive defenses, black players are actively sacrificing queenside pawns to rupture white's central fortress.</p>
<h4>Why the French is Back:</h4>
<ol>
<li>Black achieves clear counter-targets against d4 and c3 early.</li>
<li>Modern engine evaluations show the classic 'bad' light-squared bishop frequently becomes an endgame hero after ...Ba6 exchanges.</li>
<li>White must navigate razor-sharp theoretical minefields where a single slip leads to king vulnerability.</li>
</ol>
<p>Expect to see top grandmasters testing this fighting weapon throughout the upcoming international circuit.</p>",
                'author_name' => 'IM David Howell',
                'author_title' => 'Opening Theory Columnist',
                'image_url' => 'https://images.unsplash.com/photo-1560174038-da43ac74f01b?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'views_count' => 840,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDay(),
            ],
            [
                'category_slug' => 'player-profiles',
                'title' => 'Praggnanandhaa and Vaishali: The Sibling Dynasties Redefining Global Chess',
                'slug' => 'praggnanandhaa-and-vaishali-sibling-dynasty',
                'summary' => 'From Chennai chess academies to simultaneous Candidates qualification, the Rameshbabu siblings have carved an unprecedented chapter in the history of international sports.',
                'content' => "<p class='lead'>Never before in the centuries-old history of chess have a brother and sister simultaneously occupied positions in the world top tier and qualified for the supreme Candidates tournament together.</p>
<p>Rameshbabu Praggnanandhaa and his elder sister Vaishali Rameshbabu represent the pinnacle of modern Indian chess excellence: unfazed demeanor, exceptional work ethic, and an encyclopedic understanding of classical technique.</p>
<p>Under the tutelage of coach RB Ramesh and the unwavering support of their mother Nagalakshmi, the duo has inspired an entire generation of junior players across the subcontinent and worldwide.</p>",
                'author_name' => 'Susan Polgar',
                'author_title' => 'Grandmaster & Former Women\'s World Champion',
                'image_url' => 'https://images.unsplash.com/photo-1528819622765-d6bcf132f793?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '7 min read',
                'views_count' => 1120,
                'is_featured' => true,
                'is_breaking' => false,
                'published_at' => now()->subDays(2),
            ],
            [
                'category_slug' => 'gm-insights',
                'title' => 'Mastering Complex Rook Endgames: The Lucena & Philidor Principles in 2026',
                'slug' => 'mastering-complex-rook-endgames-lucena-philidor',
                'summary' => 'Rook and pawn endgames comprise nearly 50% of all master conclusions. Review the decisive maneuvers that separate Grandmasters from club champions.',
                'content' => "<p class='lead'>Tarrasch famously observed that 'all rook endgames are drawn'—a humorous exaggeration highlighting just how tricky and paradoxical these endings truly are.</p>
<p>In this instructional masterclass, we examine how top players convert minuscule advantages using modern 'bridge building' techniques and checking distance principles.</p>
<h4>Golden Rules of Rook Endgames</h4>
<ul>
<li><strong>Rooks belong behind passed pawns</strong>—whether your own to support advance, or the opponent's to impede them.</li>
<li><strong>The active king is worth a full pawn</strong> in 4-vs-3 rook endgames on a single flank.</li>
<li><strong>Cutting off the enemy king</strong> along a file is often more potent than immediately snatching hanging pawns.</li>
</ul>",
                'author_name' => 'GM Karsten Müller',
                'author_title' => 'FIDE Senior Trainer & Endgame Specialist',
                'image_url' => 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '8 min read',
                'views_count' => 670,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(3),
            ],
            [
                'category_slug' => 'tournaments',
                'title' => 'Hikaru Nakamura Clinches Titled Tuesday Double With Blistering Blitz Speed',
                'slug' => 'hikaru-nakamura-clinches-titled-tuesday-double',
                'summary' => 'Streaming live to over 50,000 spectators, Hikaru Nakamura swept both early and late editions of Titled Tuesday with an astronomical 21/22 aggregate score.',
                'content' => "<p class='lead'>Super-GM and streaming sensation Hikaru Nakamura proved once again why he remains the undisputed blitz juggernaut of online chess, sweeping both editions of Tuesday's global arena.</p>
<p>Playing at an average move speed of 0.8 seconds while answering chat questions, Nakamura dispatched a field featuring over 700 titled players including top grandmasters.</p>
<p>'Speed chess is about pattern recognition and emotional equilibrium under bullet clocks,' Nakamura explained during his celebratory stream.</p>",
                'author_name' => 'ChessLiv Desk',
                'author_title' => 'Online Circuit Desk',
                'image_url' => 'https://images.unsplash.com/photo-1580541832626-2a7131ee809f?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '3 min read',
                'views_count' => 1580,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($articlesData as $item) {
            $cat = $categories[$item['category_slug']] ?? null;
            if (!$cat) continue;

            Article::firstOrCreate(
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
                ]
            );
        }

        $tournamentsData = [
            [
                'name' => 'FIDE Candidates Tournament 2026',
                'location' => 'Madrid, Spain',
                'dates' => 'Oct 10 - Oct 28, 2026',
                'format' => '14-Round Classical (Double Round-Robin)',
                'current_leader' => 'Gukesh D (7.5 / 11)',
                'status' => 'ongoing',
            ],
            [
                'name' => 'Freestyle Chess Grand Slam Tour',
                'location' => 'Paris, France',
                'dates' => 'Oct 12 - Oct 18, 2026',
                'format' => 'Chess960 Rapid & Blitz Knockout',
                'current_leader' => 'Magnus Carlsen (6.0 / 7)',
                'status' => 'ongoing',
            ],
            [
                'name' => 'Tata Steel Chess Tournament 2027',
                'location' => 'Wijk aan Zee, Netherlands',
                'dates' => 'Jan 16 - Feb 01, 2027',
                'format' => '14-Player Classical Super-Tournament',
                'current_leader' => 'Field Announced (Defending: Wei Yi)',
                'status' => 'upcoming',
            ],
            [
                'name' => 'FIDE World Rapid & Blitz Championship',
                'location' => 'Doha, Qatar',
                'dates' => 'Dec 25 - Dec 31, 2026',
                'format' => 'Open & Women Swiss Championships',
                'current_leader' => 'Registrations Active',
                'status' => 'upcoming',
            ],
            [
                'name' => 'Global Chess League Season 3',
                'location' => 'London, UK',
                'dates' => 'Sep 02 - Sep 14, 2026',
                'format' => 'Franchise Team Rapid System',
                'current_leader' => 'Triveni Continental Kings (Champions)',
                'status' => 'completed',
            ],
        ];

        foreach ($tournamentsData as $t) {
            Tournament::firstOrCreate(
                ['name' => $t['name']],
                $t
            );
        }

        $puzzlesData = [
            [
                'title' => "Paul Morphy's Opera House Queen Sacrifice",
                'fen' => '4kb1r/p2rqppp/5n2/1B2p1B1/4P3/1Q6/PPP2PPP/2KR4 w k - 0 14',
                'turn' => 'white',
                'solution' => '1. Bxd7+ Nxd7 2. Qb8+ Nxb8 3. Rd8#',
                'rating' => 1950,
                'hint' => 'White has severe pressure along the d-file. Eliminate the defending knight and sacrifice the queen for an irresistible back-rank checkmate.',
            ],
            [
                'title' => 'Classic Smothered Mate Motif',
                'fen' => '6k1/5ppp/8/8/8/8/1Q4PP/6K1 w - - 0 1',
                'turn' => 'white',
                'solution' => '1. Qb8#',
                'rating' => 1100,
                'hint' => 'Spot the king trapped behind his own wall of pawns.',
            ],
            [
                'title' => 'Lasker Classical Double Bishop Sacrifice',
                'fen' => 'r1bq1rk1/ppp2ppp/2n1pn2/3p4/2PP4/2NBPN2/PP3PPP/R1BQK2R w KQ - 0 1',
                'turn' => 'white',
                'solution' => '1. Bxh7+ Kxh7 2. Ng5+ Kg8 3. Qh5',
                'rating' => 1750,
                'hint' => 'Strike the h7 vulnerability to force open the enemy fortress.',
            ],
        ];

        foreach ($puzzlesData as $pz) {
            Puzzle::firstOrCreate(
                ['title' => $pz['title']],
                $pz
            );
        }
    }
}
