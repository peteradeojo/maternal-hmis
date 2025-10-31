<?php

namespace App\Enums;

use App\Models\Admission;
use App\Models\AdmissionPlan;
use App\Models\AncVisit;
use App\Models\GeneralVisit;
use App\Models\Visit;

enum EventLookup: string
{
    case visit = Visit::class;
    case admission = Admission::class;
    case admission_plan = AdmissionPlan::class;
    case antenatal = AncVisit::class;
    case general_visit = GeneralVisit::class;

    public static function fromName(string $name): self
    {
        $case = self::tryFromName($name);
        if ($case === null) {
            throw new \InvalidArgumentException("No enum case found for name: {$name}");
        }
        return $case;
    }

    private static function tryFromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null;
    }
}
