<?php

namespace App\Livewire\Forms\Doctor;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ExaminationForm extends Form
{
    #[Validate('required|string')]
    public ?string $exam;

    #[Validate('required|string')]
    public ?string $result;
}
