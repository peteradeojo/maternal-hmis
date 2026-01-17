<?php

namespace App\Traits;

use App\Enums\Status;

trait CastsStatus {
    protected function initializeCastsStatus(): void {
        $this->mergeCasts([
            'status' => Status::class,
        ]);
    }
}
