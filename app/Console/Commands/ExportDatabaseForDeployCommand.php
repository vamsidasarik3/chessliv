<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ExportDatabaseForDeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export-for-deploy {--output= : Custom path for the exported database dump}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports local database into a compressed SQL dump for deployment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database export for deployment...');

        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");

        if (!$dbConfig || !in_array($dbConfig['driver'] ?? '', ['mysql', 'mariadb'])) {
            $this->error("Unsupported database driver: " . ($dbConfig['driver'] ?? 'unknown'));
            return 1;
        }

        $host = $dbConfig['host'] ?? '127.0.0.1';
        $port = $dbConfig['port'] ?? '3306';
        $database = $dbConfig['database'] ?? '';
        $username = $dbConfig['username'] ?? '';
        $password = $dbConfig['password'] ?? '';

        if (empty($database)) {
            $this->error('Database name is empty in configuration.');
            return 1;
        }

        $exportDir = storage_path('app/deployments/exports');
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $customOutput = $this->option('output');
        if ($customOutput) {
            $outputPath = $customOutput;
            if (!str_ends_with($outputPath, '.gz') && !str_ends_with($outputPath, '.sql')) {
                $outputPath .= '.sql.gz';
            }
        } else {
            $timestamp = date('Ymd_His');
            $outputPath = $exportDir . DIRECTORY_SEPARATOR . "db_export_{$database}_{$timestamp}.sql.gz";
        }

        $mysqldump = $this->findMysqldumpBinary();
        if (!$mysqldump) {
            $this->error('Could not find mysqldump binary in standard paths or PATH.');
            return 1;
        }

        $tempSqlFile = storage_path('app/deployments/exports/temp_' . uniqid() . '.sql');

        $command = [
            $mysqldump,
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--routines',
            '--triggers',
            $database,
            "--result-file={$tempSqlFile}",
        ];

        if (!empty($password)) {
            $command[] = "--password={$password}";
        }

        $this->info("Dumping schema and data using: {$mysqldump}...");
        $process = new Process($command);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($tempSqlFile) || filesize($tempSqlFile) === 0) {
            $this->error('Database dump failed: ' . $process->getErrorOutput());
            if (file_exists($tempSqlFile)) {
                @unlink($tempSqlFile);
            }
            return 1;
        }

        $this->info("Compressing SQL dump into: {$outputPath}...");
        $fpIn = fopen($tempSqlFile, 'rb');
        $fpOut = gzopen($outputPath, 'wb9');

        while (!feof($fpIn)) {
            gzwrite($fpOut, fread($fpIn, 1024 * 512));
        }

        fclose($fpIn);
        gzclose($fpOut);
        @unlink($tempSqlFile);

        $size = filesize($outputPath);
        $sizeFormatted = round($size / 1024 / 1024, 2) . ' MB';

        $this->info("Export completed successfully ({$sizeFormatted}).");
        $this->line("BUNDLE_PATH={$outputPath}");

        return 0;
    }

    protected function findMysqldumpBinary(): ?string
    {
        $candidates = [
            'G:/xampp/mysql/bin/mysqldump.exe',
            'C:/xampp/mysql/bin/mysqldump.exe',
            'mysqldump',
            'mariadb-dump',
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        $check = new Process(['where', 'mysqldump']);
        $check->run();
        if ($check->isSuccessful()) {
            $lines = array_filter(explode("\n", trim($check->getOutput())));
            if (!empty($lines)) {
                return trim($lines[0]);
            }
        }

        return null;
    }
}

