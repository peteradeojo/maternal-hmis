<?php

namespace App\Console\Commands;

use App\Models\IcdDiagnosis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LoadICD10List extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-icd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = base_path("icd10.csv");

        $fh = fopen($filename, "r");
        if (!$fh) return 0;

        $data = [];
        while (($row = fgetcsv($fh))) {
            $data[] = [
                'code' => $row[1],
                'short_description' => trim($row[2]),
                'long_description' => trim($row[3]),
            ];
        }
        $data = collect($data);
        $time = now();

        $data->chunk(200, false)->each(function ($c) use (&$time) {
            DB::beginTransaction();
            try {
                IcdDiagnosis::insert($c->toArray());

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
            }
        });
    }
}
