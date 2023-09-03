<?php

namespace App\Enums;

enum AncCategory: int {
    case Bronze = 1;
    case Silver = 2;
    case Gold = 3;
    case Diamon = 4;
    case Platinum = 5;

    public static function getValues() {
        $values = [];

        foreach(self::cases() as $case) {
            $values[] = $case->value;
        }

        return $values;
    }
}
