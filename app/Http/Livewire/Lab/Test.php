<?php

namespace App\Http\Livewire\Lab;

use App\Enums\Status;
use App\Traits\ComponentState;
use Livewire\Component;

class Test extends Component
{
    use ComponentState;

    public $test;

    public $results = [];
    public $status;

    public function mount($test)
    {
        $this->test = $test;
        $this->status = $test->status;
        $this->results = $test->results;

        $this->initHash = $this->currentHash = $this->getHash();
    }

    public function render()
    {
        return view('livewire.lab.test');
    }

    public function addResult()
    {
        $this->results[] = (object)[
            'description' => '',
            'result' => '',
            'unit' => '',
            'reference_range' => '',
        ];

        $this->currentHash = $this->getHash();
        $this->dispatch('$refresh');
    }

    public function removeResult($i)
    {
        $part1 = array_slice($this->results, 0, $i);
        $part2 = array_slice($this->results, $i + 1, null);
        $this->results = array_values($part1 + $part2);
        $this->currentHash = $this->getHash();

        $this->dispatch('$refresh');
    }

    public function save() {
        $this->test->status = $this->status;
        $this->test->results = $this->results;
        $this->test->save();

        $this->results = $this->test->results;
        $this->resetHash();
    }

    public function getHashData(): mixed
    {
        return [$this->results, $this->status];
    }
}
