<?php

namespace App\Livewire\Forms\Doctor;

use Livewire\Attributes\Validate;
use Livewire\Form;

class AncFollowup extends Form
{
    #[Validate('nullable|string')]
    public string $fundal_height;

    #[Validate('nullable|string')]
    public string $presentation;

    #[Validate('nullable|string')]
    public string $presentation_relationship;

    #[Validate('nullable|string')]
    public string $note;

    #[Validate('nullable|string')]
    public string $lie;
}
