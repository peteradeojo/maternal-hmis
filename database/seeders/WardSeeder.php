<?php

namespace Database\Seeders;

use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wards = [
            [
                'name' => 'PEACE',
                'beds' => 1,
                'type' => 'private',
            ],
            [
                'name' => 'JOY',
                'beds' => 1,
                'type' => 'private',
            ],
            [
                'name' => 'LOVE',
                'beds' => 1,
                'type' => 'private',
            ],
            [
                'name' => 'SCBU',
                'beds' => 2,
                'type' => 'public',
            ],
            [
                'name' => 'FAITH',
                'beds' => 1,
            ],
        ];

        foreach ($wards as $w) {
            Ward::updateOrCreate(['name' => $w['name']], $w);
        }
    }
}
