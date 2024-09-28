<?php

namespace App\Livewire\Forms\Doctor;

use Livewire\Attributes\Validate;
use Livewire\Form;

class AncBookingForm extends Form
{
    #[Validate('nullable|string')]
    public ?string $presentation;

    #[Validate('nullable|string')]
    public ?string $lie;

    #[Validate('nullable|string')]
    public ?string $fundal_height;

    #[Validate('nullable|string')]
    public ?string $fetal_heart_rate;

    #[Validate('nullable|string')]
    public ?string $presentation_relationship;

    #[Validate('nullable|string')]
    public ?string $gravida;

    #[Validate('nullable|string')]
    public ?string $parity;
}
