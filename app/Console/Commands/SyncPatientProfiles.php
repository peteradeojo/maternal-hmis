<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SyncPatientProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-patients';

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
        return;
        $oldDb = DB::connection('old');
        $newDb = DB::connection('mysql');

        $newCategories = $newDb->table('patient_categories')->pluck('id', 'name')->toArray();

        $data = $oldDb->table('patients', 'p')->leftJoin('patient_categories', 'p.category_id', '=', DB::raw('patient_categories.id'))->selectRaw('p.*, patient_categories.name as category_name');

        $newDb->beginTransaction();

        try {
            $data->chunkById(
                column: "p.id",
                alias: "id",
                count: 100,
                callback: function (
                    $patients,
                    $index
                ) use ($newCategories, &$newDb) {
                    $patients->transform(function (
                        $patient,
                        $key
                    ) use ($newCategories) {
                        $data = [
                            'id' => $patient->id,
                            'name' => trim("{$patient->last_name} {$patient->first_name} $patient->middle_name"),
                            'category_id' => $newCategories[$patient->category_name] ?? $newCategories["Adult"],
                            'gender' => $patient->gender,
                            'religion' => $patient->religion,
                            'tribe' => $patient->tribe,
                            'marital_status' => $patient->marital_status,
                            'occupation' => $patient->occupation,
                            'dob' => $patient->dob,
                            'phone' => $patient->phone,
                            'card_number' => $patient->card_number,
                            'email' => $patient->email,
                            'created_at' => $patient->created_at,
                        ];
                        return $data;
                    });

                    $patients = ($patients->map(fn ($patient) => [...$patient, 'id' => null])); //->toArray()));

                    $newDb->table('patients')->insert($patients->toArray());
                    DB::commit();
                }
            );
        } catch (\Throwable $th) {
            //throw $th;
            dump($th->getMessage());
            $newDb->rollBack();
        }
    }
}
