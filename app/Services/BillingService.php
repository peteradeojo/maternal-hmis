<?php

namespace App\Services;

use App\Enums\Status;
use App\Interfaces\OperationalEvent;
use App\Models\Visit;
use App\Services\TreatmentService;

class BillingService
{
    public function __construct() {}

    public function getVisitBill(Visit $visit)
    {
        $bill = [
            'consultation' => $this->getBillables($visit) + $this->getBillables($visit->visit),
            'admission' => $this->getBillables($visit->admission) + $this->getBillables($visit->admission?->plan),
        ];

        return $bill;
    }

    public function getBillables(?OperationalEvent $evt)
    {
        if (empty($evt)) return;

        $drugs = $evt->prescription?->lines ?? collect([]);
        $tests = $evt->valid_tests;
        $scans = $evt->radios;

        $drugs = $drugs->map(function ($line) {
            $dispensed = $line->dispensed();
            $qty = $line->qty_dispensed ?? TreatmentService::getCount($line->item, $line);
            $unit_price = (TreatmentService::getPrice($line->item_id, $line->profile ?? 'RETAIL'));

            return [
                'saved' => true,
                'description' => (string) $line,
                'quantity' => $qty,
                'unit_price' => $unit_price,
                'total_amt' => ($dispensed + $qty) * $unit_price,
                'status' => $line->status,
            ];
        })->toArray();

        $tests = $tests->map(fn($test) => [
            'saved' => true,
            'description' => (string) $test,
            'quantity' => 1,
            'unit_price' => $test->describable->amount,
            'total_amt' => $test->describable->amount,
            'status' => $test->status,
            // 'product' => $test->describable->toArray(),
            // 'data' => $test->toArray()
        ])->toArray();

        // $scans = $evt?->imagings?->load('describable')->map(fn($item) => [
        $scans = $scans->load('describable')->map(fn($item) => [
            'saved' => true,
            'description' => (string) $item,
            'quantity' => 1,
            'unit_price' => $item->describable->amount,
            'total_amt' => $item->describable->amount,
            'status' => $item->status,
            // 'product' => $item->describable->toArray(),
            // 'data' => $item->toArray(),
        ])->toArray();

        return [
            'phm' => ['items' => $drugs, 'total' => array_reduce($drugs, fn($a, $b) => $a + $b['total_amt'], 0)],
            'radio' => ['items' => $scans, 'total' => array_reduce($scans, fn($a, $b) => $a + $b['total_amt'], 0)],
            'lab' => ['items' => $tests, 'total' => array_reduce($tests, fn($a, $b) => $a + $b['total_amt'], 0)],
            'other' => [
                ($evt instanceof Visit) && !empty($evt->consultant_id) ? [
                    'saved' => true,
                    'description' => 'Consultation fee',
                    'quantity' => null,
                    'unit_price' => null,
                    'total_amt' => 10000,
                    'status' => Status::active,
                ] : null,
            ],
        ];
    }
}
