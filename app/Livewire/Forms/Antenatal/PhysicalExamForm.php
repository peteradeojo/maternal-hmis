<?php

namespace App\Livewire\Forms\Antenatal;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PhysicalExamForm extends Form
{
    #[Validate('nullable|string')]
    public $oedema;

    #[Validate('nullable|string')]
    public $anemia;

    #[Validate('nullable|string')]
    public $respiratory;

    #[Validate('nullable|string')]
    public $cardio;

    #[Validate('nullable|string')]
    public $vaginal;

    #[Validate('nullable|string')]
    public $other;

    #[Validate('nullable|string')]
    public $spleen;

    #[Validate('nullable|string')]
    public $liver;

    #[Validate('nullable|string')]
    public $comment;
}
