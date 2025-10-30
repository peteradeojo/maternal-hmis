<?php

namespace App\Livewire\Forms\Doctor;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PrescriptionRequest extends Form
{
    #[Validate('nullable|string')]
    public $dosage = '';

    #[Validate('nullable|string')]
    public $duration = '';

    #[Validate('nullable|string')]
    public $frequency = 'stat';

    // #[Validate('required|string')]
    // public $route = '';
}
