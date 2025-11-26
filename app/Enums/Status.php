<?php

namespace App\Enums;

enum Status: int
{
    case closed = 0;
    case active = 1;
    case blocked = 2;
    case pending = 3;
    case completed = 4;
    case ejected = 5;
    case quoted = 6;
    case PAID = 7;
    case UNPAID = 8;
    case cancelled = 9;

    case Unavailable = 10;
}
