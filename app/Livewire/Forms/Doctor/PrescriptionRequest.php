<?php

namespace App\Livewire\Forms\Doctor;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PrescriptionRequest extends Form
{
    #[Validate('required|string')]
    public $dosage = '';

    #[Validate('required|integer')]
    public $duration = '';

    #[Validate('required|string')]
    public $frequency = 'stat';

    // #[Validate('required|string')]
    // public $route = '';
}
