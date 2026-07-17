<?php

namespace App\Interfaces;

use App\Models\Visit;

interface ShouldBeBillable
{
    public function getVisit(): Visit;
    public function getEvent(): OperationalEvent;
    public function getChargeFor(OperationalEvent $evt): array;
}
