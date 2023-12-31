<?php

namespace App\Enums;

enum Department: int {
    case DOC = 1;
    case NUR = 2;
    case REC = 3;
    case PHA = 4;
    case LAB = 5;
    case IT = 6;
    case RAD = 7;
    case DIS = 8;
    case NHI = 9;

    public static function getIds() {
        return array_map(fn($department) => $department->value, self::cases());
    }
}
