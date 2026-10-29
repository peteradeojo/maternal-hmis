<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

class RunDbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run application backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = config('database.connections.pgsql');

        $backupDir = storage_path('app/backups');
        $remoteFileName = date('Y-m-d_H-i-s') . '.sql';
        $filename = $backupDir . '/' . $remoteFileName;

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $process = Process::fromShellCommandline(sprintf(
            'PGPASSWORD="%s" pg_dump -U %s -h %s -p %s -d %s -F c -b -v -f %s',
            $config['password'],
            $config['username'],
            $config['host'],
            $config['port'],
            $config['database'],
            $filename,
        ));

        try {
            $this->info('Starting Postgresql backup');
            $process->mustRun();
            $this->info("Backup generated successfully! Saved to {$filename}");

            $name = Storage::disk('google')->put($remoteFileName, File::get($filename));
            // Gdrive::put($remoteFileName, $filename);

            dump($name);
            $this->info("Backup uploaded successfully! Saved to {$filename}");


            return self::SUCCESS;
        } catch (ProcessFailedException $e) {
            $this->error("Backup failed. " . $e->getMessage());
            logger()->error('PostgreSQL Backup Failed', ['exception' => $e->getMessage()]);

            return self::FAILURE;
        }
    }
}
