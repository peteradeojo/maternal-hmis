<?php

namespace App\Livewire\Forms\Doctor;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ObsData extends Form
{
    #[Validate('nullable|string')]
    public ?string $gravida;

    #[Validate('nullable|string')]
    public ?string $parity;

    #[Validate('nullable|string')]
    public ?string $risk_assessment;
}
