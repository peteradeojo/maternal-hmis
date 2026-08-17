<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send:error {message}', function () {
    laas()->emergency($this->argument('message'), [
        'source' => 'pull.sh',
    ]);
});

Artisan::command('rebuild-inventory', function () {
    DB::statement("SELECT rebuild_inventory_balances()");
});

Artisan::command("parse-nhis-portals", function () {
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $reader->setReadDataOnly(true);
    $sheet = $reader->load("storage/app/nhis-portals.xlsx");

    $wSheet = $sheet->getActiveSheet();
    $highestRow = $wSheet->getHighestDataRow();

    $data = [];
    for ($row = 2; $row <= $highestRow; ++$row) {
        $data[] = [
            'name' => $wSheet->getCell('C' . $row)->getValue(),
            'class' => $wSheet->getCell('B' . $row)->getValue(),
            'website' => $wSheet->getCell('D' . $row)->getValue(),
            'phone' => $wSheet->getCell('E' . $row)->getValue(),
            'email' => $wSheet->getCell('F' . $row)->getValue(),
        ];
    }

    // echo json_encode($data);

    $fh = fopen("storage/app/public/nhis-portals.json", "w");
    fwrite($fh, json_encode($data));
    fclose($fh);
});
