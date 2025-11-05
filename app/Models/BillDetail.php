<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillDetail extends Model
{
    protected $fillable = [
        'bill_id', 'user_id', 'description', 'quantity', 'unit_price', 'total_price',
        'chargeable_type', 'chargeable_id', 'tag', 'meta', 'quoted_at', 'quoted_by',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function amount(): Attribute {
        return Attribute::make(
            get: fn ($v, $attributes) => $attributes['tag'] == 'drug' ? $attributes['total_price'] * 1.5 : $attributes['total_price'],
        );
    }
}
