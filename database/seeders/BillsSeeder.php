<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\Visit;
use App\Models\BillPayment;

class BillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // $bills = Bill::factory()->has(BillPayment::factory()->count(1))->count(100)->make();
        //// dump($bills);
        $visits = Visit::get();
        dump($visits);
    }
}
