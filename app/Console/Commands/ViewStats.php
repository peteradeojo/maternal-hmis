<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ViewStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:view-stats {--list-options} {option?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $stats = [
        'nginx_uptime' => "awk -F'backend:' '{print $2}' /var/log/nginx/hmis_backend.log | awk -F' status:' '{print $1}' | sed -e 's/ : /\\n/g' | sort | uniq -c",
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list-options')) {
            $this->usage();
        }

        if ($option = $this->argument('option')) {
            return $this->__call($option, []);
        } else {
            $this->usage();
        }
    }

    public function usage() {
        $this->info("Usage: php artisan app:view-stats {option}\n\n");
        $this->info("Available stats:");
        $this->info(implode("\n", array_keys($this->stats)));
        exit(0);
    }

    public function nginx_uptime() {}

    public function __call($method, $parameters)
    {
        if (in_array($method, array_keys($this->stats))) {
            $cmd = $this->stats[$method];
            $output = [];
            $output = shell_exec($cmd);
            $this->info($output);
            return;
        }

        $this->error("Error: requested stats do not exist.");
        exit(1);
    }
}
