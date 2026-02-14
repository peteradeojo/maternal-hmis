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
    protected $signature = 'app:view-stats {--list-options} {option?} {options?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $stats = [
        'nginx_uptime' => "awk -F'backend:' '{print $2}' /var/log/nginx/hmis_backend.log | awk -F' status:' '{print $1}' | sed -e 's/ : /\\n/g' | sort | uniq -c",
        'uptime' => 'w',
        // 'storage' => 'df -hT -t tmpfs -t ext4 -t vfat',
        'storage' => null,
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

            if (empty($cmd)) {
                if (!method_exists($this, $method)) $this->fail("Method not exists.");
                return $this->{$method}($this->argument('options'), $parameters);
            }

            $output = [];
            $output = shell_exec($cmd);
            $this->info($output);
            return;
        }

        $this->error("Error: requested stats do not exist.");
        exit(1);
    }

    protected function storage($options) {
        function parseDiskStorage($line) {
            return [$name, $type, $size, $used, $available, $use_percent, $mount] = array_values(array_filter(explode(" ", $line)));
        }

        $output = shell_exec("df -hT -t ext4 -t vfat -t tmpfs");
        $output = array_filter(explode("\n", $output));
        array_shift($output);

        if (@$options[0] == 'json') {
            $data = [];
            foreach ($output as $l) {
                $d = parseDiskStorage($l);
                $data[] = [
                    'fs' => $d[0],
                    'type' => $d[1],
                    'size' => $d[2],
                    'used' => $d[3],
                    'avail' => $d[4],
                    'use%' => $d[5],
                    'mount' => $d[6],
                ];
            }
            $this->info(json_encode($data));
            return;
        }

        foreach($output as $line) {
            [$name, $type, $size, $used, $available, $use_percent, $mount] = parseDiskStorage($line);
            echo "Disk: $name ($type)\nUsed: $used/$size ($use_percent)\nAvailable: $available/$size\nMounted on: $mount\n\n";
        }
    }
}
