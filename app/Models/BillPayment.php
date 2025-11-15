<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillPayment extends Model
{
    use SoftDeletes;

    protected $touches = ['bill'];

    protected $fillable = [
        'bill_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'status',
        'user_id',
    ];

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    protected static function booted()
    {
        static::saved(function (Self $payment) {
            if ($payment->bill->balance <= 0 && $payment->bill->status != Status::cancelled->value) {
                $payment->bill->update([
                    'status' => Status::completed->value,
                ]);
            }
        });
    }
}
