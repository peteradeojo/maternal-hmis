<?php

namespace App\Enums;

enum Gender: int {
    case Female = 0;
    case Male = 1;

    public static function getValues() {
        $values = [];

        foreach(self::cases() as $case) {
            $values[] = $case->value;
        }

        return $values;
    }
}
