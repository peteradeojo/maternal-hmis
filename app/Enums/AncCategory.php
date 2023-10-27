<?php

namespace App\Enums;

enum AncCategory: int {
    case Bronze = 1;
    case Silver = 2;
    case Gold = 3;
    case Diamond = 4;
    case Platinum = 5;
    case Limited = 6;
    case Gold_Plus = 7;
    case Diamond_Plus = 8;


    public static function getValues() {
        $values = [];

        foreach(self::cases() as $case) {
            $values[] = $case->value;
        }

        return $values;
    }
}
