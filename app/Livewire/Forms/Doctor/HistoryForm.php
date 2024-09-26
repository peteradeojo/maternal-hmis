<?php

namespace App\Livewire\Forms\Doctor;

use Livewire\Attributes\Validate;
use Livewire\Form;

class HistoryForm extends Form
{
    #[Validate('required|string')]
    public string $presentation = "";

    #[Validate('required|string')]
    public string $duration = "";
}
