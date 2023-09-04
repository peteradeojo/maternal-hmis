<?php

namespace App\Enums;

enum Status: int {
    case active = 1;
    case closed = 0;
    case blocked = 2;
    case pending = 3;
    case completed = 4;
}
