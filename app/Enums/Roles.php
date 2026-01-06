<?php

namespace App\Enums;

enum Roles: string
{
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Nurse = 'nurse';
    case Record = 'record';
    case Pharmacy = 'pharmacy';
    case Lab = 'lab';
    case Radiology = 'radiology';
    case Billing = 'billing';
    case Media = 'media';
    case Insurance = 'insurance';
    case Support = 'support';
    case Finance = 'finance';
}
