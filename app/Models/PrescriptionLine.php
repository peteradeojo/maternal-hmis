<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class PrescriptionLine extends Model
{
    protected $fillable = [
        'item_id',
        'dosage',
        'frequency',
        'duration',
        'status',
        'dispensed_by',
        'prescribed_by',
        'profile',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    protected $with = ['item'];

    public function prescription() {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function dispensed_by() {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function item() {
        return $this->belongsTo(StockItem::class, 'item_id');
    }

    public function __toString()
    {
        return "{$this->item->name} {$this->dosage} {$this->frequency} - for {$this->duration} " . (is_numeric($this->duration) ? 'day(s)' : '');
    }
}
