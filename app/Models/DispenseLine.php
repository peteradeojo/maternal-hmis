<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class DispenseLine extends Model
{
    protected $fillable = [
        'source_type',
        'source_id',
        'qty_dispensed',
        'user_id',
    ];

    public function source()
    {
        $this->morphTo();
    }

    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }

    public function quantity(): Attribute
    {
        return Attribute::make(
            get: fn($v, $attrs) => $attrs['qty_dispensed'],
        );
    }

    public function stock_transaction() {
        
    }
}
