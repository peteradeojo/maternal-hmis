<?php

namespace App\Models;

use App\Enums\Status;
use App\Interfaces\PatientRecord;
use App\Services\TreatmentService;
use App\Traits\NeedsRecorderInfo;
use Illuminate\Database\Eloquent\Model;

class PrescriptionLine extends Model implements PatientRecord
{
    use NeedsRecorderInfo;

    protected $fillable = [
        'item_id',
        'dosage',
        'frequency',
        'duration',
        'status',
        'dispensed_by',
        'prescribed_by',
        'profile',
        'description',
        'qty_dispensed',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    protected $with = ['item', 'recorder'];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function dispensed_by()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'item_id');
    }

    public function dispenses() {
        return $this->morphMany(DispenseLine::class, 'source');
    }

    public function __toString()
    {
        return ($this->item?->name ?? $this->description) . " {$this->dosage} {$this->frequency} - for {$this->duration} " . (is_numeric($this->duration) ? 'day(s)' : '');
    }

    public function getDispensingReport()
    {
        $qty = $this->qty_dispensed ?? TreatmentService::getCount($this->item->toArray(), $this->toArray());
        return [
            'id' => $this->id,
            'description' => (string) $this,
            'unit' => $this->item->base_unit,
            'price' => TreatmentService::getPrice($this->item_id, $this->profile),
            'sku' => $this->item->sku,
            'item_id' => $this->item_id,
            'qty_on_hand' => $this->item->balance,
            'quantity' => $qty,
            'status' => $this->status,
            'left' => $this->item->balance - $qty,
        ];
    }

    public function dispensed()
    {
        return $this->dispenses->sum('qty_dispensed');
    }
}
