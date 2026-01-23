<?php

namespace App\Traits;

use App\Enums\Status;

trait CastsStatus {
    protected function initializeCastsStatus(): void {
        $this->mergeCasts([
            'status' => Status::class,
        ]);
    }

    public function scopeStatus($query, Status $status) {
        return $query->where('status', $status);
    }

    public function scopeActive($query) {
        return $query->status(Status::active);
    }

    public function scopePending($query) {
        return $query->status(Status::pending);
    }
}
