<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ImportDatabaseForDeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import-for-deploy {path : Path to SQL or .sql.gz dump file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports an SQL or .sql.gz dump into the active database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('path');

        if (!file_exists($filePath)) {
            $this->error("Target dump file not found: {$filePath}");
            return 1;
        }

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

        $mysqlBinary = $this->findMysqlBinary();
        if (!$mysqlBinary) {
            $this->error('Could not find mysql or mariadb client binary.');
            return 1;
        }

        $this->info("Importing dump [{$filePath}] into [{$database}] on [{$host}:{$port}]...");

        $tempSqlFile = null;
        if (str_ends_with(strtolower($filePath), '.gz')) {
            $this->info('Decompressing .gz archive for import...');
            $tempSqlFile = storage_path('app/deployments/imports/temp_' . uniqid() . '.sql');
            $importDir = dirname($tempSqlFile);
            if (!is_dir($importDir)) {
                mkdir($importDir, 0755, true);
            }

            $sfp = gzopen($filePath, 'rb');
            $dfp = fopen($tempSqlFile, 'wb');
            while (!gzeof($sfp)) {
                fwrite($dfp, gzread($sfp, 1024 * 512));
            }
            fclose($dfp);
            gzclose($sfp);

            $sourcePath = $tempSqlFile;
        } else {
            $sourcePath = $filePath;
        }

        $cmdStr = sprintf(
            '"%s" --host=%s --port=%s --user=%s %s %s < "%s"',
            $mysqlBinary,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            !empty($password) ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            $sourcePath
        );

        $process = Process::fromShellCommandline($cmdStr);
        $process->setTimeout(600);
        $process->run();

        if ($tempSqlFile && file_exists($tempSqlFile)) {
            @unlink($tempSqlFile);
        }

        if (!$process->isSuccessful()) {
            $this->error('Database import failed: ' . $process->getErrorOutput());
            return 1;
        }

        $this->info('Database imported successfully!');
        return 0;
    }

    protected function findMysqlBinary(): ?string
    {
        $candidates = [
            'G:/xampp/mysql/bin/mysql.exe',
            'C:/xampp/mysql/bin/mysql.exe',
            'mysql',
            'mariadb',
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        $check = new Process(['where', 'mysql']);
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

