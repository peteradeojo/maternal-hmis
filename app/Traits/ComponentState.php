<?php

namespace App\Traits;

trait ComponentState {
    public $initHash;
    public $currentHash;

    public function getHash() {
        return md5(json_encode($this->getHashData()));
    }

    public function updateHash() {
        $this->currentHash = $this->getHash();
    }

    public function resetHash() {
        $this->initHash = $this->currentHash = $this->getHash();
        $this->dispatch('$refresh');
    }

    abstract public function getHashData(): mixed;
}
