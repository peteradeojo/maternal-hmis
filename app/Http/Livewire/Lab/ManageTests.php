<?php

namespace App\Http\Livewire\Lab;

use App\Enums\Status;
use App\Interfaces\OperationalEvent;
use App\Models\Product;
use App\Models\Visit;
use Livewire\Component;

class ManageTests extends Component
{
    public OperationalEvent $visit;

    public $tests;

    public $orderedTests;

    public function mount(OperationalEvent $visit)
    {
        $this->visit = $visit;
        $this->getTests();
    }

    public function render()
    {
        return view('livewire.lab.manage-tests');
    }

    public function addTest($data)
    {
        $this->visit->tests()->create([
            'name' => $data['name'],
            'status' => Status::pending->value,
            'patient_id' => $this->visit->patient_id,
            'results' => [],
            'describable_type' => Product::class,
            'describable_id' => $data['id'],
        ]);

        $this->dispatch('close-order');
        $this->getTests();
        $this->dispatch('$refresh');
    }

    private function getTests()
    {
        $this->tests = $this->visit->tests
            ->merge($this->visit->visit->tests)
            ->merge($this->visit->admission?->plan->tests ?? [])
            ->merge($this->visit->plan?->tests ?? [])
            ->merge($this->visit->admission?->tests ?? []);
    }
}
