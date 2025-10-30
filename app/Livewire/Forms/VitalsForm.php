<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class VitalsForm extends Form
{
    #[Validate('nullable|numeric')]
    public $temperature;

    #[Validate('nullable|numeric')]
    public $weight;

    #[Validate('nullable|string')]
    public $blood_pressure;

    #[Validate('nullable|numeric')]
    public $pulse;

    #[Validate('nullable|numeric')]
    public $respiration;

    #[Validate('date')]
    public $recorded_date;
}
