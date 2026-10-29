<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsSource;
use App\Services\NewsIngestionService;

class ImportChessNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chessliv:import-feeds 
                            {--source= : Specific news source slug (e.g. fide-news)}
                            {--limit= : Max items to import per feed}
                            {--force : Force import even if recently fetched}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest chess news articles from configured RSS/Atom public feeds';

    /**
     * Execute the console command.
     */
    public function handle(NewsIngestionService $ingestionService): int
    {
        $this->info('Starting ChessLiv news feed ingestion...');

        $sourceSlug = $this->option('source');
        $limit = $this->option('limit') ? (int)$this->option('limit') : null;

        if ($sourceSlug) {
            $source = NewsSource::where('slug', $sourceSlug)
                ->orWhere('id', $sourceSlug)
                ->first();

            if (!$source) {
                $this->error("News source with slug or ID '{$sourceSlug}' not found.");
                return Command::FAILURE;
            }

            $this->line("Fetching feed: <comment>{$source->name}</comment> ({$source->feed_url})");
            $result = $ingestionService->ingestSource($source, $limit);

            $this->displaySingleResult($result);

            return $result['success'] ? Command::SUCCESS : Command::FAILURE;
        }

        // Ingest all active sources
        $this->line('Ingesting from all active chess news sources...');
        $summary = $ingestionService->ingestAll($limit);

        $this->newLine();
        $this->info("Ingestion completed across {$summary['total_sources']} sources:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Sources', $summary['total_sources']],
                ['Articles Imported', $summary['total_imported']],
                ['Duplicates Skipped', $summary['total_skipped']],
                ['Failed Sources', $summary['total_failed']],
            ]
        );

        $rows = [];
        foreach ($summary['source_results'] as $slug => $res) {
            $status = $res['success'] ? '<info>OK</info>' : '<error>Failed</error>';
            $errorSummary = !empty($res['errors']) ? implode('; ', array_slice($res['errors'], 0, 1)) : 'None';
            $rows[] = [
                $res['source_name'],
                $status,
                $res['imported'],
                $res['skipped'],
                $errorSummary,
            ];
        }

        $this->table(['Source', 'Status', 'Imported', 'Skipped', 'Error Details'], $rows);

        return $summary['total_failed'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    protected function displaySingleResult(array $result): void
    {
        if ($result['success']) {
            $this->info("Imported: {$result['imported']} new articles | Skipped: {$result['skipped']} duplicates.");
        } else {
            $this->error("Failed to ingest feed from {$result['source_name']}");
            foreach ($result['errors'] as $err) {
                $this->line("  - <fg=red>{$err}</>");
            }
        }
    }
}
