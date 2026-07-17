<?php

namespace App\Interfaces;

use App\Models\Visit;

interface Billable
{
    public function getVisit(): \App\Models\Visit;
    public function getChargeFor(Visit $visit);
}
