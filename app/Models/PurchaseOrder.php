<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id',
        'po_number',
        'status'
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    static function generatePoNumber()
    {
        return "#PUR" . date('ymd-his');
    }

    public function lines() {
        return $this->hasMany(PurchaseOrderLine::class, 'po_id');
    }
}
